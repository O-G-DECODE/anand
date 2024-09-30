<?php
// Include the connection file
include 'connection.php';

$message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $club_name = trim($_POST['club_name']);
    
    if (!empty($club_name)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO club (name) VALUES (?)");
        $stmt->bind_param("s", $club_name);
        
        if ($stmt->execute()) {
            $message = "Club added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Please enter a club name.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Club</title>
    <link rel="stylesheet" href="style_add_dlt.css">
    <script>
        function validateForm() {
            const clubName = document.forms["clubForm"]["club_name"].value;
            if (clubName == "") {
                alert("Club name must be filled out");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<h1>Add New Club</h1>

<div class="form-container">
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form name="clubForm" action="" method="POST" onsubmit="return validateForm()">
        <label for="club_name">Club Name:</label>
        <input type="text" id="club_name" name="club_name" required>
        
        <input type="submit" value="Add Club">
    </form>
</div>

</body>
</html>
