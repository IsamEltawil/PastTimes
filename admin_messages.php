<?php
session_start();
include 'config.php';

// Security: Only admins can access
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle sending a message
if (isset($_POST['send_message'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $product_id = intval($_POST['product_id']);
    $message_text = $_POST['message_text'];
    $sender_id = 0; // Admin system user ID (placeholder for admin)
    
    $stmt = $conn->prepare("INSERT INTO tblmessage (sender_id, receiver_id, product_id, message_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $product_id, $message_text);
    
    if ($stmt->execute()) {
        $success_msg = "Message sent successfully!";
    } else {
        $error_msg = "Error sending message: " . $conn->error;
    }
    $stmt->close();
}

// Handle updating order status
if (isset($_POST['update_order_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['order_status'];
    
    if (in_array($new_status, ['pending', 'paid', 'delivered'])) {
        $stmt = $conn->prepare("UPDATE tblorder SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            $success_msg = "Order status updated to: " . ucfirst($new_status);
        } else {
            $error_msg = "Error updating status: " . $conn->error;
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Communications | Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: black; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; background-color: black; border: 1px solid black; cursor: pointer; margin-right: 5px; }
        .btn-green { background-color: green; }
        .panel { border: 2px solid black; padding: 15px; margin-top: 20px; max-width: 600px; background: #eee; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .message-box { background: white; padding: 15px; border: 1px solid #ccc; margin-bottom: 15px; border-radius: 4px; }
        .message-header { font-weight: bold; color: #333; margin-bottom: 10px; }
        .message-text { margin-bottom: 10px; }
        .message-time { font-size: 12px; color: #666; }
    </style>
</head>
<body>

    <h1>Order Communications & Delivery Management</h1>
    <a href="admin_dashboard.php">&larr; Back to Dashboard</a>
    <hr>

    <?php if (isset($success_msg)): ?>
        <div class="success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_msg)): ?>
        <div class="error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- SECTION: VIEW ALL ORDERS & SEND MESSAGES -->
    <h2>Active Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Buyer</th>
            <th>Seller</th>
            <th>Product</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php
        $order_query = "
            SELECT 
                o.order_id, 
                o.buyer_id, 
                o.product_id, 
                o.total_amount,
                o.order_date,
                o.status,
                b.username as buyer_name,
                p.seller_id,
                p.item_name,
                p.brand,
                s.username as seller_name
            FROM tblorder o
            JOIN tbluser b ON o.buyer_id = b.user_id
            JOIN tblproduct p ON o.product_id = p.product_id
            JOIN tbluser s ON p.seller_id = s.user_id
            ORDER BY o.order_date DESC
        ";
        
        $result = $conn->query($order_query);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>#" . $row['order_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['buyer_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['seller_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['brand']) . " - " . htmlspecialchars($row['item_name']) . "</td>";
                echo "<td><strong>" . ucfirst($row['status']) . "</strong></td>";
                echo "<td>" . date('M d, Y', strtotime($row['order_date'])) . "</td>";
                echo "<td>";
                echo "<a href='admin_messages.php?order_id=" . $row['order_id'] . "&buyer_id=" . $row['buyer_id'] . "&seller_id=" . $row['seller_id'] . "&product_id=" . $row['product_id'] . "' class='btn'>Send Message</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No orders found.</td></tr>";
        }
        ?>
    </table>

    <!-- SECTION: SEND MESSAGE TO BUYER/SELLER -->
    <?php if (isset($_GET['order_id'])): ?>
        <?php
        $order_id = intval($_GET['order_id']);
        $buyer_id = intval($_GET['buyer_id']);
        $seller_id = intval($_GET['seller_id']);
        $product_id = intval($_GET['product_id']);
        
        // Get order and product details
        $order_detail = $conn->query("SELECT o.*, b.username as buyer_name, b.email as buyer_email, s.username as seller_name, s.email as seller_email, p.item_name, p.brand FROM tblorder o JOIN tbluser b ON o.buyer_id = b.user_id JOIN tbluser s ON o.product_id = p.seller_id JOIN tblproduct p ON o.product_id = p.product_id WHERE o.order_id = $order_id");
        $order = $order_detail->fetch_assoc();
        ?>

        <div class="panel">
            <h3>Order #<?php echo $order_id; ?> - Communication</h3>
            <hr>
            <p><strong>Product:</strong> <?php echo htmlspecialchars($order['brand']) . " - " . htmlspecialchars($order['item_name']); ?></p>
            <p><strong>Buyer:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?> (<?php echo htmlspecialchars($order['buyer_email']); ?>)</p>
            <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_name']); ?> (<?php echo htmlspecialchars($order['seller_email']); ?>)</p>
            <p><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>
            <p><strong>Amount:</strong> R<?php echo number_format($order['total_amount'], 2); ?></p>
            
            <!-- Update Order Status -->
            <div style="background: #fff3cd; padding: 15px; margin: 15px 0; border-radius: 4px;">
                <form action="admin_messages.php?order_id=<?php echo $order_id; ?>&buyer_id=<?php echo $buyer_id; ?>&seller_id=<?php echo $seller_id; ?>&product_id=<?php echo $product_id; ?>" method="POST" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <label><strong>Update Order Status:</strong></label>
                    <select name="order_status" style="padding: 5px; margin-right: 10px;">
                        <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="paid" <?php echo ($order['status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                        <option value="delivered" <?php echo ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                    </select>
                    <button type="submit" name="update_order_status" class="btn btn-green">Update Status</button>
                </form>
            </div>
            <hr>

            <!-- View existing messages -->
            <h4>Message History:</h4>
            <?php
            $messages = $conn->query("SELECT m.*, u.username FROM tblmessage m LEFT JOIN tbluser u ON m.sender_id = u.user_id WHERE m.product_id = $product_id AND (m.sender_id = 0 OR m.sender_id IN ($buyer_id, $seller_id)) ORDER BY m.timestamp ASC");
            
            if ($messages && $messages->num_rows > 0) {
                while($msg = $messages->fetch_assoc()) {
                    $sender_name = ($msg['sender_id'] == 0) ? "ADMIN" : $msg['username'];
                    echo "<div class='message-box'>";
                    echo "<div class='message-header'>From: $sender_name</div>";
                    echo "<div class='message-text'>" . htmlspecialchars($msg['message_text']) . "</div>";
                    echo "<div class='message-time'>" . date('M d, Y H:i', strtotime($msg['timestamp'])) . "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No messages yet.</p>";
            }
            ?>

            <!-- Send message form -->
            <h4>Send Message:</h4>
            <form action="admin_messages.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                
                <div style="margin-bottom: 15px;">
                    <label><strong>Send to:</strong></label><br>
                    <input type="radio" name="receiver_id" value="<?php echo $buyer_id; ?>" checked> Buyer (<?php echo htmlspecialchars($order['buyer_name']); ?>)<br>
                    <input type="radio" name="receiver_id" value="<?php echo $seller_id; ?>"> Seller (<?php echo htmlspecialchars($order['seller_name']); ?>)
                </div>

                <div style="margin-bottom: 15px;">
                    <label><strong>Message:</strong></label><br>
                    <textarea name="message_text" rows="6" style="width:100%; padding: 10px;" placeholder="Type your message here..." required></textarea>
                </div>

                <button type="submit" name="send_message" class="btn btn-green">Send Message</button>
                <a href="admin_messages.php" class="btn">Cancel</a>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
