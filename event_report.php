<?php
include("connection.php"); // Database connection
session_start(); // Start the session

// Check if the event ID is passed via POST
if (isset($_POST['event_id']) && !empty($_POST['event_id'])) { // Changed from event_name to event_id
    $event_id = $_POST['event_id'];

    // Fetch the event details and the club name
    $stmt = $conn->prepare("
        SELECT e.name AS event_name, e.date, e.period, c.name AS club_name
        FROM event e
        JOIN staff s ON e.staff_id = s.staff_id
        JOIN club c ON s.club_id = c.club_id
        WHERE e.event_id = ?
    ");
    if ($stmt) {
        $stmt->bind_param("i", $event_id); // Using event_id to fetch event details
        $stmt->execute();
        $stmt->bind_result($event_name, $event_date, $event_period, $club_name);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Error fetching event details.";
        exit;
    }

    // Fetch students who have approved attendance, along with their course and department
    $stmt = $conn->prepare("
        SELECT r.roll_number, s.name AS student_name, c.name AS course_name, d.name AS department_name
        FROM request r
        JOIN student s ON r.roll_number = s.roll_number
        JOIN course c ON s.course_id = c.course_id
        JOIN department d ON c.department_id = d.department_id
        WHERE r.event_id = ? AND r.approve > 0
    ");
    if ($stmt) {
        $stmt->bind_param("i", $event_id); // Using event_id to fetch student details
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo "Error fetching student attendance details.";
        exit;
    }
} else {
    echo "No event selected. Please go back and select an event.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Report</title>
   <link rel="stylesheet" href="report_style.css">
</head>
<body>
    <div class="container">
         echo"<img src='mes-logo.webp' alt='Logo'>";
        <h3><?php echo htmlspecialchars($event_name); ?></h3>
        <p>Date: <?php echo htmlspecialchars($event_date); ?></p>
        <p>Period: <?php echo htmlspecialchars($event_period); ?></p>
        <p>Club: <?php echo htmlspecialchars($club_name); ?></p>

        <?php if (isset($result) && $result->num_rows > 0): ?>
            <h4>Students who attended:</h4>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No students have added attendance for this event.</p>
        <?php endif; ?>

        <button class="print-btn" onclick="window.print()">Print Report</button>
    </div>
</body>
</html>
