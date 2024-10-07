<?php
include("connection.php");

// Start session
session_start();

// Login form submission
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email and password exist in database
    $query = "SELECT * FROM staff WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Login successful, store email in session and redirect to staff page
        $_SESSION['email'] = $email;
        header('Location: staff_page.php');
        exit;
    } else {
        echo '<script>alert("Invalid email or password!")</script>';
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="login_form.css">
</head>
<body>
    <div class="container">
        <div class="login-section">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h3>Staff Login</h3>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit" name="submit" value="Login">Login</button>
                <a href="forget_staff.html">Forget password?</a>
            </form>
        </div>
    </div>
</body>
</html>
