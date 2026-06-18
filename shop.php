<?php include 'header.php'; ?>

<main id="shop" class="container">
    <h2>Shop Collection</h2>
    <p>Premium Second-Hand Finds</p>
    <hr>

    <div class="shop-layout">
        <!-- Sidebar Filter (HTML integrated from your original code) -->
        <div class="sidebar">
            <form action="shop.php" method="GET">
                <div class="sidebar-section">
                    <h3>Category</h3>
                    <ul>
                        <li><input type="radio" name="category" value="all" <?php echo (!isset($_GET['category']) || $_GET['category']=='all') ? 'checked' : ''; ?>> All</li>
                        <li><input type="radio" name="category" value="tops" <?php echo (isset($_GET['category']) && $_GET['category']=='tops') ? 'checked' : ''; ?>> Tops</li>
                        <li><input type="radio" name="category" value="bottoms" <?php echo (isset($_GET['category']) && $_GET['category']=='bottoms') ? 'checked' : ''; ?>> Bottoms</li>
                        <li><input type="radio" name="category" value="outerwear" <?php echo (isset($_GET['category']) && $_GET['category']=='outerwear') ? 'checked' : ''; ?>> Outerwear</li>
                        <li><input type="radio" name="category" value="dresses" <?php echo (isset($_GET['category']) && $_GET['category']=='dresses') ? 'checked' : ''; ?>> Dresses</li>
                        <li><input type="radio" name="category" value="shoes" <?php echo (isset($_GET['category']) && $_GET['category']=='shoes') ? 'checked' : ''; ?>> Shoes</li>
                        <li><input type="radio" name="category" value="accessories" <?php echo (isset($_GET['category']) && $_GET['category']=='accessories') ? 'checked' : ''; ?>> Accessories</li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3>Brand</h3>
                    <ul>
                        <li><input type="checkbox" name="brand[]" value="burberry" <?php echo (isset($_GET['brand']) && in_array('burberry', (array)$_GET['brand'])) ? 'checked' : ''; ?>> Burberry</li>
                        <li><input type="checkbox" name="brand[]" value="levis" <?php echo (isset($_GET['brand']) && in_array('levis', (array)$_GET['brand'])) ? 'checked' : ''; ?>> Levi's</li>
                        <li><input type="checkbox" name="brand[]" value="reformation" <?php echo (isset($_GET['brand']) && in_array('reformation', (array)$_GET['brand'])) ? 'checked' : ''; ?>> Reformation</li>
                        <li><input type="checkbox" name="brand[]" value="acne" <?php echo (isset($_GET['brand']) && in_array('acne', (array)$_GET['brand'])) ? 'checked' : ''; ?>> Acne Studios</li>
                    </ul>
                </div>
                <button type="submit" class="btn btn-full">Filter Results</button>
            </form>
        </div>

        <!-- Dynamic Product Grid -->
        <div class="main-shop">
            <div class="grid">
                <?php
                $sql = "SELECT * FROM tblproduct WHERE status = 'approved' ORDER BY upload_date DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <!-- Uses image path stored during process.php upload -->
                                <img src="<?php echo $row['image_path']; ?>" alt="Product">
                            </div>
                            <div class="product-brand"><?php echo strtoupper($row['brand']); ?></div>
                            <h3 class="product-title"><?php echo $row['item_name']; ?></h3>
                            <div class="product-price">R<?php echo number_format($row['price'], 2); ?></div>
                            <p>Size: <?php echo $row['size']; ?></p>
                            
                            <a href="shop.php?add_to_cart=<?php echo $row['product_id']; ?>" class="btn btn-full">Add to Cart</a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No items currently for sale.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>