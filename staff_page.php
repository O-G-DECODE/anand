<?php
include("connection.php");
session_start(); // Start the session to access session data

// Initialize staff name, ID, and club name
$staff_name = "Staff Member";
$staff_id = null;
$club_name = null; // Initialize club name
$club_id = null; // Initialize club_id

// Fetch the staff name, ID, and club name based on the email in the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    // Prepare and execute the query to fetch staff name, ID, and club name
    $stmt = $conn->prepare("
        SELECT s.name, s.staff_id, c.name, c.club_id 
        FROM staff s 
        LEFT JOIN club c ON s.club_id = c.club_id 
        WHERE s.email = ?
    ");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($name, $id, $club, $club_id);
        if ($stmt->fetch()) {
            $staff_name = htmlspecialchars($name); // Sanitize output
            $staff_id = $id;
            $club_name = htmlspecialchars($club); // Sanitize output
            $_SESSION['staff_id'] = $staff_id; // Store staff_id in session
            $_SESSION['club_id'] = $club_id; // Store club_id in session
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form was submitted to create an event
    if (isset($_POST["submit"])) {
        // Get form data with validation
        $event_name = isset($_POST["event_name"]) ? trim($_POST["event_name"]) : '';
        $event_date = isset($_POST["event_date"]) ? trim($_POST["event_date"]) : '';
        $event_period = isset($_POST["event_period"]) ? $_POST["event_period"] : [];

        // Convert the period array to a comma-separated string
        $event_period_string = implode(',', $event_period);

        // Check if all fields are filled
        if (empty($event_name) || empty($event_date) || empty($event_period_string)) {
            echo "Please fill all fields!";
        } else {
            // Get current date (YYYY-MM-DD) for the create_date field
            $currentDate = date('Y-m-d');

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO event (name, date, period, staff_id, create_date) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                // Bind parameters including the current date
                $stmt->bind_param("sssis", $event_name, $event_date, $event_period_string, $staff_id, $currentDate);

                // Execute statement
                if ($stmt->execute()) {
                    echo "Event created successfully!";
                } else {
                    echo "Error: " . $stmt->error;
                }

                // Close statement
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Profile - Event Creation</title>
    <link rel="stylesheet" href="style_staff_page.css">
</head>
<body>
<div class="header">
    <div>
        <span><?php echo $staff_name; ?></span>
        <br>
    </div>

    <!-- Only show these options if the staff belongs to a club -->
    <?php if ($club_id > 0): ?>
    <div class="profile-options">
        <a href="add_volunters.php">Add <?php echo $club_name; ?> Student</a>
        <a href="remove_volunters.php"> Remove <?php echo $club_name; ?> Student</a>
        <a href="event.php">Events</a>
        <a href="reports_staff.php">Reports</a>
    </div>
    <?php endif; ?>
    <div class="profile-options"><a href="review_attendance_sheet.php">Attendance Sheets</a></div>
    <div class="profile-options"><a href="edit_staff_profile.php">Edit Profile</a></div>
    <div class="profile-options"><a href="report_attendance_marker.php">Department Report</a></div>
    <a href="logout.php" class="logout-button">Logout</a>
</div>

<div class="container">
    <h2>Create New Event</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="event-name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" required>
        </div>
        <div class="form-group">
            <label for="event-date">Date:</label>
            <input type="date" id="event_date" name="event_date" required>
        </div>
        <div class="form-group">
            <label for="event-period">Period:</label>
            <select id="event_period" name="event_period[]" multiple required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" name="submit">Create Event</button>
        </div>
    </form>
</div>
</body>
</html>
