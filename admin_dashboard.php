<?php
session_start();
include 'config.php';

// 1. Security Kick-out: If not Isam or Motau, send them away
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// 2. Handle DELETE Customer
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM tbluser WHERE user_id = $del_id");
    header("Location: admin_dashboard.php");
    exit();
}

// 3. Handle ADD Customer
if (isset($_POST['add_user'])) {
    $u = $_POST['username'];
    $e = $_POST['email'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO tbluser (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $u, $e, $p);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// 4. Handle UPDATE Customer
if (isset($_POST['update_user'])) {
    $uid = $_POST['user_id'];
    $new_u = $_POST['username'];
    $new_e = $_POST['email'];
    
    $stmt = $conn->prepare("UPDATE tbluser SET username=?, email=? WHERE user_id=?");
    $stmt->bind_param("ssi", $new_u, $new_e, $uid);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// 5. Handle APPROVE Customer
if (isset($_GET['approve'])) {
    $approve_id = intval($_GET['approve']);
    $conn->query("UPDATE tbluser SET is_verified = 1 WHERE user_id = $approve_id");
    header("Location: admin_dashboard.php");
    exit();
}

// 6. Handle DELETE Product
if (isset($_GET['delete_product'])) {
    $del_product_id = intval($_GET['delete_product']);
    $conn->query("DELETE FROM tblproduct WHERE product_id = $del_product_id");
    header("Location: admin_dashboard.php");
    exit();
}

// 7. Handle ADD Product
if (isset($_POST['add_product'])) {
    $item_name = $_POST['item_name'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $condition = $_POST['condition'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image_path = $_POST['image_path'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO tblproduct (item_name, brand, category, size, condition, price, description, image_path, status, seller_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'approved', 1)");
    $stmt->bind_param("sssssdsss", $item_name, $brand, $category, $size, $condition, $price, $description, $image_path);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}

// 8. Handle UPDATE Product
if (isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $item_name = $_POST['item_name'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $condition = $_POST['condition'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    $stmt = $conn->prepare("UPDATE tblproduct SET item_name=?, brand=?, category=?, size=?, condition=?, price=?, description=? WHERE product_id=?");
    $stmt->bind_param("sssssdsi", $item_name, $brand, $category, $size, $condition, $price, $description, $product_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}

// 9. Handle APPROVE Product (mark as approved)
if (isset($_GET['approve_product'])) {
    $approve_product_id = intval($_GET['approve_product']);
    $conn->query("UPDATE tblproduct SET status = 'approved' WHERE product_id = $approve_product_id");
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard | Pastimes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: black; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; background-color: black; border: 1px solid black; cursor: pointer; }
        .btn-red { background-color: red; }
        .btn-green { background-color: green; }
        .panel { border: 2px solid black; padding: 15px; margin-top: 20px; max-width: 400px; background: #eee;}
    </style>
</head>
<body>

    <h1>Welcome, <?php echo $_SESSION['admin_name']; ?> (Admin)</h1>
    <a href="admin_login.php" style="color:red;">[ Logout Admin ]</a>
    <a href="admin_messages.php" style="color:green; margin-left: 20px;">[ Order Communications ]</a>
    <hr>

    <!-- SECTION: VIEW & DELETE CUSTOMERS -->
    <h2>Customer Database</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email Address</th>
            <th>Account Status</th> <!-- NEW HEADER -->
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM tbluser");
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Check verification status
                $status_text = ($row['is_verified'] == 1) ? "<span style='color:green; font-weight:bold;'>Verified</span>" : "<span style='color:orange; font-weight:bold;'>Pending</span>";
                
                echo "<tr>";
                echo "<td>" . $row['user_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . $status_text . "</td>"; // DISPLAY STATUS
                echo "<td>";
                
                // Show the Approve button ONLY if they are still pending
                if ($row['is_verified'] == 0) {
                    echo "<a href='admin_dashboard.php?approve=" . $row['user_id'] . "' class='btn btn-green' style='margin-right: 5px;'>Approve</a>";
                }
                
                echo "<a href='admin_dashboard.php?edit=" . $row['user_id'] . "' class='btn' style='background-color: blue; margin-right: 5px;'>Edit</a>
                      <a href='admin_dashboard.php?delete=" . $row['user_id'] . "' class='btn btn-red' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No customers found.</td></tr>";
        }
        ?>
    </table>

    <!-- SECTION: ADD NEW CUSTOMER -->
    <div class="panel">
        <h3>Add New Customer</h3>
        <form action="admin_dashboard.php" method="POST">
            <input type="text" name="username" placeholder="Username" required style="width:100%; margin-bottom:5px;"><br>
            <input type="email" name="email" placeholder="Email" required style="width:100%; margin-bottom:5px;"><br>
            <input type="password" name="password" placeholder="Password" required style="width:100%; margin-bottom:5px;"><br>
            <button type="submit" name="add_user" class="btn btn-green">Add User</button>
        </form>
    </div>

    <!-- SECTION: UPDATE CUSTOMER -->
    <?php 
    if(isset($_GET['edit'])) {
        $edit_id = $_GET['edit'];
        $edit_res = $conn->query("SELECT * FROM tbluser WHERE user_id = $edit_id");
        if($edit_user = $edit_res->fetch_assoc()) {
    ?>
        <div class="panel" style="border-color: blue;">
            <h3>Update Customer (ID: <?php echo $edit_user['user_id']; ?>)</h3>
            <form action="admin_dashboard.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $edit_user['user_id']; ?>">
                <input type="text" name="username" value="<?php echo $edit_user['username']; ?>" required style="width:100%; margin-bottom:5px;"><br>
                <input type="email" name="email" value="<?php echo $edit_user['email']; ?>" required style="width:100%; margin-bottom:5px;"><br>
                <button type="submit" name="update_user" class="btn" style="background-color: blue;">Save Changes</button>
                <a href="admin_dashboard.php" class="btn btn-red">Cancel</a>
            </form>
        </div>
    <?php 
        }
    } 
    ?>

    <hr style="margin-top: 40px;">

    <!-- SECTION: VIEW & DELETE CLOTHING ITEMS -->
    <h2>Clothing Inventory</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Size</th>
            <th>Price</th>
            <th>Condition</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        $product_result = $conn->query("SELECT * FROM tblproduct ORDER BY upload_date DESC");
        if ($product_result && $product_result->num_rows > 0) {
            while($product_row = $product_result->fetch_assoc()) {
                $status_badge = ($product_row['status'] == 'approved') ? "<span style='color:green; font-weight:bold;'>Approved</span>" : "<span style='color:orange; font-weight:bold;'>" . ucfirst($product_row['status']) . "</span>";
                
                echo "<tr>";
                echo "<td>" . $product_row['product_id'] . "</td>";
                echo "<td>" . htmlspecialchars($product_row['item_name']) . "</td>";
                echo "<td>" . htmlspecialchars($product_row['brand']) . "</td>";
                echo "<td>" . htmlspecialchars($product_row['category']) . "</td>";
                echo "<td>" . htmlspecialchars($product_row['size']) . "</td>";
                echo "<td>R" . number_format($product_row['price'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($product_row['condition']) . "</td>";
                echo "<td>" . $status_badge . "</td>";
                echo "<td>";
                
                // Show the Approve button ONLY if status is pending
                if ($product_row['status'] == 'pending') {
                    echo "<a href='admin_dashboard.php?approve_product=" . $product_row['product_id'] . "' class='btn btn-green' style='margin-right: 5px;'>Approve</a>";
                }
                
                echo "<a href='admin_dashboard.php?edit_product=" . $product_row['product_id'] . "' class='btn' style='background-color: blue; margin-right: 5px;'>Edit</a>
                      <a href='admin_dashboard.php?delete_product=" . $product_row['product_id'] . "' class='btn btn-red' onclick=\"return confirm('Are you sure you want to delete this product?');\">Delete</a>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No products found.</td></tr>";
        }
        ?>
    </table>

    <!-- SECTION: ADD NEW PRODUCT -->
    <div class="panel">
        <h3>Add New Product</h3>
        <form action="admin_dashboard.php" method="POST">
            <input type="text" name="item_name" placeholder="Item Name" required style="width:100%; margin-bottom:5px;"><br>
            <input type="text" name="brand" placeholder="Brand" required style="width:100%; margin-bottom:5px;"><br>
            <input type="text" name="category" placeholder="Category (tops, bottoms, etc.)" required style="width:100%; margin-bottom:5px;"><br>
            <input type="text" name="size" placeholder="Size" required style="width:100%; margin-bottom:5px;"><br>
            <select name="condition" required style="width:100%; margin-bottom:5px;">
                <option value="">-- Select Condition --</option>
                <option value="New">New</option>
                <option value="Like New">Like New</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
            </select><br>
            <input type="number" name="price" step="0.01" placeholder="Price (R)" required style="width:100%; margin-bottom:5px;"><br>
            <textarea name="description" placeholder="Description" required style="width:100%; margin-bottom:5px; height: 80px;"></textarea><br>
            <input type="text" name="image_path" placeholder="Image Path (e.g., uploads/image.jpg)" style="width:100%; margin-bottom:5px;"><br>
            <button type="submit" name="add_product" class="btn btn-green">Add Product</button>
        </form>
    </div>

    <!-- SECTION: UPDATE PRODUCT -->
    <?php 
    if(isset($_GET['edit_product'])) {
        $edit_product_id = intval($_GET['edit_product']);
        $edit_product_res = $conn->query("SELECT * FROM tblproduct WHERE product_id = $edit_product_id");
        if($edit_product = $edit_product_res->fetch_assoc()) {
    ?>
        <div class="panel" style="border-color: blue;">
            <h3>Update Product (ID: <?php echo $edit_product['product_id']; ?>)</h3>
            <form action="admin_dashboard.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
                <input type="text" name="item_name" value="<?php echo htmlspecialchars($edit_product['item_name']); ?>" required style="width:100%; margin-bottom:5px;"><br>
                <input type="text" name="brand" value="<?php echo htmlspecialchars($edit_product['brand']); ?>" required style="width:100%; margin-bottom:5px;"><br>
                <input type="text" name="category" value="<?php echo htmlspecialchars($edit_product['category']); ?>" required style="width:100%; margin-bottom:5px;"><br>
                <input type="text" name="size" value="<?php echo htmlspecialchars($edit_product['size']); ?>" required style="width:100%; margin-bottom:5px;"><br>
                <select name="condition" required style="width:100%; margin-bottom:5px;">
                    <option value="New" <?php echo ($edit_product['condition'] == 'New') ? 'selected' : ''; ?>>New</option>
                    <option value="Like New" <?php echo ($edit_product['condition'] == 'Like New') ? 'selected' : ''; ?>>Like New</option>
                    <option value="Good" <?php echo ($edit_product['condition'] == 'Good') ? 'selected' : ''; ?>>Good</option>
                    <option value="Fair" <?php echo ($edit_product['condition'] == 'Fair') ? 'selected' : ''; ?>>Fair</option>
                </select><br>
                <input type="number" name="price" step="0.01" value="<?php echo $edit_product['price']; ?>" required style="width:100%; margin-bottom:5px;"><br>
                <textarea name="description" required style="width:100%; margin-bottom:5px; height: 80px;"><?php echo htmlspecialchars($edit_product['description']); ?></textarea><br>
                <button type="submit" name="update_product" class="btn" style="background-color: blue;">Save Changes</button>
                <a href="admin_dashboard.php" class="btn btn-red">Cancel</a>
            </form>
        </div>
    <?php 
        }
    } 
    ?>

</body>
</html>
