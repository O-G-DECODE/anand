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
        // Login successful, store email in session and redirect to dashboard page
        $_SESSION['email'] = $email;
        header('Location: staff_page.php');
        exit;
    } else {
        // Email or password incorrect, display error message
        echo '<script>alert("Invalid email or password!")</script>';
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Staff Login Section -->
        <div class="login-section">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h3>Staff Login</h3>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit" name="submit" value="Login">Login</button>
                <br> <br><a href="forget_staff.html"><u> Forget password? </u> </a>
            </form>
        </div>
    </div>
</body>
</html>