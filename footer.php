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
         * Adds an item to the cart via AJAX and updates header counts
         */
        function addToCart(productId) {
            fetch('ajax_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=add&product_id=${encodeURIComponent(productId)}`
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    let cartLink = document.getElementById('cart-link');
                    if (cartLink) cartLink.innerText = `Cart (${data.cart_count})`;
                    alert('Item added to your Pastimes cart!');
                }
            }).catch(err => {
                console.error('Cart AJAX error', err);
            });
        }

        function toggleSelectAll(source) {
            let checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = source.checked;
            });
        }

        /**
         * Placeholder for UI enhancements 
         * (Add logic here for smooth scrolling or modal triggers)
         */
        console.log("Pastimes UI Loaded.");
    </script>
</body>
</html>