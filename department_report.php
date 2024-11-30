<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Report</title>
    <link rel="stylesheet" href="report_style.css">
</head>
<body>
    <?php
    include 'connection.php'; // Include the database connection

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize input data
        $department_id = $_POST['department_name'];
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];

        // Fetch the department name
        $dept_query = "SELECT name FROM department WHERE department_id = ?";
        $stmt = $conn->prepare($dept_query);
        $stmt->bind_param("i", $department_id);
        $stmt->execute();
        $stmt->bind_result($department_name);
        $stmt->fetch();
        $stmt->close();

        // Fetch student details who participated in events
        $query = "
            SELECT r.roll_number, s.name AS student_name, e.name AS event_name, e.date, e.period, c.name AS club_name
            FROM request r
            JOIN student s ON r.roll_number = s.roll_number
            JOIN event e ON r.event_id = e.event_id
            JOIN staff st ON e.staff_id = st.staff_id
            JOIN club c ON st.club_id = c.club_id
            JOIN course cr ON s.course_id = cr.course_id
            WHERE cr.department_id = ? 
            AND r.approve > 0 
            AND e.date BETWEEN ? AND ?
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $department_id, $from_date, $to_date);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    ?>
    
    <div class="container">
       <img src="mes-logo.webp" alt="Logo">
        <?php if (isset($department_name)): ?>
            <h3><?php echo htmlspecialchars($department_name); ?></h3>
        <?php endif; ?>
        <p>From: <?php echo htmlspecialchars($from_date); ?> To: <?php echo htmlspecialchars($to_date); ?></p>

        <?php if (isset($result) && $result->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Student Name</th>
                        <th>Date</th>
                        <th>Period</th>
                        <th>Event Name</th>
                        <th>Club Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['period']); ?></td>
                            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['club_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found for the selected department and date range.</p>
        <?php endif; ?>

        <button class="print-btn" onclick="window.print()">Print Report</button>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
