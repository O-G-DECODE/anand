<?php
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the date range from the form
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Validate that both dates are provided
    if (!empty($from_date) && !empty($to_date)) {
        // Query to fetch rejected students' details within the specified event date range, sorted by event date
        $stmt = $conn->prepare("
            SELECT r.roll_number, 
                   s.name AS student_name,
                   e.name AS event_name, e.date AS event_date, e.period
            FROM reject r
            JOIN student s ON r.roll_number = s.roll_number
            JOIN event e ON r.event_id = e.event_id
            WHERE e.date BETWEEN ? AND ?
            ORDER BY e.date ASC
        ");
        $stmt->bind_param("ss", $from_date, $to_date);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo "<script>alert('Please select both from and to dates.');</script>";
        $result = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejected Student Report</title>
    <link rel="stylesheet" href="report_style.css">
</head>
<body>
    <div class="container">
        <h2>MES COLLEGE MARAMPALLY</h2>
        <h5>Rejected Student Report from <?php echo htmlspecialchars($from_date); ?> to <?php echo htmlspecialchars($to_date); ?></h5>

        <!-- Display Report if Results Exist -->
        <?php if (isset($result) && $result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Student Name</th>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Event Period</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['period']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif (isset($result)): ?>
            <p>No records found for the selected date range.</p>
        <?php endif; ?>

        <button class="print-btn" onclick="window.print()">Print Report</button>
    </div>

<?php
// Close statement and connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>

</body>
</html>
