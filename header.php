<?php
session_start();
include_once 'config.php';

// ==========================================
// CART LOGIC
// ==========================================
// 1. Create an empty cart if one doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array(); 
}

// 2. Add an item to the cart
if (isset($_GET['add_to_cart'])) {
    $product_id = $_GET['add_to_cart'];
    
    // Only add it if it's not already in the cart
    if (!in_array($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id;
    }
    
    // Redirect back to the shop to prevent refresh bugs
    header("Location: index.php?cart_added=1#shop");
    exit();
}

// 3. Clear the cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = array();
    header("Location: index.php#cart");
    exit();
}

// Count items for the navigation bar
$cart_count = count($_SESSION['cart']);
// ==========================================
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastimes | Premium Second-Hand Clothing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: black;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        a {
            color: black;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        h1, h2, h3 {
            margin-top: 0;
        }

        .btn {
            background-color: black;
            color: white;
            padding: 10px 15px;
            border: 2px solid black;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            display: inline-block;
        }

        .btn:hover {
            background-color: white;
            color: black;
        }

        .btn-outline {
            background-color: white;
            color: black;
        }

        .btn-full {
            width: 100%;
            margin-top: 10px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            padding: 10px;
        }

        .global-header {
            background-color: black;
            color: white;
            padding: 15px 20px;
        }

        .global-header a {
            color: white;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            display: inline-block;
        }

        .nav-links {
            list-style-type: none;
            padding: 0;
            margin: 0;
            float: right;
        }

        .nav-links li {
            display: inline-block;
            margin-left: 15px;
            margin-top: 5px;
        }

        .global-header::after {
            content: "";
            display: table;
            clear: both;
        }

        .hero {
            background-color: #dddddd;
            text-align: center;
            padding: 50px 20px;
            border-bottom: 3px solid black;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            border: 2px solid black;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .product-img-wrapper {
            background-color: #cccccc;
            height: 250px;
            border: 1px solid #999;
            text-align: center;
            line-height: 250px;
            font-weight: bold;
            color: #555;
            margin-bottom: 10px;
            position: relative;
        }
        
        /* Ensures dynamic images fit perfectly */
        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .badge {
            background-color: red;
            color: white;
            font-weight: bold;
            padding: 5px;
            position: absolute;
            top: 5px;
            left: 5px;
            line-height: 1;
            border: 1px solid black;
        }

        .product-brand {
            font-size: 12px;
            color: #555;
            font-weight: bold;
        }

        .product-title {
            font-size: 16px;
            margin: 5px 0;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: green;
        }

        .shop-layout {
            display: flex;
            flex-wrap: wrap;
        }

        .sidebar {
            width: 200px;
            border-right: 2px solid black;
            padding-right: 20px;
            margin-right: 20px;
        }

        .sidebar-section {
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .main-shop {
            flex: 1;
        }

        .form-container, .auth-card {
            border: 3px solid black;
            padding: 20px;
            background-color: #eeeeee;
            max-width: 500px;
            margin: 0 auto 30px auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 2px solid #888;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .file-upload-area {
            border: 3px dashed black;
            padding: 30px;
            text-align: center;
            background-color: white;
            margin-bottom: 20px;
        }

        .global-footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            border-top: 5px solid gray;
        }

        .demo-separator {
            background-color: yellow;
            color: black;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border: 2px dashed red;
            margin: 30px 0;
        }

        @media (max-width: 700px) {
            .nav-links {
                float: none;
                display: block;
                margin-top: 10px;
            }
            .nav-links li {
                display: block;
                margin: 5px 0;
            }
            .shop-layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 2px solid black;
                padding-right: 0;
                margin-right: 0;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="global-header">
        <div class="logo">
            <a href="index.php">Pastimes Clothing</a>
        </div>
        <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="index.php#shop">Shop</a></li>
    <li><a href="sell.php">Sell Item</a></li>
    
    <?php if(isset($_SESSION['username'])): ?>
        <!-- This displays the name of the person who logged in -->
        <li style="color: #00FF00; font-weight: bold;">
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
        </li>
        <li><a href="logout.php" style="color: red;">Logout</a></li>
    <?php else: ?>
        <!-- This shows if no one is logged in -->
        <li><a href="index.php#login">Login</a></li>
    <?php endif; ?>
    
    <li><a href="index.php#cart" id="cart-link">Cart (<?php echo $cart_count; ?>)</a></li>
    
    <!-- NEW ADMIN LINK -->
    <li><a href="admin_login.php" style="color: yellow; border: 1px solid yellow; padding: 2px 8px; border-radius: 4px;">Admin Portal</a></li>
</ul>
    </header>