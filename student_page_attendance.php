<?php
session_start();
include("connection.php");

// Initialize variables
$student_name = "Student";

// Check if the roll number is in the session
if (isset($_SESSION['roll_number'])) {
    $roll_number = $_SESSION['roll_number'];

    // Query to check if the roll number exists in the request table
    $stmt = $conn->prepare("SELECT r.event_id, e.name, e.date, e.period, r.approve 
                            FROM request r
                            JOIN event e ON r.event_id = e.event_id
                            WHERE r.roll_number = ?");
    $stmt->bind_param("i", $roll_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Student is in the request table
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Student Event Details</title>
            <link rel="stylesheet" type="text/css" href="your_stylesheet.css">
            <style>
                <?php echo file_get_contents("style_student_page.css"); ?>

                /* Custom style for status colors */
                .status-approved {
                    color: green;
                    font-weight: bold;
                }

                .status-pending {
                    color: red;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Event Attendance Details</h2>
                <div class="event-list">
                    <table>
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Period</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Loop through each event and show its details
                            while ($row = $result->fetch_assoc()) {
                                $event_name = htmlspecialchars($row['name']);
                                $event_date = htmlspecialchars($row['date']);
                                $event_period = htmlspecialchars($row['period']);
                                $approve_status = $row['approve'];

                                // Determine the status
                                $status_class = ($approve_status > 0) ? "status-approved" : "status-pending";
                                $status_text = ($approve_status > 0) ? "Approved" : "Pending";
                                
                                echo "<tr>
                                        <td>$event_name</td>
                                        <td>$event_date</td>
                                        <td>$event_period</td>
                                        <td class='$status_class'>$status_text</td>
                                      </tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </body>
        </html>

        <?php
    } else {
        echo "No events found for this student.";
    }

    $stmt->close();
} else {
    echo "No student is logged in.";
}
$conn->close();
?>
