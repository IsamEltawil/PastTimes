<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clothing_store";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Flat buyer protection fee applied to each cart/checkout (currency units)
define('BUYER_PROTECTION_FEE', 15.00);
?>