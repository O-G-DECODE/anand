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
            <h1>Attendance Sheet </h1>
        <table> 
            
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
