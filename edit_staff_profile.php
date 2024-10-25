<?php
// Start the session
session_start();

// Include the connection file
include 'connection.php';

// Get the email from the session
$email = $_SESSION['email'];

// SQL query to fetch staff details for the logged-in staff member
$sql = "SELECT 
            s.staff_id, 
            s.name AS staff_name,
            d.name AS department_name,
            s.email
        FROM 
            staff s
        JOIN 
            department d ON s.department_id = d.department_id
        WHERE 
            s.email = ?";  // Use a prepared statement to prevent SQL injection

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Details</title>
    <link rel="stylesheet" href="style_of_ussers.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background-color: #6e8efb;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9ff;
        }
        tr:hover {
            background-color: #e6e9ff;
        }
    </style>
</head>
<body>

<h1>Staff Details</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Email</th>
                <th>Action</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        $staff_id = htmlspecialchars($row['staff_id']);
        echo "<tr>
                <td>" . htmlspecialchars($row['staff_name']) . "</td>
                <td>" . htmlspecialchars($row['department_name']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>
                    <form method='get' action='edit_staff.php' style='display:inline;'>
                        <input type='hidden' name='staff_id' value='$staff_id'>
                        <button type='submit' class='btn-edit'>Edit</button>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No results found.</p>";
}

$stmt->close();
$conn->close();
?>

</body>
</html>
