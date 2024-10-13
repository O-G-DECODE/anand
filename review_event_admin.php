<?php
include("connection.php");
session_start(); // Ensure the session is started to access session data

// Check if user is logged in
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Fetch the staff_id of the staff member from the staff table
    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($staff_staff_id);
        $stmt->fetch();
        $stmt->close();

        // Fetch all events
        $stmt = $conn->prepare("SELECT * FROM event");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e4d3ea;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width:1100px;
            width: 100%;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }

        th {
            background-color: #6e8efb;
            color: #fff;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f8f9ff;
        }

        tr:hover {
            background-color: #e6e9ff;
        }

        caption {
            font-size: 1.5em;
            margin: 20px 0;
            color: #6e8efb;
            font-weight: 700;
        }

        .btn-delete, .btn-attendance, .btn-review {
            border: none;
            padding: 8px 8px;
            cursor: pointer;
            border-radius: 10px;
            font-size: 0.9em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-left: 5px; /* Add spacing between buttons */
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
        }

        .btn-attendance {
            background-color: #6e8efb;
            color: white;
        }

        .btn-attendance:hover {
            background-color: #5c7cfa;
            transform: translateY(-2px);
        }

        .btn-review {
            background-color: #4caf50;
            color: white;
        }

        .btn-review:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }

        /* Flexbox for Action column */
        .action-buttons {
            display: flex;
            align-items: center; /* Center vertically */
        }

        .status {
            margin-right: 10px; /* Space between status and buttons */
        }
    </style>
</head>
<body>
    <div class="container">
       
        <table>
            <caption>All Events</caption>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Period</th>
                    <th>Created Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
<?php
            while ($row = $result->fetch_assoc()) {
                $event_id = htmlspecialchars($row['event_id']);
                $event_name = htmlspecialchars($row['name']);
                $event_date = htmlspecialchars($row['date']);
                $event_period = htmlspecialchars($row['period']);
                $event_created_date = htmlspecialchars($row['create_date']);

                // Check attendance status
                $stmt2 = $conn->prepare("SELECT approve FROM request WHERE event_id = ?");
                $stmt2->bind_param("i", $event_id);
                $stmt2->execute();
                $stmt2->bind_result($approve);
                $stmt2->fetch();
                $stmt2->close();

                // Determine the status and buttons based on attendance status
                if ($approve > 0) {
                    $status = "Approved";
                    $attendance_button = "<form method='get' action='view_attendance_admin.php' style='display:inline;'>
                                            <input type='hidden' name='event_id' value='$event_id'>
                                            <button type='submit' class='btn-review'>View  Attendance</button>
                                          </form>";
                } else {
                    $status = "Pending..";
                    $attendance_button = "<form method='get' action='mark_attendance_admin.php' style='display:inline;'>
                                            <input type='hidden' name='event_id' value='$event_id'>
                                            <button type='submit' class='btn-attendance'>Mark Attendance</button>
                                          </form>";
                }

                echo "<tr>
                        <td>$event_name</td>
                        <td>$event_date</td>
                        <td>$event_period</td>
                        <td>$event_created_date</td>
                        <td class='action-buttons'>
                            <span class='status'>$status</span>
                            $attendance_button
                            <form method='post' action='delete_event.php' style='display:inline;'>
                                <input type='hidden' name='event_id' value='$event_id'>
                                <button type='submit' class='btn-delete'>Delete</button>
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
    }
} else {
    echo "No user is logged in.";
}

// Close connection
$conn->close();
?>
