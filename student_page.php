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
        $stmt = $conn->prepare("SELECT e.name, e.date, e.period FROM event e WHERE e.staff_id IN ($staff_ids_str)");
        if ($stmt) {
            $stmt->execute();
            $stmt->bind_result($event_name, $event_date, $event_period);
            while ($stmt->fetch()) {
                $events[] = [
                    'name' => htmlspecialchars($event_name),
                    'date' => htmlspecialchars($event_date),
                    'period' => htmlspecialchars($event_period)
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .student-name {
            flex-grow: 1;
            text-align: center;
            font-size: 18px;
        }
        .header .logout-button {
            background-color: #e74c3c;
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .header .logout-button:hover {
            background-color: #c0392b;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            background-color: #008CBA;
            color: #fff;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #005f73;
        }
        .event-list {
            margin-top: 20px;
        }
        .event-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .event-list table, .event-list th, .event-list td {
            border: 1px solid #ddd;
        }
        .event-list th, .event-list td {
            padding: 8px;
            text-align: left;
        }
        .event-list th {
            background-color: #4CAF50; /* green background for table header */
            color: #fff;
        }
    </style>
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
                                <td><a href="attendance.php?event_name=<?php echo urlencode($event['name']); ?>"><button>Add Attendance</button></a></td><!-- Placeholder button for marking attendance -->
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
