<?php 
include 'header.php'; // This connects to your DB automatically

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get the data from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
$checkEmail = $conn->prepare("SELECT email FROM tbluser WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$result = $checkEmail->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('This email is already registered. Please login instead.');</script>";
} else {
    // Proceed with the INSERT command...
 

    // 2. Encrypt the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Save it to the database using a Prepared Statement
    $stmt = $conn->prepare("INSERT INTO tbluser (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        // This confirms it worked!
        echo "<script>alert('Account saved in database! You can now login.'); window.location='index.php#login';</script>";
    } else {
        echo "<script>alert('Error saving to database.');</script>";
    }
    $stmt->close();

}
}
?>

<main class="container">
    <div class="auth-card">
        <h2 style="text-align:center;">Create a New Account</h2>
        <hr>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-full">Register Account</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>