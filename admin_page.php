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
            <li><a href="#clubs ">Manage Clubs and Coordinators</a></li>
            <li><a href="#event">Manage Events</a></li>
            <li><a href="#attendance_sheet_&_event"> Events & Attendance Sheet</a></li>
            <li><a href="#report">Reports</a></li>

        </ul>
    </nav>
    <main>        
        <section id="staff">
            <h2> Staffs</h2>
           <a href="staff_details.php"><button>  View and edit</button> </a>
           <a href="add_staff.php"><button class="add-btn">Add</button></a>
           <a href="remove_staff.php"><button class="remove-btn">Remove</button></a>
            <!--  more functionality as needed -->
        </section>

        <section id="departments">
            <h2> Departments</h2>
           <a href="department_details.php"><button>  View and edit</button> </a>
           <a href="add_department.php"><button class="add-btn">Add</button></a>
           <a href="remove_department.php"><button class="remove-btn">Remove</button></a>
            <!--  more functionality as needed -->
        </section>

        <section id="courses">
            <h2> Courses</h2>
           <a href="course_details.php"><button>  View and edit</button> </a>
           <a href="add_course.php"><button class="add-btn">Add</button></a>
           <a href="remove_course.php"><button class="remove-btn">Remove</button></a>
        </section>
        <section id="clubs">
            <h2> Clubs</h2>
            <a href="club_details.php "><button>  View and edit</button></a>
            <a href="volunteer_admin_page.php"><button> Manage Coodinators</button></a>
            <a href="add_club.php"><button class="add-btn">Add</button></a>
            <a href="remove_club.php"><button class="remove-btn">Remove</button></a>
        </section>
        <section id="attendance_sheet_&_event">
            <h2>Events And Attendance Sheet   </h2>
           <a href="review_event_admin.php"><button>  View</button> </a>
        </section>
        <section id="students">
    <h2> Students</h2> 
    <a href="student_details.php"><button>View</button></a>
</section>
<section id="report">
    <h2> Reports</h2> 
    <a href="report_admin.php?section=student_report"><button>Student Wise Report</button></a>
<a href="report_admin.php?section=rejected_students"><button>Rejected Student Report</button></a>
<a href="report_admin.php?section=department_report"><button>Department Wise Report</button></a>
<a href="report_admin.php?section=club_report"><button>Club Report</button></a>
<a href="report_admin.php?section=event_report"><button>Event Report</button></a>
<a href="report_admin.php?section=approved_event"><button>Approved Event Report</button></a>
<a href="report_admin.php?section=unapproved_event"><button>Un-Approved Event Report</button></a>

</section>
    </main>
    <footer>
        <p>&copy; 2024 Admin Dashboard</p>
    </footer>
</body>
</html>
