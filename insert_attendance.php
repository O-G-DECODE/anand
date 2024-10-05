<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include("connection.php");

// Handle the AJAX request if event_id and rollNumbers are provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id']) && isset($_POST['rollNumbers'])) {
    $eventId = $_POST['event_id'];
    $rollNumbers = json_decode($_POST['rollNumbers'], true);

    if (is_array($rollNumbers) && !empty($eventId)) {
        // Prepare the SQL query
        $sql = "INSERT INTO request (roll_number, event_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare statement failed: " . $conn->error);
            echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
            exit();
        }

        // Begin transaction
        $conn->begin_transaction();

        try {
            foreach ($rollNumbers as $rollNumber) {
                $stmt->bind_param("si", $rollNumber, $eventId);
                $stmt->execute();
            }

            // Commit transaction
            $conn->commit();

            echo json_encode(array('status' => 'success', 'message' => 'Records inserted successfully.'));
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            error_log("Insert failed: " . $e->getMessage());
            echo json_encode(array('status' => 'error', 'message' => 'Insert failed.'));
        }

        // Close connections
        $stmt->close();
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid data.'));
    }

    $conn->close();
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Required data missing.'));
}
?>
