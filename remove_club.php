<?php
// Include the connection file
include 'connection.php';

$message = "";

// Fetch clubs for the dropdown
$sql = "SELECT club_id, name FROM club";
$result = $conn->query($sql);
$clubs = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $clubs[] = $row;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $club_id = $_POST['club_id'];
    
    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM club WHERE club_id = ?");
    $stmt->bind_param("i", $club_id);
    
    if ($stmt->execute()) {
        $message = "Club removed successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Club</title>
    <link rel="stylesheet" href="style_add_dlt.css">
</head>
<body>


<div class="form-container">
<h2>Remove Club</h2>
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <label for="club_id">Select Club:</label>
        <select id="club_id" name="club_id" required>
            <option value="">--Select a Club--</option>
            <?php foreach ($clubs as $club): ?>
                <option value="<?php echo $club['club_id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Remove Club">
    </form>
</div>

</body>
</html>
