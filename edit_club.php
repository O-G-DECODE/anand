<?php
// Include the connection file
include("connection.php");
session_start();

if (!isset($_SESSION['email'])) {
    echo "No user is logged in.";
    exit;
}

$club_id = isset($_GET['club_id']) ? intval($_GET['club_id']) : 0;

if ($club_id <= 0) {
    echo "Invalid club ID.";
    exit;
}

// Fetch current club name
$stmt = $conn->prepare("SELECT name FROM club WHERE club_id = ?");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$stmt->bind_result($club_name);
if (!$stmt->fetch()) {
    echo "Club not found.";
    $stmt->close();
    exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_club_name = trim($_POST['club_name']);

    $update_stmt = $conn->prepare("UPDATE club SET name = ? WHERE club_id = ?");
    $update_stmt->bind_param("si", $new_club_name, $club_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Club name updated successfully!'); window.location.href = 'club_details.php';</script>";
    } else {
        echo "Error updating club: " . $conn->error;
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Club</title>
  <link rel="stylesheet" href="edit_admin.css">
</head>
<body>

<div class="container">
    <h2>Edit Club Name</h2>
    <form method="POST" action="">
        <label for="club_name">Club Name</label>
        <input type="text" id="club_name" name="club_name" value="<?php echo htmlspecialchars($club_name); ?>" required>

        <button type="submit">Update Club</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
