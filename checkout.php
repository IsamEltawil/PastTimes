<?php
include_once 'config.php';
include 'header.php'; 

// 1. Security Checks
if (empty($_SESSION['cart'])) {
    echo "<script>window.location='index.php#cart';</script>";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to your account to checkout!'); window.location='index.php#login';</script>";
    exit();
}
?>

<main class="container">
    <div class="form-container" style="max-width: 600px; margin: 0 auto;">
        <h2 style="text-align:center;">Secure Checkout</h2>
        <hr>
        
        <h3>Order Summary</h3>
        <div style="background-color: #fff; padding: 15px; border: 1px solid #ccc; margin-bottom: 20px;">
            <?php
            $total = 0;
            $ids_in_cart = implode(',', $_SESSION['cart']);
            $cart_sql = "SELECT * FROM tblproduct WHERE product_id IN ($ids_in_cart)";
            $cart_result = $conn->query($cart_sql);

            if ($cart_result && $cart_result->num_rows > 0) {
                while($item = $cart_result->fetch_assoc()) {
                    $total += $item['price'];
                    echo "<p><b>" . strtoupper($item['brand']) . "</b> - " . $item['item_name'] . " <span style='float:right; color:green; font-weight:bold;'>R" . number_format($item['price'], 2) . "</span></p>";
                }
                echo "<hr>";
                echo "<h4>Total Amount to Pay: <span style='float:right; color:green;'>R" . number_format($total, 2) . "</span></h4>";
            }
            ?>
        </div>

        <!-- The Confirmation Form -->
        <form action="process_checkout.php" method="POST">
            <!-- Even though we don't save the address in the DB right now, adding this makes it feel like a real checkout! -->
            <div class="form-group">
                <label>Shipping Address (For delivery):</label>
                <textarea name="shipping_address" rows="3" class="form-control" placeholder="123 Rosebank College St, Polokwane..." required></textarea>
            </div>
            
            <div class="form-group">
                <label>Payment Method:</label>
                <select name="payment_method" class="form-control" required>
                    <option value="card">Credit / Debit Card</option>
                    <option value="eft">EFT (Bank Transfer)</option>
                    <option value="cash">Cash on Delivery</option>
                </select>
            </div>

            <button type="submit" class="btn btn-full" style="background-color: green; border-color: darkgreen; font-size: 18px; padding: 15px;">Confirm Purchase</button>
            <a href="index.php#cart" class="btn btn-full btn-outline" style="margin-top: 10px;">Cancel & Return to Cart</a>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>