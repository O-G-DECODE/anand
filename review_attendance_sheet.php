<?php
// Include the database connection file
include 'connection.php';

// Start the session
session_start();

// Check if the staff_id is set in the session
if (!isset($_SESSION['staff_id'])) {
    header('Location: staff_page.php');
    exit;
}

// Get the staff_id from the session
$staff_id = $_SESSION['staff_id'];
//echo $staff_id;

// Query to fetch the department_id from the staff table
$query = "SELECT department_id FROM staff WHERE staff_id = '$staff_id'";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $department_id = $row['department_id'];
} else {
    echo "Error: Staff ID not found in the staff table.";
    exit;
}

// Query to check if the department_id matches the approve field in the request table
$query = "SELECT * FROM request WHERE approve = '$department_id'";
$result = mysqli_query($conn, $query);

// Retrieve the details of the matching event_id
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $event_id = $row['event_id'];
        $query = "SELECT * FROM event WHERE event_id = '$event_id'";
        $event_result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($event_result) > 0) {
            $event_row = mysqli_fetch_assoc($event_result);
            ?>
            <div class="event-container">
                <h3><?= htmlspecialchars($event_row['name']) ?></h3>
                <p><strong>Date:</strong> <?= htmlspecialchars($event_row['date']) ?></p>
                <p><strong>Period:</strong> <?= htmlspecialchars($event_row['period']) ?></p>
                <button class="review-btn" onclick="location.href='attendance_sheet.php?event_id=<?= $event_id ?>'">Attendance Sheet</button>
            </div>
            <?php
        } else {
            echo " Event ID not found in the event table.";
        }
    }
} else {
    echo " <b>No Attendance sheets requested.</b>";
}
?>
<html>
<head>
    <title>Event Details</title>
    <style>
        body {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Center the containers */
            background-color: #f9f9f9; /* Light background */
            font-family: Arial, sans-serif; /* Font styling */
        }

        .event-container {
            display: flex;
            flex-direction: column; /* Vertical stacking */
            justify-content: space-between; /* Space out content */
            width: 250px; /* Set a fixed width */
            height: 200px; /* Set a fixed height */
            margin: 15px; /* Margin for spacing */
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff; /* White background */
            transition: transform 0.2s; /* Smooth scaling effect */
        }

        .event-container:hover {
            transform: scale(1.05); /* Slightly enlarge on hover */
        }

        h3 {
            margin: 0 0 10px; /* Adjust heading margin */
            font-size: 1.2em; /* Font size for heading */
        }

        .review-btn {
            background-color: #4CAF50;
            color: #fff;
            padding: 8px 12px; /* Padding for button */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px; /* Button font size */
            margin-top: auto; /* Align at the bottom */
            transition: background-color 0.3s; /* Smooth background transition */
        }

        .review-btn:hover {
            background-color: #3e8e41; /* Darker green on hover */
        }
    </style>
</head>
<body>
</body>
</html>
