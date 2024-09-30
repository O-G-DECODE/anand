<?php
// Include the connection file
include 'connection.php';

$message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_name = trim($_POST['department_name']);
    
    if (!empty($department_name)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO department (name) VALUES (?)");
        $stmt->bind_param("s", $department_name);
        
        if ($stmt->execute()) {
            $message = "Department added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Please enter a department name.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Department</title>
    <link rel="stylesheet" href="style_add_dlt.css">
    <script>
        function validateForm() {
            const departmentName = document.forms["departmentForm"]["department_name"].value;
            if (departmentName == "") {
                alert("Department name must be filled out");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<div class="form-container">
<h2>Add New Department</h2>
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form name="departmentForm" action="" method="POST" onsubmit="return validateForm()">
        <label for="department_name">Department Name:</label>
        <input type="text" id="department_name" name="department_name" required>
        
        <input type="submit" value="Add Department">
    </form>
</div>

</body>
</html>
