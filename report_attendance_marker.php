<?php
// Start session and include the connection file
session_start();
include 'connection.php';

if (!isset($_SESSION['email'])) {
    echo "Please log in to view the report.";
    exit;
}

// Retrieve email from session
$email = $_SESSION['email'];

// Fetch the department_id of the staff based on their email
$dept_query = "SELECT department_id FROM staff WHERE email = ?";
$stmt = $conn->prepare($dept_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($department_id);
$stmt->fetch();
$stmt->close();

if (!$department_id) {
    echo "No department found for this staff.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Staff Report</title>
    <link rel="stylesheet" href="report_page_style.css">
</head>
<body>
    <div class="container">
    <div class="form-group">
        <h3>Department Report</h3>

        <!-- Form to select date range -->
        <form action="department_staff_report.php" method="post">
            <input type="hidden" name="department_id" value="<?php echo htmlspecialchars($department_id); ?>">
            
            <!-- Date range selection -->
            <label for="from_date">From Date</label>
            <input type="date" id="from_date" name="from_date" required>
            
            <label for="to_date">To Date</label>
            <input type="date" id="to_date" name="to_date" required>

            <button type="submit">Generate Report</button>
        </form>
    </div> </div>
</body>
</html>
