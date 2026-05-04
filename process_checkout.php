<?php
session_start();
include 'config.php';

// Only run this if they actually submitted the checkout form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $buyer_id = $_SESSION['user_id'];
    $checkout_success = true;

    // Prepare the statement ONCE outside the loop for security and speed
    $stmt = $conn->prepare("INSERT INTO tblorder (buyer_id, product_id, total_amount, status) VALUES (?, ?, ?, 'Pending')");

    // Loop through each item in the session cart
    foreach ($_SESSION['cart'] as $session_product_id) {
        
        $product_id = intval($session_product_id); 
        
        // Fetch the exact price for this specific item
        $price_sql = "SELECT price FROM tblproduct WHERE product_id = $product_id";
        $price_result = $conn->query($price_sql);
        
        if ($price_result && $price_result->num_rows > 0) {
            $item = $price_result->fetch_assoc();
            $item_price = $item['price'];
            
            // Bind the data and execute the insert for this specific product
            $stmt->bind_param("iid", $buyer_id, $product_id, $item_price);
            
            if (!$stmt->execute()) {
                $checkout_success = false;
            }
        }
    }

    $stmt->close();

    if ($checkout_success) {
        // Success! Empty the cart and redirect back to the home page with an alert
        $_SESSION['cart'] = array();
        echo "<script>alert('Order Confirmed! Thank you for shopping at Pastimes.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Notice: There was an issue processing one or more items.'); window.location='index.php?clear_cart=1';</script>";
    }
} else {
    // If they try to visit this file directly, kick them back to home
    header("Location: index.php");
    exit();
}
?>