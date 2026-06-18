<?php
session_start();
include 'config.php';

// Only run this if they actually submitted the checkout form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $checkout_items = !empty($_POST['selected_items']) ? array_map('intval', $_POST['selected_items']) : array_keys($_SESSION['cart']);
    $checkout_items = array_intersect($checkout_items, array_keys($_SESSION['cart']));

    if (empty($checkout_items)) {
        header("Location: index.php#cart");
        exit();
    }

    $buyer_id = $_SESSION['user_id'];
    $checkout_success = true;

    // Prepare the statement ONCE outside the loop for security and speed
    $stmt = $conn->prepare("INSERT INTO tblorder (buyer_id, product_id, total_amount, status) VALUES (?, ?, ?, 'Pending')");

    // Loop through each selected item in the cart
    foreach ($checkout_items as $product_id) {
        
        $product_id = intval($product_id); 
        $quantity = $_SESSION['cart'][$product_id];
        
        // Fetch the exact price for this specific item
        $price_sql = "SELECT price FROM tblproduct WHERE product_id = $product_id";
        $price_result = $conn->query($price_sql);
        
        if ($price_result && $price_result->num_rows > 0) {
            $item = $price_result->fetch_assoc();
            $item_price = $item['price'] * $quantity;
            
            // Bind the data and execute the insert for this specific product
            $stmt->bind_param("iid", $buyer_id, $product_id, $item_price);
            
            if (!$stmt->execute()) {
                $checkout_success = false;
            }
        }
    }

    $stmt->close();

    // If there's a buyer protection fee, insert it as a single line item so it's visible in orders
    $buyer_fee = isset($_POST['buyer_protection_fee']) ? floatval($_POST['buyer_protection_fee']) : (defined('BUYER_PROTECTION_FEE') ? BUYER_PROTECTION_FEE : 0.0);
    if ($checkout_success && $buyer_fee > 0) {
        $stmt_fee = $conn->prepare("INSERT INTO tblorder (buyer_id, product_id, total_amount, status) VALUES (?, 0, ?, 'Fee')");
        $stmt_fee->bind_param("id", $buyer_id, $buyer_fee);
        if (!$stmt_fee->execute()) {
            $checkout_success = false;
        }
        $stmt_fee->close();
    }

    if ($checkout_success) {
        // Remove only the items that were purchased from the cart
        foreach ($checkout_items as $product_id) {
            unset($_SESSION['cart'][$product_id]);
        }
        echo "<script>alert('Order Confirmed! Thank you for shopping at Pastimes.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Notice: There was an issue processing one or more items.'); window.location='index.php#cart';</script>";
    }
} else {
    // If they try to visit this file directly, kick them back to home
    header("Location: index.php");
    exit();
}
?>