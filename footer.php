<!-- =======================================================
         GLOBAL FOOTER (footer.php)
         ======================================================= -->
    <footer class="global-footer">
        <div class="container" style="display: flex; justify-content: space-between; flex-wrap: wrap; text-align: left;">
            <div style="margin-bottom: 20px;">
                <b style="font-size: 18px;">Pastimes Clothing</b><br>
                <p style="max-width: 300px; font-size: 14px; margin-top: 10px;">
                    Your premium destination for curated second-hand fashion. 
                    Buy and sell timeless pieces with ease.
                </p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <b>Quick Links</b><br>
                <ul style="list-style: none; padding: 0; font-size: 14px; margin-top: 10px;">
                    <li><a href="index.php" style="color: white;">Home</a></li>
                    <li><a href="shop.php" style="color: white;">Shop All</a></li>
                    <li><a href="sell.php" style="color: white;">Sell an Item</a></li>
                </ul>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #444; margin: 20px 0;">

        <b>&copy; 2026 Pastimes Clothing. All rights reserved.</b><br>
        <small>Website created by Isam and Lethabo</small>
    </footer>

    <!-- =======================================================
         GLOBAL SCRIPTS
         ======================================================= -->
    <script>
        /**
         * Updates the cart counter in the header
         */
        function addToCart() {
            let cartLink = document.getElementById('cart-link');
            if (cartLink) {
                // Extracts the number inside the parentheses, e.g., "Cart (0)" -> 0
                let currentCount = parseInt(cartLink.innerText.match(/\d+/)[0]);
                cartLink.innerText = `Cart (${currentCount + 1})`;
                alert("Item added to your Pastimes cart!");
            }
        }

        /**
         * Placeholder for UI enhancements 
         * (Add logic here for smooth scrolling or modal triggers)
         */
        console.log("Pastimes UI Loaded.");
    </script>
</body>
</html>