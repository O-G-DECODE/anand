<?php
include("connection.php");

// Initialize arrays for departments and courses
$departments = array();
$courses = array();

// Retrieve department data
$sql = "SELECT * FROM department";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  $departments[] = array("department_id" => $row["department_id"], "name" => $row["name"]);
}

// Retrieve course data
$sql = "SELECT * FROM course";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  $courses[] = array("course_id" => $row["course_id"], "name" => $row["name"], "department_id" => $row["department_id"]);
}

// Process the form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $roll_number = $_POST["roll_number"];
  $name = $_POST["name"];
  $department_id = $_POST["department"];
  $course_id = $_POST["course"];
  $password = $_POST["password"];

  // Check if the roll number already exists
  $sql = "SELECT * FROM student WHERE roll_number = '$roll_number'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    echo "<script>alert('Roll number already exists!');</script>";
  } else {
    // Insert data into the student table
    $sql = "INSERT INTO student (roll_number, name, password, course_id, club_id) VALUES ('$roll_number', '$name', '$password', '$course_id', 0)";
    if ($conn->query($sql) === TRUE) {
      echo "<script>alert('Registration successful!');</script>";
      header("Location: student_login.php");
      exit();
    } else {
      echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Registration Form</title>
  <style>
    /* Colorlib inspired styling */
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #e0f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .registration-container {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      color: #333;
      font-size: 14px;
    }

    input[type="text"], input[type="password"], select {
      width: 100%;
      padding: 12px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="registration-container">
    <h2>Register Account</h2>
    <form id="registrationForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
      <div class="form-group">
        <label for="roll_number">Roll Number:</label>
        <input type="text" id="roll_number" name="roll_number" required>
      </div>
      <div class="form-group">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="department">Department:</label>
        <select id="department" name="department" required>
          <option value="">Select a department</option>
          <?php foreach ($departments as $department) { ?>
            <option value="<?php echo $department["department_id"]; ?>"><?php echo $department["name"]; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label for="course">Course:</label>
        <select id="course" name="course" required>
          <option value="">Select a course</option>
          <?php foreach ($courses as $course) { ?>
            <option value="<?php echo $course["course_id"]; ?>" data-department-id="<?php echo $course["department_id"]; ?>">
              <?php echo $course["name"]; ?>
            </option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <input type="submit" value="Register">
    </form>
  </div>

  <script>
    // Show/hide courses based on department selection
    document.getElementById("department").addEventListener("change", function() {
      var departmentId = this.value;
      var courses = document.getElementById("course").options;
      for (var i = 0; i < courses.length; i++) {
        if (courses[i].getAttribute("data-department-id") == departmentId || courses[i].value == "") {
          courses[i].style.display = "block";
        } else {
          courses[i].style.display = "none";
        }
      }
    });

    // Form validation
    document.getElementById("registrationForm").addEventListener("submit", function(event) {
      var rollNumber = document.getElementById("roll_number").value;
      var name = document.getElementById("name").value;
      var department = document.getElementById("department").value;
      var course = document.getElementById("course").value;
      var password = document.getElementById("password").value;

      if (!rollNumber || !name || !department || !course || !password) {
        alert("Please fill out all fields.");
        event.preventDefault();
      }
    });
  </script>

</body>
</html>
