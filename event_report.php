<?php
include("connection.php"); // Database connection
session_start(); // Start the session

// Check if the event name is passed via POST
if (isset($_POST['event_name']) && !empty($_POST['event_name'])) {
    $event_name = $_POST['event_name'];

    // Fetch the event ID based on the event name
    $stmt = $conn->prepare("SELECT event_id FROM event WHERE name = ?");
    if ($stmt) {
        $stmt->bind_param("s", $event_name);
        $stmt->execute();
        $stmt->bind_result($event_id);
        $stmt->fetch();
        $stmt->close();

        // Check if event ID exists in the request table and if approved
        $stmt = $conn->prepare("SELECT approve FROM request WHERE event_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $stmt->bind_result($approve);
            $stmt->fetch();
            $stmt->close();

            // If event is approved, fetch the student details
            if ($approve > 0) {
                // Fetch the list of students who have added attendance for the event
                $stmt = $conn->prepare("
                    SELECT r.roll_number, s.name as student_name, c.name as course_name
                    FROM request r
                    JOIN student s ON r.roll_number = s.roll_number
                    JOIN course c ON s.course_id = c.course_id
                    WHERE r.event_id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $event_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                }
            } else {
                echo "No students have approved attendance for this event.";
            }
        }
    } else {
        echo "Error fetching event information.";
    }
} else {
    echo "No event selected. Please go back and select an event.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Report</title>
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --accent-color: #fd79a8;
            --background-color: #f9f9f9;
            --text-color: #2d3436;
            --card-background: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e4d3ea;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background-color: var(--card-background);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h3 {
            color: var(--primary-color);
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        .print-btn {
            margin-top: 20px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
            border-radius: 10px;
            text-transform: uppercase;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        @media print {
            body {
                font-family: Arial, sans-serif;
            }
            .container {
                max-width: 210mm;
                margin: 0 auto;
                padding: 20px;
                background-color: white;
                box-shadow: none;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Event Report for "<?php echo htmlspecialchars($event_name); ?>"</h3>

        <?php if (isset($result) && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Name</th>
                        <th>Course</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
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
