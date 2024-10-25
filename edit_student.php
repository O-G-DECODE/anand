<?php
session_start();
include("connection.php");

// Check if the student is logged in
if (!isset($_SESSION['roll_number'])) {
    echo "No student is logged in.";
    exit;
}

$roll_number = $_SESSION['roll_number'];

// Fetch current student details
$stmt = $conn->prepare("SELECT name, password FROM student WHERE roll_number = ?");
$stmt->bind_param("s", $roll_number);
$stmt->execute();
$stmt->bind_result($name, $password);
if (!$stmt->fetch()) {
    echo "Student not found.";
    $stmt->close();
    exit;
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['name']);
    $new_password = trim($_POST['password']);

    // Update student details
    $update_stmt = $conn->prepare("UPDATE student SET name = ?, password = ? WHERE roll_number = ?");
    $update_stmt->bind_param("ssi", $new_name, $new_password, $roll_number);

    if ($update_stmt->execute()) {
        echo "<script>alert('Student details updated successfully!'); window.location.href = 'student_profile.php';</script>";
    } else {
        echo "Error updating student: " . $conn->error;
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="edit_admin.css">
</head>
<body>

<div class="container">
    <h2>Edit Student Details</h2>
    <form method="POST" action="">
        <label for="roll_number">Roll Number</label>
        <input type="text" id="roll_number" name="roll_number" value="<?php echo htmlspecialchars($roll_number); ?>" readonly required>

        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>

        <button type="submit">Update Student</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
