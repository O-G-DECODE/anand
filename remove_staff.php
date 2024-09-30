<?php
// Include the connection file
include 'connection.php';

$message = "";

// Fetch staff for the dropdown
$sql = "SELECT staff_id, name FROM staff";
$result = $conn->query($sql);
$staffs = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $staffs[] = $row;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];

    // Prepare and bind to delete staff member
    $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);
    
    if ($stmt->execute()) {
        $message = "Staff and all associated details removed successfully!";
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
    <title>Remove Staff</title>
    <link rel="stylesheet" href="style_add_dlt.css">
</head>
<body>

<h1>Remove Staff</h1>

<div class="form-container">
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <label for="staff_id">Select Staff:</label>
        <select id="staff_id" name="staff_id" required>
            <option value="">--Select Staff--</option>
            <?php foreach ($staffs as $staff): ?>
                <option value="<?php echo $staff['staff_id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Remove Staff">
    </form>
</div>

</body>
</html>
