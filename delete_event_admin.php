<?php
include("connection.php");
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    echo "No user is logged in.";
    exit;
}

// Retrieve the event_id from the POST request
if (isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM event WHERE event_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $event_id);
        if ($stmt->execute()) {
            // Redirect back to the events page
            header("Location: review_event_admin.php");
            exit;
        } else {
            echo "Error executing query: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No event ID provided.";
}

// Close connection
$conn->close();
?>
