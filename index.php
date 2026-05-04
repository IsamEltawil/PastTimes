<?php 
include_once 'config.php'; 
include 'header.php'; 
?>

<!-- Success Message Logic -->
<?php if (isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; text-align: center; margin: 20px; border-radius: 5px; font-weight: bold;">
        Item successfully listed on Pastimes! Check the shop section below.
    </div>
<?php endif; ?>

<main id="home">
    <!-- Hero Section -->
    <div class="hero">
        <h1>Buy & Sell Timeless Fashion</h1>
        <p>A curated marketplace for premium second-hand clothing.</p>
        <br>
        <a href="#shop" class="btn">Shop Collection</a>
        <a href="#sell" class="btn btn-outline">Start Selling</a>
    </div>

    <div class="container">
        <h2>Featured Arrivals</h2>
        <hr>
        
        <div class="grid">
            <?php
            // Fetch only 4 approved products for the homepage
            $sql = "SELECT * FROM tblproduct WHERE status = 'approved' ORDER BY upload_date DESC LIMIT 4";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="product-card">
                        <div class="product-img-wrapper">
                            <!-- Condition Badge based on database value -->
                            <span class="badge"><?php echo strtoupper($row['condition']); ?></span>
                            
                            <!-- Displaying the actual uploaded image -->
                            <img src="<?php echo $row['image_path']; ?>" alt="Product" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div class="product-brand"><?php echo strtoupper($row['brand']); ?></div>
                        <h3 class="product-title"><?php echo $row['item_name']; ?></h3>
                        <div class="product-price">R<?php echo number_format($row['price'], 2); ?></div>
                        <p>Size: <?php echo $row['size']; ?></p>
                        
                        <!-- The addToCart function we added to your footer -->
                        <a href="index.php?add_to_cart=<?php echo $row['product_id']; ?>" class="btn btn-full" style="display:block; text-align:center;">Add to Cart</a>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1/-1; text-align:center;'>No products found. Start by selling an item!</p>";
            }
            ?>
        </div>
    </div>
</main>

