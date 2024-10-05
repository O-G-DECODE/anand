<?php
// Include the connection file
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL query to fetch staff_id from staff table corresponding to email
    $sql = "SELECT staff_id FROM staff WHERE email = '$email' AND password = '$password'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $staff_id = $result->fetch_assoc()['staff_id'];

        // Check if the staff_id is in the admin table
        $sql = "SELECT * FROM admin WHERE staff_id = '$staff_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // If the staff_id is in the admin table, redirect to admin_page.php
            header("Location: admin_page.php");
            exit;
        } else {
            $error = "You are not authorized to access the admin page";
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    
    <div class="login-section">

    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>

    <form method="post">
    <h3>Admin Login</h3>
        
        <input type="email" id="email" name="email" placeholder="Email" required><br>
        <input type="password" id="password" name="password" placeholder="Password" require><br>
        <button type="submit" name="submit" value="Login">Login</button>
    </form>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>