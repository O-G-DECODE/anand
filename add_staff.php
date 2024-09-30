<?php
// Include the connection file
include 'connection.php';

$message = "";

// Fetch departments for the dropdown
$sql = "SELECT department_id, name FROM department";
$department_result = $conn->query($sql);
$departments = [];

if ($department_result->num_rows > 0) {
    while ($row = $department_result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Fetch clubs for the dropdown
$sql = "SELECT club_id, name FROM club";
$club_result = $conn->query($sql);
$clubs = [];

if ($club_result->num_rows > 0) {
    while ($row = $club_result->fetch_assoc()) {
        $clubs[] = $row;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $department_id = $_POST['department_id'];
    $email = trim($_POST['email']);
    $password = $email; // Set password as email and hash it
    $club_id = $_POST['club_id'];

    if (!empty($name) && !empty($department_id) && !empty($email)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO staff (name, department_id, email, password, club_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $name, $department_id, $email, $password, $club_id);
        
        if ($stmt->execute()) {
            $message = "Staff added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Staff</title>
    <link rel="stylesheet" href="style_add_dlt.css">
</head>
<body>

<h1>Add New Staff</h1>

<div class="form-container">
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="department_id">Select Department:</label>
        <select id="department_id" name="department_id" required>
            <option value="">--Select a Department--</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['department_id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="club_id">Select Club:</label>
        <select id="club_id" name="club_id">
            <option value="">--No Club--</option> <!-- Option for no club -->
            <?php foreach ($clubs as $club): ?>
                <option value="<?php echo $club['club_id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Add Staff">
    </form>
</div>

</body>
</html>
