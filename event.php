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
        $stmt->bind_result($staff_staff_id);
        $stmt->fetch();
        $stmt->close();

        // Fetch events created by this staff member
        $stmt = $conn->prepare("SELECT * FROM event WHERE staff_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $staff_staff_id);
            $stmt->execute();
            $result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Events</title>
    <style>
       body {
    font-family: 'Poppins', sans-serif;
    background-color :#e4d3ea;
    margin: 0;
    padding: 20px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    max-width: 900px;
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

.btn-delete, .btn-review {
    border: none;
    padding: 8px 15px;
    cursor: pointer;
    border-radius: 10px;
    font-size: 0.9em;
    font-weight: 600;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-delete {
    background-color: #f44336;
    color: white;
}

.btn-delete:hover {
    background-color: #d32f2f;
    transform: translateY(-2px);
}

.btn-review {
    background-color: #6e8efb;
    color: white;
    margin-left: 5px;
}

.btn-review:hover {
    background-color: #5c7cfa;
    transform: translateY(-2px);
}

    </style>
</head>
<body>
    <div class="container">
        <table>
            <caption>Events Created by You</caption>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Period</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
<?php
            while ($row = $result->fetch_assoc()) {
                $event_id = htmlspecialchars($row['event_id']);
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['date']) . "</td>
                        <td>" . htmlspecialchars($row['period']) . "</td>
                        <td>
                            <form method='post' action='delete_event.php' style='display:inline;'>
                                <input type='hidden' name='event_id' value='$event_id'>
                                <button type='submit' class='btn-delete'>Delete</button>
                            </form>
                            <form method='get' action='review_event.php' style='display:inline;'>
                                <input type='hidden' name='event_id' value='$event_id'>
                                <button type='submit' class='btn-review'>Mark Attendance</button>
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
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No user is logged in.";
}

// Close connection
$conn->close();
?>
