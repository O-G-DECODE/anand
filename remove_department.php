<?php
// Include the connection file
include 'connection.php';

$message = "";

// Fetch departments for the dropdown
$sql = "SELECT department_id, name FROM department";
$result = $conn->query($sql);
$departments = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_id = $_POST['department_id'];

    // Prepare and execute the deletion of associated courses
    $course_delete_stmt = $conn->prepare("DELETE FROM course WHERE department_id = ?");
    $course_delete_stmt->bind_param("i", $department_id);
    
    if ($course_delete_stmt->execute()) {
        // Now delete the department
        $stmt = $conn->prepare("DELETE FROM department WHERE department_id = ?");
        $stmt->bind_param("i", $department_id);
        
        if ($stmt->execute()) {
            $message = "Department and associated courses removed successfully!";
        } else {
            $message = "Error deleting department: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Error deleting associated courses: " . $course_delete_stmt->error;
    }

    $course_delete_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Department</title>
    <link rel="stylesheet" href="style_add_dlt.css">
</head>
<body>


<div class="form-container">
<h2>Remove Department</h2>
<?php if ($message): ?>
    <div class="<?= strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
        <?= $message; ?>
        <?= strpos($message, 'Error') === false ? '<button onclick="location.href=\'admin_page.php\'">OK</button>' : ''; ?>
    </div>
<?php endif; ?>
    
    <form action="" method="POST">
        <label for="department_id">Select Department:</label>
        <select id="department_id" name="department_id" required>
            <option value="">--Select a Department--</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['department_id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Remove Department">
    </form>
</div>

</body>
</html>
