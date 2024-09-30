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
              $_SESSION['roll_number'] = $roll_number; // Corrected here
            // Login successful, redirect to welcome page
            header('Location: student_page.php');
            exit;
        } else {
            // Password incorrect, display error message
            echo '<script>alert("Incorrect password!")</script>';
        }
    } else {
        // Roll number does not exist, display error message
        echo '<script>alert("Roll number does not exist!")</script>';
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> Student Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Student Login Section -->
        <div class="login-section">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h3>Student Login</h3>
                <input type="text" id="roll_number" name="roll_number" placeholder="Roll Number" required>
                <input type="password" id="studentPassword" name="password" placeholder="Password" required>
                <button type="submit" name="submit" value="Login">Submit</button>
                <br> <br><a href="forgetstudent.html"><u> Forget password? </u> </a>
                <a href="student_registeration.php"><br> <br><u> Register</u></a>
            </form>
        </div>
    </div>
</body>
</html>