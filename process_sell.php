<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $item_name   = $_POST['item_name'];
    $brand       = $_POST['brand'];
    $category    = $_POST['category'];
    $size        = $_POST['size'];
    $price       = $_POST['price'];
    $condition   = $_POST['condition'];
    $description = $_POST['description'];
    
    // Image Upload Logic
    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["item_image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
        // Use a placeholder seller_id (e.g., 1) or $_SESSION['user_id'] if logged in
        $seller_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

        $stmt = $conn->prepare("INSERT INTO tblproduct (seller_id, item_name, brand, category, price, size, `condition`, description, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')");
        $stmt->bind_param("isssdssss", $seller_id, $item_name, $brand, $category, $price, $size, $condition, $description, $target_file);
        
        if ($stmt->execute()) {
            header("Location: index.php?upload=success#shop");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>