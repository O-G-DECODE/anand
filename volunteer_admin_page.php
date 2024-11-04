<?php
include 'connection.php';
session_start();

// Check if a remove request was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_roll_number'])) {
    $roll_number = $_POST['remove_roll_number'];

    // Update query to set club_id to NULL or 0
    $update_query = "UPDATE student SET club_id = NULL WHERE roll_number = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("s", $roll_number);

    if ($stmt->execute()) {
        $message = "Student removed from the club successfully.";
    } else {
        $message = "Failed to remove student from the club.";
    }

    $stmt->close();
}

// Fetch all students with their club details, ordered by club name
$query = "
    SELECT s.roll_number, s.name AS student_name, d.name AS department_name, c.name AS club_name, s.club_id
    FROM student s
    JOIN course cr ON s.course_id = cr.course_id
    JOIN department d ON cr.department_id = d.department_id
    LEFT JOIN club c ON s.club_id = c.club_id
    WHERE s.club_id IS NOT NULL AND s.club_id > 0
    ORDER BY c.name, s.name
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Coordinators</title>
    <link rel="stylesheet" href="report_style.css">
</head>
<body>
    <div class="container">
        <h2>Student Coordinators</h2>

        <?php if (isset($message)) : ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Club</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $current_club = null;
                    while ($row = $result->fetch_assoc()): 
                        // Check if club group should start
                        if ($current_club !== $row['club_name']) {
                            $current_club = $row['club_name'];
                            echo "<tr><td colspan='5' class='club-group'><strong>{$current_club}</strong></td></tr>";
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['club_name']); ?></td>
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
            <p>No students are currently members of any club.</p>
        <?php endif; ?>
    </div>
</body>
</html>
