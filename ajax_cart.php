<?php
session_start();
include_once 'config.php';
header('Content-Type: application/json');

// Initialize cart if missing
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

switch ($action) {
    case 'add':
        if ($product_id > 0) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]++;
            } else {
                $_SESSION['cart'][$product_id] = 1;
            }
        }
        break;
    case 'update':
        if ($product_id > 0) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        break;
    case 'remove':
        if ($product_id > 0) {
            unset($_SESSION['cart'][$product_id]);
        }
        break;
    case 'clear':
        $_SESSION['cart'] = array();
        break;
}

// Recalculate totals
$cart_count = 0;
$cart_total = 0.0;
if (!empty($_SESSION['cart'])) {
    // fetch product prices for accurate total
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    if (!empty($ids)) {
        $in = implode(',', $ids);
        $sql = "SELECT product_id, price FROM tblproduct WHERE product_id IN ($in)";
        $res = mysqli_query($conn, $sql);
        $prices = array();
        while ($r = mysqli_fetch_assoc($res)) {
            $prices[$r['product_id']] = floatval($r['price']);
        }
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $cart_count += $qty;
            $price = isset($prices[$pid]) ? $prices[$pid] : 0.0;
            $cart_total += $price * $qty;
        }
    }
}

// Buyer protection fee: flat value from config if set, otherwise 0
$buyer_protection_fee = defined('BUYER_PROTECTION_FEE') ? BUYER_PROTECTION_FEE : 0.0;
$cart_total_with_fee = $cart_total + $buyer_protection_fee;

echo json_encode([
    'success' => true,
    'cart_count' => $cart_count,
    'cart_total' => number_format($cart_total, 2, '.', ''),
    'buyer_protection_fee' => number_format($buyer_protection_fee, 2, '.', ''),
    'cart_total_with_fee' => number_format($cart_total_with_fee, 2, '.', '')
]);
