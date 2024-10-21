<?php
session_start();
include("connection.php");

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    die("You need to log in first.");
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
            <h3> <?php echo htmlspecialchars($student['student_name']); ?></h3>
            <p>Roll Number: <?php echo htmlspecialchars($student['roll_number']); ?></p>
            <p>Department: <?php echo htmlspecialchars($student['department_name']); ?></p>
            <p>Course: <?php echo htmlspecialchars($student['course_name']); ?></p>

            <?php
            // Fetch events participated by the student and the club that created the event
            $sql = "SELECT e.name as event_name, e.period, e.date, cl.name as club_name
                    FROM request r
                    JOIN event e ON r.event_id = e.event_id
                    JOIN staff st ON e.staff_id = st.staff_id
                    JOIN club cl ON st.club_id = cl.club_id
                    WHERE r.roll_number = ? AND r.approve > 0"; // Fetch only approved events
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $student['roll_number']);
            $stmt->execute();
            $events_result = $stmt->get_result();

            if ($events_result->num_rows > 0) {
                echo "<h4>Events participated:</h4>";
                echo "<table>";
                echo "<tr><th>Event Name</th><th>Period</th><th>Date</th><th>Club</th></tr>";
                while ($event = $events_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($event['event_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($event['period']) . "</td>";
                    echo "<td>" . htmlspecialchars($event['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($event['club_name']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No approved events found for this student.</p>";
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
