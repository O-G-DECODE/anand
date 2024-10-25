<?php
include("connection.php");
session_start();

if (!isset($_SESSION['email'])) {
    echo "No user is logged in.";
    exit;
}

$staff_id = isset($_GET['staff_id']) ? intval($_GET['staff_id']) : 0;

if ($staff_id <= 0) {
    echo "Invalid staff ID.";
    exit;
}

$stmt = $conn->prepare("SELECT name, department_id, email, password, club_id FROM staff WHERE staff_id = ?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$stmt->bind_result($name, $department_id, $email, $password, $club_id);
if (!$stmt->fetch()) {
    echo "Staff member not found.";
    $stmt->close();
    exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $department_id = intval($_POST['department_id']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $club_id = !empty($_POST['club_id']) ? intval($_POST['club_id']) : NULL;

    $update_stmt = $conn->prepare("UPDATE staff SET name = ?, department_id = ?, email = ?, password = ?, club_id = ? WHERE staff_id = ?");
    $update_stmt->bind_param("sissii", $name, $department_id, $email, $password, $club_id, $staff_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Staff details updated successfully!'); window.location.href = 'staff_details.php';</script>";
    } else {
        echo "Error updating staff: " . $conn->error;
    }

    $update_stmt->close();
}

// Fetch department and club options for dropdowns
$departments = $conn->query("SELECT department_id, name FROM department");
$clubs = $conn->query("SELECT club_id, name FROM club");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
    <link rel="stylesheet" href="edit_admin.css">
</head>
<body>

<div class="container">
    <h2>Edit Staff Details</h2>
    <form method="POST" action="">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

        <label for="department_id">Department</label>
        <select id="department_id" name="department_id" required>
            <option value="">Select Department</option>
            <?php while ($department = $departments->fetch_assoc()): ?>
                <option value="<?php echo $department['department_id']; ?>" <?php if ($department_id == $department['department_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($department['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>

        <label for="club_id">Club (Optional)</label>
        <select id="club_id" name="club_id">
            <option value="">No Club</option>
            <?php while ($club = $clubs->fetch_assoc()): ?>
                <option value="<?php echo $club['club_id']; ?>" <?php if ($club_id == $club['club_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($club['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Update Staff</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
