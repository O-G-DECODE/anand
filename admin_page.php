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
</header>
    <nav>
        <ul>
            <li><a href="#students">Manage Students</a></li>
            <li><a href="#staff">Manage Staff</a></li>
            <li><a href="#departments">Manage Departments</a></li>
            <li><a href="#courses">Manage Courses</a></li>
            <li><a href="#clubs">Manage Clubs</a></li>
            <li><a href="#event">Manage Events</a></li>

            <li><a href="logout.php"><button>Logout</button></a></li>
        </ul>
    </nav>
    <main>
    <section id="students">
    <h2> Students</h2> 
    <a href="student_details.php"><button>View</button></a>
    <!--<a href="add_student.php"><button class="add-btn">Add</button></a>
    <a href="remove_student.php"><button class="remove-btn">Remove</button></a> -->
</section>

<!-- Repeat the same for other sections -->
        
        <section id="staff">
            <h2> Staffs</h2>
           <a href="staff_details.php"><button>  View</button> </a>
           <a href="add_staff.php"><button class="add-btn">Add</button></a>
           <a href="remove_staff.php"><button class="remove-btn">Remove</button></a>
            <!--  more functionality as needed -->
        </section>

        <section id="departments">
            <h2> Departments</h2>
           <a href="department_details.php"><button>  View</button> </a>
           <a href="add_department.php"><button class="add-btn">Add</button></a>
           <a href="remove_department.php"><button class="remove-btn">Remove</button></a>
            <!--  more functionality as needed -->
        </section>

        <section id="courses">
            <h2> Courses</h2>
           <a href="course_details.php"><button>  View</button> </a>
           <a href="add_course.php"><button class="add-btn">Add</button></a>
           <a href="remove_course.php"><button class="remove-btn">Remove</button></a>
            <!--  more functionality as needed -->
        </section>

        <section id="clubs">
            <h2> Clubs</h2>
            <a href="club_details.php "><button>  View</button></a>
            <a href="add_club.php"><button class="add-btn">Add</button></a>
            <a href="remove_club.php"><button class="remove-btn">Remove</button></a>
            <!--  more functionality as needed -->
        </section>

        <section id="event">
            <h2> Events</h2>
           <a href="event_details.php"><button>  View</button> </a>
           <a href="add_event.php"><button class="add-btn">Add</button></a>
           <a href="remove_event.php"><button class="remove-btn">Remove</button></a>
            <!--  more functionality as needed -->
        </section>

    </main>
    <footer>
        <p>&copy; 2024 Admin Dashboard</p>
    </footer>
</body>
</html>