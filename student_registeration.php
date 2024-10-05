<?php
include("connection.php");

// Initialize arrays for departments, courses, and years
$departments = array();
$courses = array();
$years = array();

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

  // Check if the roll number already exists in the student table
  $sql = "SELECT * FROM student WHERE roll_number = '$roll_number'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    echo "<script>alert('Roll number already exists!');</script>";
  } else {
    // Insert the data into the student table
    $sql = "INSERT INTO student (roll_number, name, password, course_id , club_id) VALUES ('$roll_number', '$name', '$password', '$course_id' , 0)";
    if ($conn->query($sql) === TRUE) {
      echo "<script>alert('Registration successful!');</script>";
      header("Location: student_login.php"); // Add this line to redirect to studentlogin.php
      exit(); // Exit the script to prevent further execution
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
    /* Internal Style Sheet */
    body {
      font-family: Arial, sans-serif;
      background-color: #94cccd; /* Light teal background */
      color: #004d40; /* Dark teal text color */
      margin: 0;
      padding: 0;
    }
    
    h1 {
      text-align: center;
      color: #4CAF50; 
      background-color: #ffffff;
    }

    form {
      max-width: 500px;
      margin: 0 auto;
      padding: 20px;
      background-color: #ffffff; /* White background for the form */
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      margin-bottom: 10px;.getElementById(...) is null
      color: #00796b; 
    }

    input[type="text"], input[type="password"], select {
      width: 100%;
      padding: 10px;
      margin: 5px 0 20px 0;
      border: 1px solid #004d40; 
      border-radius: 4px;
    }

    input[type="submit"] {
      background-color: #4CAF50; /* Dark teal background */
      color: #ffffff; /* White text */
      border: none;
      padding: 15px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    input[type="submit"]:hover {
      background-color: #00796b; /* Lighter teal on hover */
    }

    /* Hide all courses initially .getElementById(...) is null*/
    #course option {
      display: none;
    }
  </style>
</head>
<body>
  <h1>Student Registration Form</h1>
  <form id="registrationForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
    <label for="roll_number">Roll Number:</label>
    <input type="text" id="roll_number" name="roll_number">

    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name">

    <label for="department">Department:</label>
    <select id="department" name="department">
      <option value="">Select a department</option>
      <?php foreach ($departments as $department) { ?>
        <option value="<?php echo $department["department_id"]; ?>"><?php echo $department["name"]; ?></option>
      <?php } ?>
    </select>

    <label for="course">Course:</label>
    <select id="course" name="course">
      <option value="">Select a course</option>
      <?php foreach ($courses as $course) { ?>
        <option value="<?php echo $course["course_id"]; ?>" data-department-id="<?php echo $course["department_id"]; ?>">
          <?php echo $course["name"]; ?>
        </option>
      <?php } ?>
    </select>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">

    <input type="submit" value="Register">
  </form>

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
      var year = document.getElementById("year").value;
      var password = document.getElementById("password").value;

      if (!rollNumber || !name || !department || !course || !year || !password) {
        alert("Please fill out all fields.");
        event.preventDefault(); // Prevent form submission
      }
    });
  </script>
</body>
</html>
