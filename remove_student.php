<?php
// Include the connection file
include 'connection.php';

$message = "";
$studentDetails = null;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_number = trim($_POST['roll_number']);

    // Prepare and bind to check if student exists
    $stmt = $conn->prepare("SELECT s.name AS student_name, s.course_id, c.name AS course_name, d.name AS department_name
                             FROM student s
                             JOIN course c ON s.course_id = c.course_id
                             JOIN department d ON c.department_id = d.department_id
                             WHERE s.roll_number = ?");
    $stmt->bind_param("s", $roll_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $studentDetails = $result->fetch_assoc();
    } else {
        $message = "Student does not exist.";
    }

    $stmt->close();
}

// Check if the deletion should occur
if (isset($_POST['delete']) && $studentDetails) {
    $stmt = $conn->prepare("DELETE FROM student WHERE roll_number = ?");
    $stmt->bind_param("s", $roll_number);
    
    if ($stmt->execute()) {
        $message = "Student removed successfully!";
        $studentDetails = null; // Clear details after deletion
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Student</title>
    <link rel="stylesheet" href="style_add_dlt.css">
</head>
<body>

<div class="form-container">
<h2>Remove Student</h2>
<?php if ($message): ?>
    <div class="<?= strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
        <?= $message; ?>
        <?= strpos($message, 'Error') === false ? '<button onclick="location.href=\'admin_page.php\'">OK</button>' : ''; ?>
    </div>
<?php endif; ?>
    
    <form action="" method="POST">
        <label for="roll_number">Roll Number:</label>
        <input type="text" id="roll_number" name="roll_number" required>
        
        <input type="submit" value="Check Student">
    </form>
    
    <?php if ($studentDetails): ?>
        <h2>Student Details</h2>
        <p>Name: <?php echo htmlspecialchars($studentDetails['student_name']); ?></p>
        <p>Department: <?php echo htmlspecialchars($studentDetails['department_name']); ?></p>
        <p>Course: <?php echo htmlspecialchars($studentDetails['course_name']); ?></p>
        
        <form action="" method="POST">
            <input type="hidden" name="roll_number" value="<?php echo htmlspecialchars($roll_number); ?>">
            <input type="submit" name="delete" value="Remove Student">
        </form>
    <?php endif; ?>
</div>

</body>
</html>
