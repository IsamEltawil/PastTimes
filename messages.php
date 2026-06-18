<?php
session_start();
include 'config.php';
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to view your messages.'); window.location='index.php#login';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle sending a new message from user to seller (simple form post)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $product_id = intval($_POST['product_id']);
    $message_text = trim($_POST['message_text']);

    if ($receiver_id > 0 && !empty($message_text)) {
        $ins = $conn->prepare("INSERT INTO tblmessage (sender_id, receiver_id, product_id, message_text, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $ins->bind_param('iiis', $user_id, $receiver_id, $product_id, $message_text);
        $ins->execute();
        $ins->close();
        echo "<div style='background:#e7f7e7; padding:10px; border:1px solid #b2e2b2; margin-bottom:10px;'>Message sent to seller.</div>";
    }
}

?>

<main class="container">
    <div style="max-width: 900px; margin: 20px auto;">
        <h2>Your Messages</h2>
        <p>Communications from Pastimes Admin regarding your orders and deliveries.</p>
        <hr>

        <?php
        // Get all messages for this user from admin (sender_id = 0)
        $messages_query = "
            SELECT 
                m.message_id,
                m.message_text,
                m.timestamp,
                p.item_name,
                p.brand,
                p.product_id,
                o.order_id,
                o.status as order_status,
                o.order_date
            FROM tblmessage m
            JOIN tblproduct p ON m.product_id = p.product_id
            LEFT JOIN tblorder o ON o.product_id = p.product_id AND (o.buyer_id = ? OR o.seller_id = ?)
            WHERE m.receiver_id = ?
            ORDER BY m.timestamp DESC
        ";
        
        $stmt = $conn->prepare($messages_query);
        $stmt->bind_param("iii", $user_id, $user_id, $user_id);
        $stmt->execute();
        $messages_result = $stmt->get_result();
        
        if ($messages_result && $messages_result->num_rows > 0) {
            while($msg = $messages_result->fetch_assoc()) {
                echo "<div style='background: white; border: 2px solid #333; padding: 20px; margin-bottom: 15px; border-radius: 5px;'>";
                echo "<div style='background: #f0f0f0; padding: 10px; margin-bottom: 15px; border-left: 4px solid green;'>";
                echo "<strong style='font-size: 16px;'>📩 Message from Pastimes Admin</strong><br>";
                echo "<small style='color: #666;'>Sent: " . date('M d, Y H:i', strtotime($msg['timestamp'])) . "</small>";
                echo "</div>";
                
                echo "<p><strong>Item:</strong> " . htmlspecialchars($msg['brand']) . " - " . htmlspecialchars($msg['item_name']) . "</p>";
                
                if ($msg['order_id']) {
                    echo "<p><strong>Order #" . $msg['order_id'] . ":</strong> " . ucfirst($msg['order_status']) . "</p>";
                }
                
                echo "<div style='background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0;'>";
                echo "<p>" . nl2br(htmlspecialchars($msg['message_text'])) . "</p>";
                echo "</div>";
                
                echo "</div>";
            }
        } else {
            echo "<div style='text-align: center; padding: 40px; background: #f0f0f0; border-radius: 5px;'>";
            echo "<h3>No messages yet</h3>";
            echo "<p>You don't have any messages from our admin team.</p>";
            echo "</div>";
        }
        
        $stmt->close();
        ?>

        <hr style="margin-top: 40px;">
        <a href="index.php" class="btn" style="background-color: black; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">← Back to Home</a>
    </div>
</main>

<?php include 'footer.php'; ?>
