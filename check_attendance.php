<?php
session_start();
include("connection.php");

// Check if the roll number and event_id are set
if (isset($_SESSION['roll_number']) && isset($_POST['event_id'])) {
    $roll_number = $_SESSION['roll_number'];
    $event_id = $_POST['event_id'];

    // Step 1: Check if the request exists and is approved
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
            exit();
        } else {
            // Step 2: If not approved, check if the event has expired
            $query = "SELECT create_date FROM event WHERE event_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $create_date = new DateTime($row['create_date']);
                $current_date = new DateTime();
                
                // Calculate the difference between create_date and current_date
                $interval = $create_date->diff($current_date);
                $days_difference = $interval->days;

                if ($days_difference > 7) {
                    // Event is expired (created more than 7 days ago)
                    echo "<script>
                        alert('Event has expired for attendance.');
                        window.location.href = 'student_page.php';
                    </script>";
                    exit();
                } else {
                    // Event is still valid, proceed to attendance
                    header("Location: attendance.php?event_id=" . urlencode($event_id));
                    exit();
                }
            } else {
                // Event not found
                echo "<script>
                    alert('Event not found.');
                    window.location.href = 'student_page.php';
                </script>";
                exit();
            }
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
