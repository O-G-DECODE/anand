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
    $from_date = $_POST['from_date_student'];
    $to_date = $_POST['to_date_student'];

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
           <img src="mes-logo.webp" alt="Logo">
            <h3> <?php echo htmlspecialchars($student['student_name']); ?></h3>
            <p>Roll Number: <?php echo htmlspecialchars($student['roll_number']); ?></p>
            <p>Department: <?php echo htmlspecialchars($student['department_name']); ?></p>
            <p>Course: <?php echo htmlspecialchars($student['course_name']); ?></p>

            <?php
            // Fetch events participated by the student under this staff's supervision within the date range
            $sql = "SELECT e.name as event_name, e.date, e.period
                    FROM request r
                    JOIN event e ON r.event_id = e.event_id
                    WHERE r.roll_number = ? AND e.staff_id = ? AND e.date BETWEEN ? AND ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiss", $student['roll_number'], $staff_id, $from_date, $to_date);
            $stmt->execute();
            $events_result = $stmt->get_result();

            if ($events_result->num_rows > 0) {
                echo "<h4>Events participated between $from_date and $to_date:</h4>";
                echo "<table>";
                echo "<tr><th>Event Name</th><th>Period</th><th>Date</th></tr>";
                while ($event = $events_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($event['event_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($event['period']) . "</td>";
                    echo "<td>" . htmlspecialchars($event['date']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No events found for this student under your supervision between $from_date and $to_date.</p>";
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
