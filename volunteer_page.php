<?php
// Start session and include database connection
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Fetch the staff's club ID from the session's email
$email = $_SESSION['email'];
$club_query = "
    SELECT s.club_id, c.name AS club_name
    FROM staff s
    JOIN club c ON s.club_id = c.club_id
    WHERE s.email = ?
";

$stmt = $conn->prepare($club_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($club_id, $club_name);
$stmt->fetch();
$stmt->close();

// Check if a student is being removed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_roll_number'])) {
    $roll_number_to_remove = $_POST['remove_roll_number'];
    
    // Update the club_id to 0 for the selected student
    $remove_query = "UPDATE student SET club_id = 0 WHERE roll_number = ? AND club_id = ?";
    $remove_stmt = $conn->prepare($remove_query);
    $remove_stmt->bind_param("ii", $roll_number_to_remove, $club_id);
    $remove_stmt->execute();
    $remove_stmt->close();
    
    // Refresh the page to reflect the changes
    header("Location: volunteer_page.php");
    exit;
}

// Fetch student details with matching club ID
$student_query = "
    SELECT s.roll_number, s.name AS student_name, d.name AS department_name
    FROM student s
    JOIN course co ON s.course_id = co.course_id
    JOIN department d ON co.department_id = d.department_id
    WHERE s.club_id = ?
";

$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Members</title>
    <link rel="stylesheet" href="report_style.css">
</head>
<body>
    <div class="container">
        <h2>MES COLLEGE MARAMPALLY</h2>
        <h3> <?php echo htmlspecialchars($club_name); ?> - Volunteer Members</h3>
        
        <?php if ($result->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Student Name</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="remove_roll_number" value="<?php echo htmlspecialchars($row['roll_number']); ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to remove this student from the club?')">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No students found in your club.</p>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
