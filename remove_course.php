<?php
// Include the connection file
include 'connection.php';

$message = "";

// Fetch courses for the dropdown
$sql = "SELECT course_id, name FROM course";
$result = $conn->query($sql);
$courses = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM course WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    
    if ($stmt->execute()) {
        $message = "Course removed successfully!";
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
    <title>Remove Course</title>
    <link rel="stylesheet" href="style_add_dlt.css">
</head>
<body>



<div class="form-container">
<h2>Remove Course</h2>
<?php if ($message): ?>
    <div class="<?= strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
        <?= $message; ?>
        <?= strpos($message, 'Error') === false ? '<button onclick="location.href=\'admin_page.php\'">OK</button>' : ''; ?>
    </div>
<?php endif; ?>
    
    <form action="" method="POST">
        <label for="course_id">Select Course:</label>
        <select id="course_id" name="course_id" required>
            <option value="">--Select a Course--</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Remove Course">
    </form>
</div>

</body>
</html>
