<?php
include_once 'config.php';
include 'header.php'; 

// 1. Security Checks
$selected_items = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['selected_items'])) {
    $selected_items = array_map('intval', $_POST['selected_items']);
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Keep only selected items that exist in cart
        $selected_items = array_intersect($selected_items, array_keys($_SESSION['cart']));
    }
}

if (empty($_SESSION['cart'])) {
    echo "<script>window.location='index.php#cart';</script>";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to your account to checkout!'); window.location='index.php#login';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($selected_items)) {
    echo "<script>alert('Please select items from your cart to checkout.'); window.location='index.php#cart';</script>";
    exit();
}

$checkout_items = !empty($selected_items) ? $selected_items : array_keys($_SESSION['cart']);
?>

<main class="container">
    <div class="form-container" style="max-width: 600px; margin: 0 auto;">
        <h2 style="text-align:center;">Secure Checkout</h2>
        <hr>
        
        <h3>Order Summary</h3>
        <div style="background-color: #fff; padding: 15px; border: 1px solid #ccc; margin-bottom: 20px;">
            <?php
            $total = 0;
            $ids_in_cart = implode(',', $checkout_items);
            $cart_sql = "SELECT * FROM tblproduct WHERE product_id IN ($ids_in_cart)";
            $cart_result = $conn->query($cart_sql);

            if ($cart_result && $cart_result->num_rows > 0) {
                while($item = $cart_result->fetch_assoc()) {
                    $item_qty = $_SESSION['cart'][$item['product_id']];
                    $item_total = $item['price'] * $item_qty;
                    $total += $item_total;
                    echo "<p><b>" . strtoupper($item['brand']) . "</b> - " . $item['item_name'] . " (Qty: " . $item_qty . ") <span style='float:right; color:green; font-weight:bold;'>R" . number_format($item_total, 2) . "</span></p>";
                }
                echo "<hr>";
                $buyer_fee = defined('BUYER_PROTECTION_FEE') ? BUYER_PROTECTION_FEE : 0.0;
                $final_total = $total + $buyer_fee;
                echo "<p><b>Buyer Protection Fee:</b> <span style='float:right; color:green;'>R" . number_format($buyer_fee, 2) . "</span></p>";
                echo "<h4>Total Amount to Pay: <span style='float:right; color:green;'>R" . number_format($final_total, 2) . "</span></h4>";
            }
            ?>
        </div>

        <!-- The Confirmation Form -->
        <form action="process_checkout.php" method="POST">
            <?php foreach ($checkout_items as $item_id): ?>
                <input type="hidden" name="selected_items[]" value="<?php echo intval($item_id); ?>">
            <?php endforeach; ?>
            <input type="hidden" name="buyer_protection_fee" value="<?php echo number_format(defined('BUYER_PROTECTION_FEE') ? BUYER_PROTECTION_FEE : 0, 2, '.', ''); ?>">
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