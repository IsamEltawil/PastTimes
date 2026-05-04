<?php 
include 'header.php'; // This starts the session and connects to the DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Check if user exists (Notice we added is_verified to the SELECT)
    $stmt = $conn->prepare("SELECT user_id, username, password_hash, is_verified FROM tbluser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // 2. Verify password against the hash in the DB
        if (password_verify($password, $user['password_hash'])) {
            
            // 3. NEW: Check if the admin has approved them yet
            if ($user['is_verified'] == 0) {
                echo "<script>alert('Your account is currently pending. An admin must verify your registration before you can log in.'); window.location='index.php#login';</script>";
            } else {
                // Verified! Let them in.
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                echo "<script>alert('Welcome back, " . htmlspecialchars($user['username']) . "!'); window.location='index.php';</script>";
            }

        } else {
            echo "<script>alert('Invalid password.'); window.location='index.php#login';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.location='index.php#login';</script>";
    }
}
?>

<main id="login" class="container">
    <div class="auth-card">
        <h2 style="text-align:center;">User Login</h2>
        <hr>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-full">Sign In</button>
        </form>
        <br>
        <p style="text-align:center;">
            New here? <a href="register.php">Create an Account</a>
        </p>
    </div>
</main>

<?php include 'footer.php'; // Closes the HTML and adds JS ?>