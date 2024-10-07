<?php
include("connection.php");

// Start session
session_start();

// Login form submission
if (isset($_POST['submit'])) {
    $roll_number = $_POST['roll_number'];
    $password = $_POST['password'];

    // Check if roll number exists in database
    $query = "SELECT * FROM student WHERE roll_number = '$roll_number'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Roll number exists, check password
        $row = $result->fetch_assoc();
        if ($row['password'] == $password) {
            $_SESSION['roll_number'] = $roll_number; // Store roll number in session
            header('Location: student_page.php'); // Redirect to student page
            exit;
        } else {
            echo '<script>alert("Incorrect password!")</script>';
        }
    } else {
        echo '<script>alert("Roll number does not exist!")</script>';
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
    <title>Student Login</title>
    <link rel="stylesheet" href="login_form.css">
</head>
<body>
    <div class="container">
        <div class="login-section">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h3>Student Login</h3>
                <input type="text" id="roll_number" name="roll_number" placeholder="Roll Number" required>
                <input type="password" id="studentPassword" name="password" placeholder="Password" required>
                <button type="submit" name="submit" value="Login">Login</button>
                <a href="forgetstudent.html">Forget password?</a>
                <a href="student_registration.php">Register</a>
            </form>
        </div>
    </div>
</body>
</html>
