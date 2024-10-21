<?php
session_start();
include("connection.php");

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    die("You need to log in first.");
}

// Fetch the staff_id based on the logged-in staff email
$email = $_SESSION['email'];
$staff_id = null;

$sql = "SELECT staff_id FROM staff WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $staff_id = $row['staff_id'];
} else {
    die("Staff not found.");
}

// If form is submitted with a selected student name
if (isset($_POST['student_name_selected'])) {
    $student_name = $_POST['student_name_selected'];

    // Fetch student details
    $sql = "SELECT s.name as student_name, s.roll_number, c.name as course_name, d.name as department_name
            FROM student s
            JOIN course c ON s.course_id = c.course_id
            JOIN department d ON c.department_id = d.department_id
            WHERE s.name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_name);
    $stmt->execute();
    $student_result = $stmt->get_result();

    if ($student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Student Report</title>
           <link rel="stylesheet" href="report_style.css">
        </head>
        <body>
        <div class="container">
            <h3>Student Report for: <?php echo htmlspecialchars($student['student_name']); ?></h3>
            <p>Roll Number: <?php echo htmlspecialchars($student['roll_number']); ?></p>
            <p>Department: <?php echo htmlspecialchars($student['department_name']); ?></p>
            <p>Course: <?php echo htmlspecialchars($student['course_name']); ?></p>

            <?php
            // Fetch events participated by the student under this staff's supervision
            $sql = "SELECT e.name as event_name, e.date
                    FROM request r
                    JOIN event e ON r.event_id = e.event_id
                    WHERE r.roll_number = ? AND e.staff_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $student['roll_number'], $staff_id);
            $stmt->execute();
            $events_result = $stmt->get_result();

            if ($events_result->num_rows > 0) {
                echo "<h4>Events participated:</h4>";
                echo "<ul>";
                while ($event = $events_result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($event['event_name']) . " on " . htmlspecialchars($event['date']) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No events found for this student under your supervision.</p>";
            }
            ?>

            <button class="print-btn" onclick="window.print()">Print Report</button>
        </div>
        </body>
        </html>
        <?php
    } else {
        echo "<p>Student not found.</p>";
    }
}
?>