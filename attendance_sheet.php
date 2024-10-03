<?php
// Include the database connection file
include 'connection.php';

// Get the event_id from the GET parameter
$event_id = $_GET['event_id'];

// Use the $event_id variable to fetch the attendance sheet data

// Start the session
session_start();

// Check if the staff_id is set in the session
if (!isset($_SESSION['staff_id'])) {
    // If not, redirect to the login page or handle the error accordingly
    header('Location: staff_page.php');
    exit;
}

// Get the staff_id from the session
$staff_id = $_SESSION['staff_id'];

// Query to fetch the department_id from the staff table
$query = "SELECT department_id FROM staff WHERE staff_id = '$staff_id'";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $department_id = $row['department_id'];
} else {
    echo "Error: Staff ID not found in the staff table.";
    exit;
}

// Query to fetch the course_id and course name from the course table
$query = "SELECT course_id, name FROM course WHERE department_id = '$department_id'";
$result = mysqli_query($conn, $query);

// Array to store course names with their IDs
$course_names = [];
$students = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $course_id = $row['course_id'];
        $course_name = $row['name'];

        // Store course name in array using course_id as key
        $course_names[$course_id] = $course_name;

        // Query to fetch roll_number and name from the student table for the current course
        $query = "SELECT roll_number, name FROM student WHERE course_id = '$course_id'";
        $result2 = mysqli_query($conn, $query);

        if (mysqli_num_rows($result2) > 0) {
            while ($row2 = mysqli_fetch_assoc($result2)) {
                $roll_number = $row2['roll_number'];
                $student_name = $row2['name'];

                // Query to check if the roll number is in the request table and approve field is 1
                $query = "SELECT * FROM request WHERE roll_number = '$roll_number' AND approve = '$department_id' AND event_id = '$event_id'";
                $result3 = mysqli_query($conn, $query);

                if (mysqli_num_rows($result3) > 0) {
                    // Add approved student data to the students array
                    $students[] = [
                        'roll_number' => $roll_number,
                        'name' => $student_name,
                        'course_name' => $course_name
                    ];
                }
            }
        }
    }
} else {
    echo "No courses found for the department.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Sheet</title>
    <link rel="stylesheet" type="text/css" href="event_style.css">
</head>
<body>
    <div class="container">
        <h2>Approved Students</h2>
        <table>
            <caption>Students Attendance Sheet</caption>
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year</th>
                </tr>
            </thead>
            <tbody>
            <?php
// Loop through the students array and print them in the table
foreach ($students as $student) {
    echo "<tr>";
    echo "<td>" . $student['roll_number'] . "</td>";
    echo "<td>" . $student['name'] . "</td>";
    echo "<td>" . $student['course_name'] . "</td>";
    echo "<td> 20" . substr($student['roll_number'], 0, 2) . "</td>"; // Extract the first two digits of the roll number
    echo "</tr>";
}
?>
            </tbody>
        </table>
    </div>
</body>
</html>