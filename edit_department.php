<?php
// Include the connection file
include("connection.php");
session_start();

if (!isset($_SESSION['email'])) {
    echo "No user is logged in.";
    exit;
}

$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

if ($department_id <= 0) {
    echo "Invalid department ID.";
    exit;
}

// Fetch current department name
$stmt = $conn->prepare("SELECT name FROM department WHERE department_id = ?");
$stmt->bind_param("i", $department_id);
$stmt->execute();
$stmt->bind_result($department_name);
if (!$stmt->fetch()) {
    echo "Department not found.";
    $stmt->close();
    exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_department_name = trim($_POST['department_name']);

    $update_stmt = $conn->prepare("UPDATE department SET name = ? WHERE department_id = ?");
    $update_stmt->bind_param("si", $new_department_name, $department_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Department name updated successfully!'); window.location.href = 'department_details.php';</script>";
    } else {
        echo "Error updating department: " . $conn->error;
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <style>
        /* Your styling here */
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Department Name</h2>
    <form method="POST" action="">
        <label for="department_name">Department Name</label>
        <input type="text" id="department_name" name="department_name" value="<?php echo htmlspecialchars($department_name); ?>" required>

        <button type="submit">Update Department</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
