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
                        <li><input type="radio" name="cat" value="all" checked> All</li>
                        <li><input type="radio" name="cat" value="tops"> Tops</li>
                        <li><input type="radio" name="cat" value="bottoms"> Bottoms</li>
                    </ul>
                </div>
                <button type="submit" class="btn btn-full">Filter</button>
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
                            
                            <!-- addToCart() function is defined in footer.php -->
                            <button class="btn btn-full" onclick="addToCart()">Add to Cart</button>
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