<?php
// Include the connection file
include("connection.php");
session_start();

if (!isset($_SESSION['email'])) {
    echo "No user is logged in.";
    exit;
}

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($course_id <= 0) {
    echo "Invalid course ID.";
    exit;
}

// Fetch current course name
$stmt = $conn->prepare("SELECT name FROM course WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->bind_result($course_name);
if (!$stmt->fetch()) {
    echo "Course not found.";
    $stmt->close();
    exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_course_name = trim($_POST['course_name']);

    $update_stmt = $conn->prepare("UPDATE course SET name = ? WHERE course_id = ?");
    $update_stmt->bind_param("si", $new_course_name, $course_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Course name updated successfully!'); window.location.href = 'course_details.php';</script>";
    } else {
        echo "Error updating course: " . $conn->error;
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <style>
        /* Your styling here */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e4d3ea;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #6e8efb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #5c7cfa;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Course Name</h2>
    <form method="POST" action="">
        <label for="course_name">Course Name</label>
        <input type="text" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>" required>

        <button type="submit">Update Course</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
