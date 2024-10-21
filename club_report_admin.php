<?php
include("connection.php"); // Database connection
session_start(); // Start the session

// Handle form submission for report generation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the club_id and date range from the form submission
    $club_id = $_POST['club_id'] ?? null;
    $from_date = $_POST['start_date'] ?? null;
    $to_date = $_POST['end_date'] ?? null;

    // Validate inputs
    if (empty($club_id) || empty($from_date) || empty($to_date)) {
        echo "Please select a club and a valid date range.";
    } else {
        // Fetch the club name for display
        $stmt = $conn->prepare("SELECT name FROM club WHERE club_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $club_id);
            $stmt->execute();
            $stmt->bind_result($club_name);
            $stmt->fetch();
            $stmt->close();
        }

        // Fetch events for the selected club and date range
        $sql = "SELECT e.event_id, e.name, e.date, e.period, 
                       (SELECT COUNT(*) FROM request r WHERE r.event_id = e.event_id AND r.approve > 0) AS participant_count
                FROM event e
                WHERE e.staff_id IN (SELECT staff_id FROM staff WHERE club_id = ?)
                AND e.date BETWEEN ? AND ?";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iss", $club_id, $from_date, $to_date);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if any events were found
            if ($result->num_rows > 0) {
                echo "<div class='container'>";
                echo "<h3>Club Report for " . htmlspecialchars($club_name) . "</h3>";
                echo "<h4>From: " . htmlspecialchars($from_date) . " To: " . htmlspecialchars($to_date) . "</h4>";
                echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%;'>";
                echo "<tr><th>Event Name</th><th>Date</th><th>Period</th><th>Number of Students Participated</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    // Only show events with approved participants
                    if ($row['participant_count'] > 0) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['date']) . "</td>
                                <td>" . htmlspecialchars($row['period']) . "</td>
                                <td>" . htmlspecialchars($row['participant_count']) . "</td>
                              </tr>";
                    }
                }

                echo "</table>";
                echo "<button class='print-btn' onclick='window.print();'>Print Report</button>";
                echo "</div>";
            } else {
                echo "<div class='container'><h3>No events found for the selected club and date range.</h3></div>";
            }

            $stmt->close();
        } else {
            echo "Error fetching events.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Report</title>
    <link rel="stylesheet" href="report_style.css">
</head>
</html>
