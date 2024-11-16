<?php
session_start(); // Start the session

// Check if the email is set in the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    echo "You are not logged in.";
    // Optionally, redirect to the login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="sty.css">
    <style>
        .button-group {
            display: none; /* Initially hide the button group */
        }

    </style>
    <script>
        function toggleButtons(sectionId) {
            var buttonGroup = document.getElementById(sectionId);
            if (buttonGroup.style.display === "none" || buttonGroup.style.display === "") {
                buttonGroup.style.display = "block"; // Show the buttons
            } else {
                buttonGroup.style.display = "none"; // Hide the buttons
            }
        }
    </script>
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <a href="logout.php"><button>Logout</button></a>
</header>
<nav>
    <ul>
        <li><a href="#students">Manage Students</a></li>
        <li><a href="#staff">Manage Staff</a></li>
        <li><a href="#departments">Manage Departments</a></li>
        <li><a href="#courses">Manage Courses</a></li>
        <li><a href="#clubs">Manage Clubs and Coordinators</a></li>
        <li><a href="#event">Manage Events</a></li>
        <li><a href="#attendance_sheet_&_event">Events & Attendance Sheet</a></li>
        <li><a href="#report">Reports</a></li>
    </ul>
</nav>
<main>        
    <section id="staff">
        <h2>Staff</h2>
        <button class="toggle-btn" onclick="toggleButtons('staff-buttons')"> Manage </button>
        <div id="staff-buttons" class="button-group">
            <a href="staff_details.php"><button>View and edit</button></a>
            <a href="add_staff.php"><button class="add-btn">Add</button></a>
            <a href="remove_staff.php"><button class="remove-btn">Remove</button></a>
        </div>
    </section>

    <section id="departments">
        <h2>Departments</h2>
        <button class="toggle-btn" onclick="toggleButtons('department-buttons')"> Manage </button>
        <div id="department-buttons" class="button-group">
            <a href="department_details.php"><button>View and edit</button></a>
            <a href="add_department.php"><button class="add-btn">Add</button></a>
            <a href="remove_department.php"><button class="remove-btn">Remove</button></a>
        </div>
    </section>

    <section id="courses">
        <h2>Courses</h2>
        <button class="toggle-btn" onclick="toggleButtons('course-buttons')"> Manage </button>
        <div id="course-buttons" class="button-group">
            <a href="course_details.php"><button>View and edit</button></a>
            <a href="add_course.php"><button class="add-btn">Add</button></a>
            <a href="remove_course.php"><button class="remove-btn">Remove</button></a>
        </div>
    </section>

    <section id="clubs">
        <h2>Clubs</h2>
        <button class="toggle-btn" onclick="toggleButtons('club-buttons')"> Manage </button>
        <div id="club-buttons" class="button-group">
            <a href="club_details.php"><button>View and edit</button></a>
            <a href="volunteer_admin_page.php"><button>Manage Coordinators</button></a>
            <a href="add_club.php"><button class="add-btn">Add</button></a>
            <a href="remove_club.php"><button class="remove-btn">Remove</button></a>
        </div>
    </section>

    <section id="attendance_sheet_&_event">
        <h2>Events And Attendance Sheet</h2>
        <button class="toggle-btn" onclick="toggleButtons('attendance-buttons')"> Manage </button>
        <div id="attendance-buttons" class="button-group">
            <a href="review_event_admin.php"><button>View</button></a>
        </div>
    </section>

    <section id="students">
        <h2>Students</h2>
        <button class="toggle-btn" onclick="toggleButtons('student-buttons')"> Manage </button>
        <div id="student-buttons" class="button-group">
            <a href="student_details.php"><button>View</button></a>
        </div>
    </section>

    <section id="report">
        <h2>Reports</h2>
        <button class="toggle-btn" onclick="toggleButtons('report-buttons')"> Manage </button>
        <div id="report-buttons" class="button-group">
            <a href="report_admin.php?section=student_report"><button>Student Wise Report</button></a>
            <a href="report_admin.php?section=rejected_students"><button>Rejected Student Report</button></a>
            <a href="report_admin.php?section=department_report"><button>Department Wise Report</button></a>
            <a href="report_admin.php?section=club_report"><button>Club Report</button></a>
            <a href="report_admin.php?section=event_report"><button>Event Report</button></a>
            <a href="report_admin.php?section=approved_event"><button>Approved Event Report</button></a>
            <a href="report_admin.php?section=unapproved_event"><button>Un-Approved Event Report</button></a>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Admin Dashboard</p>
</footer>

</body>
</html>
