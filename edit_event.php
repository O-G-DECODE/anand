<?php
include("connection.php");
session_start();

// Check if the event_id is provided via GET request
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch the event details from the database
    $stmt = $conn->prepare("SELECT * FROM event WHERE event_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $name = htmlspecialchars($row['name']);
            $date = htmlspecialchars($row['date']);
            $period = htmlspecialchars($row['period']);
        } else {
            echo "<script>
                    alert('Event not found.');
                    window.location.href = 'event.php';
                  </script>";
            exit();
        }

        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
        exit();
    }
} else {
    echo "<script>
            alert('Invalid request.');
            window.location.href = 'event.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
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

        .btn-delete, .btn-review, .btn-edit {
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

        .btn-edit {
            background-color: #ffa500;
            color: white;
            margin-left: 5px;
        }

        .btn-edit:hover {
            background-color: #ff8c00;
            transform: translateY(-2px);
        }

        input[type="text"], input[type="date"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        button[type="submit"] {
            background-color: #6e8efb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
        }

        button[type="submit"]:hover {
            background-color: #5c7cfa;
        } 
        

    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Event</h2>
        <form method="post" action="update_event.php">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

            <label for="name">Event Name:</label>
            <input type="text" name="name" id="name" value="<?php echo $name; ?>" required>

            <label for="date">Event Date:</label>
            <input type="date" name="date" id="date" value="<?php echo $date; ?>" required>

            <label for="period">Event Period:</label>
            <input type="text" name="period" id="period" value="<?php echo $period; ?>" required>

            <button type="submit" class="btn-edit">Update Event</button>
        </form>
    </div>
</body>
</html>
