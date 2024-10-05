<?php
session_start();
include("connection.php");

// Check if the roll number and event_id are set
if (isset($_SESSION['roll_number']) && isset($_POST['event_id'])) {
    $roll_number = $_SESSION['roll_number'];
    $event_id = $_POST['event_id'];

    // Prepare and execute the query to check if the request exists and is approved
    $stmt = $conn->prepare("SELECT approve FROM request WHERE roll_number = ? AND event_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $roll_number, $event_id);
        $stmt->execute();
        $stmt->bind_result($approve);
        $stmt->fetch();
        $stmt->close();

        if ($approve !== null) {
            // Request exists and is approved
            echo "<script>alert('You have already added attendance and it is approved.');</script>";
            echo "<meta http-equiv='refresh' content='0;url=student_page.php'>"; // Redirect back
        } else {
            // Request doesn't exist or isn't approved, proceed to attendance
            header("Location: attendance.php?event_id=" . urlencode($event_id));
            exit();
        }
    } else {
        echo "Error preparing statement: " . htmlspecialchars($conn->error);
    }
} else {
    echo "Roll number or event ID is not set.";
}

// Close connection
$conn->close();
?>