<main id="shop" class="container">
    <h2>Shop Collection</h2>
    <p>Explore all available items</p>
    <hr>

    <div class="shop-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <form action="shop.php" method="GET">
                <div class="sidebar-section">
                    <h3>Category</h3>
                    <ul>
                        <li><input type="radio" name="category" value="all" checked> All</li>
                        <li><input type="radio" name="category" value="tops"> Tops</li>
                        <li><input type="radio" name="category" value="bottoms"> Bottoms</li>
                        <li><input type="radio" name="category" value="outerwear"> Outerwear</li>
                        <li><input type="radio" name="category" value="dresses"> Dresses</li>
                        <li><input type="radio" name="category" value="shoes"> Shoes</li>
                        <li><input type="radio" name="category" value="accessories"> Accessories</li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3>Brand</h3>
                    <ul>
                        <li><input type="checkbox" name="brand[]" value="burberry"> Burberry</li>
                        <li><input type="checkbox" name="brand[]" value="levis"> Levi's</li>
                        <li><input type="checkbox" name="brand[]" value="reformation"> Reformation</li>
                        <li><input type="checkbox" name="brand[]" value="acne"> Acne Studios</li>
                    </ul>
                </div>
                <button type="submit" class="btn btn-full">Filter Results</button>
            </form>
        </div>

        <!-- Main Shop Grid (Shows All Products) -->
        <div class="main-shop">
            <div class="grid">
                <?php
                // Fetch all approved products for the shop section
                $sql_all = "SELECT * FROM tblproduct WHERE status = 'approved' ORDER BY upload_date DESC";
                $result_all = $conn->query($sql_all);

                if ($result_all && $result_all->num_rows > 0) {
                    while($prod = $result_all->fetch_assoc()) {
                        ?>
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <img src="<?php echo $prod['image_path']; ?>" alt="Product" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <div class="product-brand"><?php echo strtoupper($prod['brand']); ?></div>
                            <h3 class="product-title"><?php echo $prod['item_name']; ?></h3>
                            <div class="product-price">R<?php echo number_format($prod['price'], 2); ?></div>
                            <p>Size: <?php echo $prod['size']; ?></p>
                            <a href="index.php?add_to_cart=<?php echo $prod['product_id']; ?>" class="btn btn-full" style="display:block; text-align:center;">Add to Cart</a>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</main>

<main id="sell" class="container">
    <div class="form-container">
        <h2 style="text-align:center;">Sell an Item</h2>
        <hr>
        <!-- The form action points to process_sell.php to handle the logic -->
        <form action="process_sell.php" method="POST" enctype="multipart/form-data">
            <div class="file-upload-area">
                <b>Drag and drop your image here</b><br><br>
                <input type="file" name="item_image" accept="image/*" required>
            </div>

            <div class="form-group">
                <label>Item Name:</label>
                <input type="text" name="item_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Brand:</label>
                <input type="text" name="brand" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <select name="category" class="form-control" required>
                    <option value="">-- Select One --</option>
                    <option value="tops">Tops</option>
                    <option value="bottoms">Bottoms</option>
                    <option value="outerwear">Outerwear</option>
                </select>
            </div>

            <div class="form-group">
                <label>Size:</label>
                <input type="text" name="size" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Desired Price (R):</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Condition:</label>
                <select name="condition" class="form-control" required>
                    <option value="New">New</option>
                    <option value="Like New">Like New</option>
                    <option value="Good">Good</option>
                    <option value="Fair">Fair</option>
                </select>
            </div>

                        <div class="form-group">
                            <label>Description:</label>
                            <textarea name="description" rows="5" class="form-control" required></textarea>
                        </div>

            <button type="submit" class="btn btn-full" style="background-color: green; border-color: darkgreen;">Submit Item for Approval</button>
        </form>
    </div>
</main>

<!-- Login and Cart sections remain mostly the same for UI -->
<main id="login" class="container">
    <div class="auth-card">
        <h2 style="text-align:center;">User Login</h2>
        <hr>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-full">Sign In</button>
        </form>
        <br>
        <a href="#forgot">Forgot password?</a>
    </div>

    <div class="auth-card">
        <h2 style="text-align:center;">Create a New Account</h2>
        <hr>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            
            <button type="submit" class="btn btn-full">Register Account</button>
        </form>
    </div>
</main>

<main id="cart" class="container">
    <h2>Your Shopping Cart</h2>
    <hr>

    <?php if (empty($_SESSION['cart'])): ?>
        <!-- Show this if cart is empty -->
        <div style="text-align: center; border: 3px solid black; padding: 50px; background-color: #f0f0f0;">
            <h1 style="font-size: 48px; margin: 0;">🛒</h1>
            <h2>Your shopping cart is currently empty!</h2>
            <br>
            <a href="#shop" class="btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <!-- Show this if items are in the cart -->
        <div class="cart-items">
            <?php
            $total = 0;
            // Turn the session array into a comma-separated list of IDs (e.g., "1,4,7")
            $ids_in_cart = implode(',', $_SESSION['cart']);
            
            // Fetch only the products that are in the cart
            $cart_sql = "SELECT * FROM tblproduct WHERE product_id IN ($ids_in_cart)";
            $cart_result = $conn->query($cart_sql);

            if ($cart_result && $cart_result->num_rows > 0) {
                echo "<table style='width:100%; border-collapse: collapse; margin-bottom: 20px;'>";
                echo "<tr style='background-color: black; color: white; text-align: left;'>
                        <th style='padding:10px;'>Item Details</th>
                        <th style='padding:10px; text-align: right;'>Price</th>
                      </tr>";
                      
                while($cart_item = $cart_result->fetch_assoc()) {
                    $total += $cart_item['price'];
                    echo "<tr>";
                    echo "<td style='padding:15px; border-bottom: 1px solid #ccc;'>
                            <b>" . strtoupper($cart_item['brand']) . "</b> - " . $cart_item['item_name'] . "<br>
                            <small>Size: " . $cart_item['size'] . " | Condition: " . $cart_item['condition'] . "</small>
                          </td>";
                    echo "<td style='padding:15px; border-bottom: 1px solid #ccc; text-align:right; font-weight:bold; color: green;'>
                            R" . number_format($cart_item['price'], 2) . "
                          </td>";
                    echo "</tr>";
                }
                
                echo "<tr>
                        <td style='padding:15px; font-weight:bold; text-align:right; font-size: 18px;'>Total Amount:</td>
                        <td style='padding:15px; font-weight:bold; text-align:right; font-size: 18px; color: green;'>
                            R" . number_format($total, 2) . "
                        </td>
                      </tr>";
                echo "</table>";
            }
            ?>
            <div style="text-align: right;">
                <a href="index.php?clear_cart=1" class="btn btn-outline" style="color:red; border-color:red;">Empty Cart</a>
                <a href="checkout.php" class="btn" style="background-color: green; border-color: darkgreen;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>