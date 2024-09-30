<?php
include("connection.php");
session_start(); // Ensure the session is started to access session data

// Check if event_id is provided via POST
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Update the approve column to 1 for all students associated with this event
    $stmt = $conn->prepare("UPDATE request SET approve = 1, status = 'approved' WHERE event_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $event_id); // Bind event_id as integer
        if ($stmt->execute()) {
            // Redirect back to the review event page after approval
            header("Location: review_event.php?event_id=" . urlencode($event_id));
            exit();
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "Invalid request. No event ID provided.";
}

// Close connection
$conn->close();
?>
