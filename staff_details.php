<?php
// Include the connection file
include 'connection.php';

// SQL query to fetch staff details along with department names
$sql = "SELECT 
            s.staff_id,  -- Include staff_id for the edit link
            s.name AS staff_name,
            d.name AS department_name,
            s.email
        FROM 
            staff s
        JOIN 
            department d ON s.department_id = d.department_id";

$result = $conn->query($sql);
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
                <th>Action</th>  <!-- New column for Action -->
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        $staff_id = htmlspecialchars($row['staff_id']);  // Get the staff_id for the edit link
        echo "<tr>
                <td>" . htmlspecialchars($row['staff_name']) . "</td>
                <td>" . htmlspecialchars($row['department_name']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>
                    <form method='get' action='edit_staff.php' style='display:inline;'>  <!-- Edit form -->
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

$conn->close();
?>

</body>
</html>
