<?php
include("connection.php");
// Retrieve the email from the session

if (isset($_SESSION['email'])) 
{
    $email = $_SESSION['email']; 
}
// Fetch all events from the event table
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
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        caption {
            font-size: 1.5em;
            margin: 10px 0;
        }
        .btn-delete, .btn-review {
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        .btn-review {
            background-color: #3498db;
            color: white;
            margin-left: 5px;
        }
        .btn-review:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>All Events</h2>
        <table>
            <caption>Events</caption>
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
                            <form method='get' action='review_event_admin.php' style='display:inline;'>
                                <input type='hidden' name='event_id' value='$event_id'>
                                <button type='submit' class='btn-review'>Review</button>
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

// Close connection
$conn->close();
?>