<?php
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll_number = $_POST['roll_number'];
    $event_id = $_POST['event_id'];

    // Insert into the reject table
    $stmt = $conn->prepare("INSERT INTO reject (roll_number, event_id) VALUES (?, ?)");
    $stmt->bind_param("si", $roll_number, $event_id);

    if ($stmt->execute()) {
        // If insertion is successful, delete the entry from the request table
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM request WHERE roll_number = ? AND event_id = ?");
        $stmt->bind_param("si", $roll_number, $event_id);

        if ($stmt->execute()) {
            // Show a success message and go back
            echo "<script>
                    alert('Rejection of student is completed.');
                    window.history.back();
                  </script>";
        } else {
            echo "<script>
                    alert('Error: Unable to complete the rejection process.');
                    window.history.back();
                  </script>";
        }
    } else {
        echo "<script>
                alert('Error: Unable to complete the rejection process.');
                window.history.back();
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>