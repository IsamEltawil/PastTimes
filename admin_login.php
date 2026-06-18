<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded Admin Verification
    if (($username === 'Isam' && $password === 'Isam@2026') || 
        ($username === 'Motau' && $password === 'Motau@2026') 
        || 
        ($username === 'Lethabo' && $password === 'Lethabo@2026')) {
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Admin Credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login | Pastimes</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #222; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { border: 3px solid #00FF00; padding: 40px; background-color: #111; width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { background-color: #00FF00; color: black; font-weight: bold; padding: 10px; width: 100%; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Portal</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form action="admin_login.php" method="POST">
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Admin Password" required>
            <button type="submit">Access System</button>
        </form>
    </div>
</body>
</html>