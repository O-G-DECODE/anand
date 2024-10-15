<?php
include("connection.php");
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the event data from the form
    if (isset($_POST['event_id']) && isset($_POST['name']) && isset($_POST['date']) && isset($_POST['period'])) {
        $event_id = $_POST['event_id'];
        $name = $_POST['name'];
        $date = $_POST['date'];
        $period = $_POST['period'];

        // Prepare the SQL query to update the event details
        $stmt = $conn->prepare("UPDATE event SET name = ?, date = ?, period = ? WHERE event_id = ?");
        if ($stmt) {
            $stmt->bind_param("sssi", $name, $date, $period, $event_id);

            // Execute the query and check if the update was successful
            if ($stmt->execute()) {
                // If the update was successful, show a success message and redirect
                echo "<script>
                        alert('Event updated successfully!');
                        window.location.href = 'event.php';
                      </script>";
            } else {
                // If there was an error with the query, display an error message
                echo "<script>
                        alert('Error updating event.');
                        window.location.href = 'edit_event.php?event_id=$event_id';
                      </script>";
            }

            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $conn->error;
        }
    } else {
        echo "<script>
                alert('All fields are required.');
                window.location.href = 'edit_event.php?event_id=$event_id';
              </script>";
    }
} else {
    // If the request method is not POST, redirect to the event page
    echo "<script>
            alert('Invalid request.');
            window.location.href = 'event.php';
          </script>";
}

$conn->close();
?>
