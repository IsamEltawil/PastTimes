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

</body>
</html>