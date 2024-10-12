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

/*/ Check if the query was successful
if (mysqli_num_rows($result) > 0) {
    echo "Department ID matches the approve field in the request table.";
}
/ */
// Retrieve the details of the matching event_id
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
?>
<!DOCTYPE html>
<head>
    <title>Event Details</title>
    <style>
        :root {
  --primary-color: #6C5CE7; /* Vibrant purple */
  --secondary-color: #7F9CF5; /* Light purple */
  --accent-color: #ED64A6; /* Vibrant pink */
  --background-color: #e4d3ea; /* Light lavender background */
  --text-color: #2d3436; /* Dark gray for text */
  --card-background: #ffffff; /* White for card backgrounds */
  --button-hover-darken: rgba(0, 0, 0, 0.1); /* Subtle darkening effect */
}

body {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  background-color: var(--background-color); /* Use defined background color */
  font-family: 'Poppins', sans-serif; /* Modern font */
  margin: 0;
  padding: 20px;
}

.event-container {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 280px; /* Slightly wider for more content */
  height: 220px; /* Adjusted height for proportions */
  margin: 15px;
  padding: 20px;
  border: none; /* No border */
  border-radius: 15px; /* More rounded corners */
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); /* Softer shadow */
  background-color: var(--card-background); /* Clean white background */
  transition: transform 0.3s, box-shadow 0.3s; /* Smoother scaling and shadow */
}

.event-container:hover {
  transform: scale(1.05); /* Slight enlarge effect */
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15); /* Shadow intensifies on hover */
}

h3 {
  margin: 0 0 15px;
  font-size: 1.3em;
  color: var(--primary-color); /* Use primary color for headings */
}

.review-btn {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); /* Gradient background */
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px; /* Slightly larger font */
  margin-top: auto; /* Push to the bottom */
  transition: background 0.3s, transform 0.2s ease; /* Smooth transitions */
  text-transform: uppercase; /* Uppercase for bold effect */
  letter-spacing: 1px;
}

.review-btn:hover {
  background: linear-gradient(135deg, #5b4fd1, #6c5ce7); /* Darker gradient on hover */
  transform: translateY(-3px); /* Lift effect on hover */
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Slight shadow on hover */
}

    </style>
</head>
<body>
</body>
</html>
