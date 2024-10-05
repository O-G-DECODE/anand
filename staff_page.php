<?php
include("connection.php");
session_start(); // Start the session to access session data

// Initialize staff name and ID
$staff_name = "Staff Member";
$staff_id = null;

// Fetch the staff name and ID based on the email in the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    // Prepare and execute the query to fetch staff name and ID
    $stmt = $conn->prepare("SELECT name, staff_id FROM staff WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($name, $id);
        if ($stmt->fetch()) {
            $staff_name = htmlspecialchars($name); // Sanitize output
            $staff_id = $id;
            $_SESSION['staff_id'] = $staff_id; // Store staff_id in session
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
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO event (name, date, period, staff_id) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssi", $event_name, $event_date, $event_period_string, $staff_id);

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
        .header img {  
            border-radius: 50%; 
            width: 50px;
            height: 50px;
        }
        .header .profile-options {
            display: flex;
            gap: 10px;
        }
        .header .profile-options a,
        .header .logout-button {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .header .logout-button {
            background-color: #e74c3c;
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
    </style>
    
</head>
<body>
    <div class="header">
        <div>
            <!-- Replace with actual profile picture -->
            <span><?php echo $staff_name; ?></span>
            <br>
        </div>
        <div class="profile-options">
            <a href="#">View Profile</a>
            <a href="event.php">Events</a>
            <a href="#">Change Password</a>
            <a href="review_attendance_sheet.php">Attendance Sheets</a>

        </div>
        <a href="logout.php" class="logout-button">Logout</a> <!-- Updated to point to logout.php -->
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
</html>