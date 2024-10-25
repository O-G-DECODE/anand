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
       <link rel="stylesheet" href="edit_admin.css">
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
