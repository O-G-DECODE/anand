<?php
include("connection.php");
session_start(); // Ensure the session is started to access session data

// Retrieve the email from the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Fetch the staff_id of the staff member from the staff table
    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($staff_id);
        $stmt->fetch();
        $stmt->close();

        // Fetch events created by this staff member
        if (isset($_GET['event_id'])) {
            $event_id = $_GET['event_id'];

            // Check if the "Approve All" button was clicked
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_all'])) {
                $event_id = $_POST['event_id'];

                // Check if any request for this event is already approved
                $stmt = $conn->prepare("SELECT COUNT(*) FROM request WHERE event_id = ? AND approve IS NOT NULL");
                if ($stmt) {
                    $stmt->bind_param("i", $event_id);
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();

                    if ($count > 0) {
                        // Display message that approval already exists
                        echo "<script>alert('You have already approved some requests for this event.');</script>";
                    } else {
                        // New logic: Fetch the event creation date from the 'day' table where type = 1
                        $stmt = $conn->prepare("SELECT `date` FROM day WHERE event_id = ? AND type = 1");
                        if ($stmt) {
                            $stmt->bind_param("i", $event_id);
                            $stmt->execute();
                            $stmt->bind_result($event_date);
                            $stmt->fetch();
                            $stmt->close();

                            if ($event_date) {
                                // Convert event_date to DateTime and get the current date
                                $create_date = new DateTime($event_date);
                                $current_date = new DateTime();
                                
                                // Calculate the difference in days
                                $interval = $create_date->diff($current_date);
                                $days_difference = $interval->days;

                                if ($days_difference > 7) {
                                    // Event is expired (created more than 7 days ago)
                                    echo "<script>
                                        alert('Event has expired.');
                                        window.location.href = 'event.php';
                                    </script>";
                                    exit(); // Stop further execution if event is expired
                                } else {
                                    // Event is still valid, proceed to further approval steps
                                    
                                    // Insert the current date and event_id into the 'day' table with type=2
                                    $stmt = $conn->prepare("INSERT INTO day (date, type, event_id) VALUES (CURDATE(), 2, ?)");
                                    if ($stmt) {
                                        $stmt->bind_param("i", $event_id);
                                        if ($stmt->execute()) {
                                            // Get the inserted date_id
                                            $date_id = $stmt->insert_id;

                                            // Update the approve column to department_id for all students associated with the event
                                            // And insert the date_id into the request table
                                            $stmt = $conn->prepare("UPDATE request r
                                                                    JOIN student s ON r.roll_number = s.roll_number
                                                                    JOIN course c ON s.course_id = c.course_id
                                                                    SET r.approve = c.department_id, r.date_id = ?
                                                                    WHERE r.event_id = ?");
                                            if ($stmt) {
                                                $stmt->bind_param("ii", $date_id, $event_id);
                                                if ($stmt->execute()) {
                                                    // Display success message as a JavaScript alert
                                                    echo "<script>alert('All students have been approved successfully!');</script>";
                                                    // Redirect back to the review event page after approval
                                                    echo "<meta http-equiv='refresh' content='0;url=event.php?event_id=" . urlencode($event_id) . "'>";
                                                    exit();
                                                } else {
                                                    echo "Error executing query: " . $stmt->error;
                                                }
                                                $stmt->close();
                                            } else {
                                                echo "Error preparing statement: " . $conn->error;
                                            }
                                        } else {
                                            echo "Error executing query: " . $stmt->error;
                                        }
                                        $stmt->close();
                                    } else {
                                        echo "Error preparing statement: " . $conn->error;
                                    }
                                }
                            } else {
                                echo "No creation date found for this event.";
                            }
                        } else {
                            echo "Error preparing statement: " . $conn->error;
                        }
                    }
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            }

            // Fetch the list of students who have added attendance for the event
            $stmt = $conn->prepare("
                SELECT r.roll_number, s.name as student_name, c.name as course_name
                FROM request r
                JOIN student s ON r.roll_number = s.roll_number
                JOIN course c ON s.course_id = c.course_id
                WHERE r.event_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Event</title>
    <link rel="stylesheet" type="text/css" href="event_style.css">
</head>
<body>
    <div class="container">
        <h2>Review Event</h2>
        <!-- Approve All Button -->
        <form method="post" action="" style="margin-bottom: 20px;">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
            <button type="submit" name="approve_all" class="btn-approve-all">Approve All</button>
        </form>
        
        <table>
            <caption>Students who have added attendance</caption>
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
<?php
                while ($row = $result->fetch_assoc()) {
                    $roll_number = htmlspecialchars($row['roll_number']);
                    $student_name = htmlspecialchars($row['student_name']);
                    $course_name = htmlspecialchars($row['course_name']);
                    echo "<tr>
                            <td>$roll_number</td>
                            <td>$student_name</td>
                            <td>$course_name</td>
                            <td>
                                <form method='post' action='remove_student.php' style='display:inline;'>
                                    <input type='hidden' name='event_id' value='$event_id'>
                                    <input type='hidden' name='roll_number' value='$roll_number'>
                                    <button type='submit' class='btn-remove'>Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "No event ID specified.";
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No user is logged in.";
}

// Close connection
$conn->close();
?>
