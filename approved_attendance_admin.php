<?php
// Include the database connection file
include 'connection.php';

// Get the event_id from the URL
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;

// If no event_id is provided, display a message and exit
if (!$event_id) {
    echo "No event specified.";
    exit;
}

// Query to fetch the event details
$query_event = "SELECT name, date FROM event WHERE event_id = '$event_id'";
$result_event = mysqli_query($conn, $query_event);

// Check if the event exists
if (mysqli_num_rows($result_event) === 0) {
    echo "Event not found.";
    exit;
}

// Fetch event details
$event_details = mysqli_fetch_assoc($result_event);

// Query to fetch approved students for the current event
$query_students = "
    SELECT s.roll_number, s.name, c.name AS course_name 
    FROM student s
    JOIN request r ON s.roll_number = r.roll_number
    JOIN course c ON s.course_id = c.course_id
    WHERE r.event_id = '$event_id' AND r.approve > 0
";
$result_students = mysqli_query($conn, $query_students);

// Store students for the event
$students = [];
if (mysqli_num_rows($result_students) > 0) {
    while ($row_student = mysqli_fetch_assoc($result_students)) {
        $students[] = $row_student;
    }
} else {
    echo "No approved students found for this event.";
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
        <h2>Attendance Sheet for <?php echo htmlspecialchars($event_details['name']); ?> (<?php echo htmlspecialchars($event_details['date']); ?>)</h2>

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
            // Loop through the students for the current event
            foreach ($students as $student) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($student['roll_number']) . "</td>";
                echo "<td>" . htmlspecialchars($student['name']) . "</td>";
                echo "<td>" . htmlspecialchars($student['course_name']) . "</td>";
                echo "<td>20" . substr($student['roll_number'], 0, 2) . "</td>"; // Extract the first two digits of the roll number
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
