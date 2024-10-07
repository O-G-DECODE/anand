<?php
session_start();
include("connection.php");

// Initialize variables
$student_name = "Student";
$club_id = null;

// Check if the roll number is in the session
if (isset($_SESSION['roll_number'])) {
    $roll_number = $_SESSION['roll_number'];

    // Prepare and execute the query to fetch student details
    $stmt = $conn->prepare("SELECT name, club_id FROM student WHERE roll_number = ?");
    if ($stmt) {
        $stmt->bind_param("s", $roll_number);
        $stmt->execute();
        $stmt->bind_result($name, $club_id);
        if ($stmt->fetch()) {
            $student_name = htmlspecialchars($name); // Sanitize output
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . htmlspecialchars($conn->error);
    }
} else {
    echo "Roll number is not set in session.";
}

// Fetch events related to the student's club, if applicable
$events = [];
if ($club_id > 0) {
    // First, find all staff IDs associated with the club
    $staff_ids = [];
    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE club_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $club_id);
        $stmt->execute();
        $stmt->bind_result($staff_id);
        while ($stmt->fetch()) {
            $staff_ids[] = $staff_id;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . htmlspecialchars($conn->error);
    }

    // Fetch events for these staff members
    if (!empty($staff_ids)) {
        // Convert staff_ids array to a comma-separated string for SQL IN clause
        $staff_ids_str = implode(',', $staff_ids);
        $stmt = $conn->prepare("SELECT e.name, e.date, e.period, e.event_id FROM event e WHERE e.staff_id IN ($staff_ids_str)");
        if ($stmt) {
            $stmt->execute();
            $stmt->bind_result($event_name, $event_date, $event_period, $event_id);
            while ($stmt->fetch()) {
                $events[] = [
                    'name' => htmlspecialchars($event_name),
                    'date' => htmlspecialchars($event_date),
                    'period' => htmlspecialchars($event_period),
                    'event_id' => $event_id // Add event_id to the event array
                ];
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . htmlspecialchars($conn->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style_student_page.css">
</head>
<body>
    <div class="header">
        <div class="student-name"><?php echo htmlspecialchars($student_name); ?></div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <div class="container">
        <h2>Your Events</h2>
        <?php if (!empty($events)): ?>
            <div class="event-list">
                <table>
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Period</th>
                            <th>Action</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['name']); ?></td>
                                <td><?php echo htmlspecialchars($event['date']); ?></td>
                                <td><?php echo htmlspecialchars($event['period']); ?></td>
                                <td>
                                    <form method="post" action="check_attendance.php">
                                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['event_id']); ?>">
                                        <button type="submit">Add Attendance</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No upcoming events found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
