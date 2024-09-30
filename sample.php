<?php
// Include the database connection file
include("connection.php");

// Handle the AJAX request if roll number and event_id are provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rollNumber']) && isset($_POST['event_id'])) {
    $rollNumber = $_POST['rollNumber'];
    $eventId = $_POST['event_id'];
    error_log("Received roll number: " . $rollNumber . " and event_id: " . $eventId); // Debugging line

    // Prepare and execute the query to fetch student details
    $sql = "SELECT s.name as student_name, c.name as course_name, d.name as department_name
            FROM student s
            JOIN course c ON s.course_id = c.course_id
            JOIN department d ON c.department_id = d.department_id
            WHERE s.roll_number = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare statement failed: " . $conn->error);
        echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
        exit();
    }
    $stmt->bind_param("s", $rollNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any record was found
    if ($row = $result->fetch_assoc()) {
        echo json_encode(array(
            'status' => 'registered',
            'student_name' => $row['student_name'],
            'course_name' => $row['course_name'],
            'department_name' => $row['department_name']
        )); // Roll number exists
    } else {
        echo json_encode(array('status' => 'not_registered')); // Roll number does not exist
    }

    // Close connections
    $stmt->close();
    $conn->close();
    exit(); // Ensure no further code is executed
}

// Handle the AJAX request to get event_id
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['event_name'])) {
    $event_name = $_GET['event_name'];
    
    // Prepare and execute the query to fetch the event_id based on event_name
    if (!empty($event_name)) {
        $sql = "SELECT event_id FROM event WHERE name = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare statement failed: " . $conn->error);
            echo json_encode(array('status' => 'error', 'message' => 'Database prepare failed.'));
            exit();
        }
        $stmt->bind_param("s", $event_name);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the event was found
        if ($row = $result->fetch_assoc()) {
            echo "Event ID: " . htmlspecialchars($row['event_id']);
        } else {
            echo "No event found with the given name.";
        }

        // Close connections
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Table</title> 
    <link rel="stylesheet" type="text/css" href="studentstyle.css">
    <script>
        let rowIndex = 1;

        function validateRollNumber(form, index) {
            var rollNumber = form.rollNumber.value;
            var eventId = document.querySelector("input[name='event_id']").value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Use the same PHP file
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            var row = document.querySelector(`#row-${index}`);
                            if (response.status === "not_registered") {
                                alert("Roll number not registered.");
                                row.querySelector(".student-name").textContent = "";
                                row.querySelector(".course-name").textContent = "";
                                row.querySelector(".department-name").textContent = "";
                            } else if (response.status === "registered") {
                                row.querySelector(".student-name").textContent = response.student_name;
                                row.querySelector(".course-name").textContent = response.course_name;
                                row.querySelector(".department-name").textContent = response.department_name;
                            }
                        } catch (e) {
                            console.error("Error parsing JSON response:", e);
                        }
                    } else {
                        console.error("AJAX request failed with status:", xhr.status);
                    }
                }
            };
            xhr.send("rollNumber=" + encodeURIComponent(rollNumber) + "&event_id=" + encodeURIComponent(eventId));
            return false; // Prevent form submission
        }

        function addRow() {
            rowIndex++;
            var tableBody = document.querySelector("tbody");
            var newRow = document.createElement("tr");
            newRow.id = `row-${rowIndex}`;
            newRow.innerHTML = `
                <td>
                    <form onsubmit="return validateRollNumber(this, ${rowIndex});">
                        <input type="text" name="rollNumber" class="input-field" placeholder="Enter Roll Number" oninput="updateYearHeading()">
                        <input type="submit" class="action-button check-button" value="Check">
                    </form>
                </td>
                <td class="student-name"></td>
                <td class="course-name"></td>
                <td class="department-name"></td>
                <td class="year-heading">---</td>
                <td><button class="action-button remove-button" onclick="removeRow(${rowIndex})">Remove</button></td>
            `;
            tableBody.appendChild(newRow);
        }

        function removeRow(index) {
            var row = document.querySelector(`#row-${index}`);
            row.remove();
        }

        function getYearHeading(rollNumber) {
            // Extract the first two digits of the roll number
            var prefix = rollNumber.substring(0, 2);
            var year = '20' + prefix;
            return year;
        }

        function updateYearHeading() {
            var rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                var rollNumberInput = row.querySelector("input[name='rollNumber']");
                if (rollNumberInput) {
                    var yearHeading = getYearHeading(rollNumberInput.value);
                    row.querySelector(".year-heading").textContent = yearHeading;
                }
            });
        }
    </script>
</head>
<body>
    <h1>Student Information</h1>
    <table>
        <thead>
            <tr>
                <th>Roll Number</th>
                <th>Name</th>
                <th>Course</th>
                <th>Department</th>
                <th>Year</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr id="row-1">
                <td>
                    <form onsubmit="return validateRollNumber(this, 1);">
                        <input type="text" name="rollNumber" class="input-field" placeholder="Enter Roll Number" oninput="updateYearHeading()">
                        <input type="submit" class="action-button check-button" value="Check">
                    </form>
                </td>
                <td class="student-name"></td>
                <td class="course-name"></td>
                <td class="department-name"></td>
                <td class="year-heading">---</td>
                <td><button class="action-button remove-button" onclick="removeRow(1)">Remove</button></td>
            </tr>
            <!-- Additional rows can be added here -->
        </tbody>
    </table>
    <input type="hidden" name="event_id" value="<?php echo isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : ''; ?>">
    <button class="action-button add-button" onclick="addRow()">Add</button>
    <button class="action-button submit-all-button" onclick="submitAll()">Submit All</button>
</body>
</html>
