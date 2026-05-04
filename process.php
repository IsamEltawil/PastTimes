<?php
include 'config.php'; // Connect to your clothing_store database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $brand     = $_POST['brand'];
    $price     = $_POST['price'];
    $size      = $_POST['size'];
    $condition = $_POST['condition'];
    
    // 1. Handle the Image Upload
    $target_dir  = "uploads/";
    $file_name   = time() . "_" . basename($_FILES["item_image"]["name"]); // Add a timestamp to avoid duplicate names
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
        // 2. Save the details to tblproduct
        // Note: Using '1' for seller_id as a placeholder until you have login sessions working
        $sql = "INSERT INTO tblproduct (seller_id, item_name, brand, price, size, `condition`, image_path, status) 
                VALUES (1, '$item_name', '$brand', '$price', '$size', '$condition', '$target_file', 'approved')";
        
        if ($conn->query($sql)) {
            // 3. Redirect back to home page if successful
            header("Location: index.php?upload=success");
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>