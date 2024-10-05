<?php
// Include the database connection file
include 'connection.php';

// Query to get all approved requests
$query = "
    SELECT r.event_id, e.name, e.date, e.period
    FROM request r
    JOIN event e ON r.event_id = e.event_id
    WHERE r.approve = 1
";
$result = mysqli_query($conn, $query);

// Retrieve the details of the approved events
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $event_id = $row['event_id'];
        ?>
        <div class="event-container">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><strong>Date:</strong> <?= htmlspecialchars($row['date']) ?></p>
            <p><strong>Period:</strong> <?= htmlspecialchars($row['period']) ?></p>
            <button class="review-btn" onclick="location.href='approved_attendance_admin.php?event_id=<?= $event_id ?>'">Attendance Sheet</button>
        </div>
        <?php
    }
} else {
    echo "<b>No Attendance sheets requested.</b>";
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
