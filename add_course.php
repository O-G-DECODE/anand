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
    $course_name = trim($_POST['course_name']);
    $department_id = $_POST['department_id'];

    if (!empty($course_name) && !empty($department_id)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO course (name, department_id) VALUES (?, ?)");
        $stmt->bind_param("si", $course_name, $department_id);
        
        if ($stmt->execute()) {
            $message = "Course added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link rel="stylesheet" href="style_add_dlt.css">
    <script>
        function validateForm() {
            const courseName = document.forms["courseForm"]["course_name"].value;
            const departmentId = document.forms["courseForm"]["department_id"].value;
            if (courseName == "" || departmentId == "") {
                alert("All fields must be filled out");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<h1>Add New Course</h1>

<div class="form-container">
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form name="courseForm" action="" method="POST" onsubmit="return validateForm()">
        <label for="course_name">Course Name:</label>
        <input type="text" id="course_name" name="course_name" required>
        
        <label for="department_id">Select Department:</label>
        <select id="department_id" name="department_id" required>
            <option value="">--Select a Department--</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['department_id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Add Course">
    </form>
</div>

</body>
</html>
