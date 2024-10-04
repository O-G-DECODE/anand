<?php
// Start the session
session_start();

// Include the database connection file
include("connection.php");

// Check if 'event_id' is present in the URL
if (isset($_GET['event_id'])) {
    // Sanitize the input
    $event_id = htmlspecialchars($_GET['event_id']);
    
    // Store the event_id in the session
    $_SESSION['event_id'] = $event_id;

    // Prepare and execute the SQL query to fetch roll_numbers based on event_id
    $stmt = $conn->prepare("SELECT roll_number FROM request WHERE event_id = ?");
    $stmt->bind_param("s", $_SESSION['event_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are roll numbers for the event
    if ($result->num_rows > 0) {
        // Start the HTML table with styles
        echo "
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 1em;
                font-family: Arial, sans-serif;
                box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            }
            th, td {
                padding: 12px 15px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #4CAF50;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
        </style>
        <table>
            <tr><th>Roll Number</th><th>Name</th><th>Course</th><th>Department</th></tr>
        ";

        // Fetch each roll_number
        while ($row = $result->fetch_assoc()) {
            $roll_number = htmlspecialchars($row['roll_number']);

            // Now, fetch the name, course, and department for each roll_number
            $student_stmt = $conn->prepare("
                SELECT s.name AS student_name, c.name AS course_name, d.name AS department_name
                FROM student s
                JOIN course c ON s.course_id = c.course_id
                JOIN department d ON c.department_id = d.department_id
                WHERE s.roll_number = ?");
            $student_stmt->bind_param("s", $roll_number);
            $student_stmt->execute();
            $student_result = $student_stmt->get_result();

            // Check if there's a matching student
            if ($student_result->num_rows > 0) {
                while ($student_row = $student_result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $roll_number . "</td>
                            <td>" . htmlspecialchars($student_row['student_name']) . "</td>
                            <td>" . htmlspecialchars($student_row['course_name']) . "</td>
                            <td>" . htmlspecialchars($student_row['department_name']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr>
                        <td>" . $roll_number . "</td>
                        <td>No student found</td>
                        <td>N/A</td>
                        <td>N/A</td>
                      </tr>";
            }

            // Close the student statement
            $student_stmt->close();
        }

        echo "</table>";
    } else {
        echo "No roll numbers found for the event ID: " . htmlspecialchars($_SESSION['event_id']);
    }

    // Close the main statement
    $stmt->close();
} else {
    echo "No Event ID found in the URL.";
}

// Close the database connection
$conn->close();
?>
