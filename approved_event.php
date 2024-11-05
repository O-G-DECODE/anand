<?php
// approved_event.php

// Include the database connection
include 'connection.php';

// Retrieve date range from the form
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;

// Prepare the SQL query to retrieve approved events within the date range
$sql = "
    SELECT DISTINCT event.event_id, event.name AS event_name, event.date, event.period, club.name AS club_name
    FROM event
    JOIN request ON event.event_id = request.event_id
    JOIN staff ON event.staff_id = staff.staff_id
    JOIN club ON staff.club_id = club.club_id
    WHERE request.approve > 0
    AND event.date BETWEEN ? AND ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from_date, $to_date);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Events Report</title>
    <link rel="stylesheet" href="report_style.css">
</head>
<body>

<div class="container">
    <h2>MES COLLEGE MARAMPALLY</h2>
    <h5>Approved Events from <?php echo htmlspecialchars($from_date); ?> to <?php echo htmlspecialchars($to_date); ?></h5>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Period</th>
                    <th>Club Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['period']); ?></td>
                        <td><?php echo htmlspecialchars($row['club_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No approved events found for the selected date range.</p>
    <?php endif; ?>

    <button class="print-btn" onclick="window.print()">Print Report</button>
</div>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>

</body>
</html>
