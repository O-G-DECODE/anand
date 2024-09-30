<?php
// Include the connection file
include 'connection.php';

$message = "";

// Fetch departments for the dropdown
$sql = "SELECT department_id, name FROM department";
$department_result = $conn->query($sql);
$departments = [];

// Fetch clubs for the dropdown
$sql = "SELECT club_id, name FROM club";
$club_result = $conn->query($sql);
$clubs = [];

// Populate departments and clubs
if ($department_result->num_rows > 0) {
    while ($row = $department_result->fetch_assoc()) {
        $departments[] = $row;
    }
}
if ($club_result->num_rows > 0) {
    while ($row = $club_result->fetch_assoc()) {
        $clubs[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_number = trim($_POST['roll_number']);
    $department_id = $_POST['department_id'];
    $course_id = $_POST['course_id'];
    $password = password_hash($roll_number, PASSWORD_DEFAULT); // Set password as roll_number and hash it
    $club_id = $_POST['club_id'];

    if (!empty($roll_number) && !empty($department_id) && !empty($course_id)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO student (roll_number, department_id, course_id, password, club_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiss", $roll_number, $department_id, $course_id, $password, $club_id);
        
        if ($stmt->execute()) {
            $message = "Student added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
    }
}

// Handle AJAX request for courses
if (isset($_GET['department_id'])) {
    $department_id = intval($_GET['department_id']);
    $stmt = $conn->prepare("SELECT course_id, name FROM course WHERE department_id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    echo json_encode($courses);
    exit; // Stop further execution for AJAX call
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <link rel="stylesheet" href="style_add_dlt.css">
    <script>
        function fetchCourses(departmentId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'add_student.php?department_id=' + departmentId, true);
            xhr.onload = function() {
                if (this.status == 200) {
                    const courses = JSON.parse(this.responseText);
                    let options = '<option value="">--Select Course--</option>';
                    courses.forEach(course => {
                        options += `<option value="${course.course_id}">${course.name}</option>`;
                    });
                    document.getElementById('course_id').innerHTML = options;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>


<div class="form-container">
<h2>Add New Student</h2>
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <label for="roll_number">Roll Number:</label>
        <input type="text" id="roll_number" name="roll_number" required>
        
        <label for="department_id">Select Department:</label>
        <select id="department_id" name="department_id" onchange="fetchCourses(this.value)" required>
            <option value="">--Select a Department--</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['department_id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="course_id">Select Course:</label>
        <select id="course_id" name="course_id" required>
            <option value="">--Select Course--</option>
        </select>
        
        <label for="club_id">Select Club:</label>
        <select id="club_id" name="club_id">
            <option value="">--No Club--</option>
            <?php foreach ($clubs as $club): ?>
                <option value="<?php echo $club['club_id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Add Student">
    </form>
</div>

</body>
</html>
