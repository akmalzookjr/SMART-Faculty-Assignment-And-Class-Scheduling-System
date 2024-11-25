<?php
session_start();

// Check if the user is logged in and has the role 'dean' or 'coordinator'
if (!isset($_SESSION['email']) || ($_SESSION['type'] !== 'dean' && $_SESSION['type'] !== 'coordinator')) {
    header('Location: login.php');
    exit();
}
// Retrieve session data
$userName = $_SESSION["name"] ?? "User";
$userType = $_SESSION["type"] ?? "User Role";

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    // Redirect to login page if session is not set
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Get the lecturer ID from the URL
$lecturer_id = $_GET['lecturer_id'];

// Fetch the lecturer's assigned schedule by joining the lecturer_assignment and assign_schedule tables
$query = "
    SELECT 
        c.Course_Name, 
        c.Course_Code, 
        s.Time_Slot, 
        s.Day
    FROM lecturer_assignment la
    JOIN assign_schedule asg ON la.Assign_Sche_ID = asg.Assign_Sche_ID
    JOIN course_section cs ON asg.Course_Section_ID = cs.Course_Section_ID
    JOIN course c ON cs.Course_ID = c.Course_ID
    JOIN schedule s ON asg.Sche_ID = s.Sche_ID
    WHERE la.Lect_ID = $lecturer_id
";

$result = mysqli_query($conn, $query);
$schedule = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch repeated courses from the lecturer_assignment table
$repeated_courses_query = "
    SELECT 
        c.Course_Name, 
        c.Course_Code, 
        s.Time_Slot, 
        s.Day
    FROM lecturer_assignment la
    JOIN assign_schedule asg ON la.Assign_Sche_ID = asg.Assign_Sche_ID
    JOIN course_section cs ON asg.Course_Section_ID = cs.Course_Section_ID
    JOIN course c ON cs.Course_ID = c.Course_ID
    LEFT JOIN schedule s ON asg.Sche_ID = s.Sche_ID
    WHERE la.Lect_ID = $lecturer_id
";

// Execute query to get repeated courses
$repeated_courses_result = mysqli_query($conn, $repeated_courses_query);
$repeated_courses = mysqli_fetch_all($repeated_courses_result, MYSQLI_ASSOC);

// Fetch lecturer info
$lecturer_name_query = "SELECT Lect_Name FROM lecturer WHERE Lect_ID = $lecturer_id";
$lecturer_name_result = mysqli_query($conn, $lecturer_name_query);
$lecturer_name = mysqli_fetch_assoc($lecturer_name_result)['Lect_Name'];

// Close connection
mysqli_close($conn);

// Prepare the schedule data to match the day and time slots format
$scheduleData = [
    'Monday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Tuesday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Wednesday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Thursday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Friday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
];

// Populate the scheduleData with the fetched schedule
foreach ($schedule as $row) {
    $scheduleData[$row['Day']][$row['Time_Slot']] = $row['Course_Name'];
}

// Add repeated courses to the scheduleData
foreach ($repeated_courses as $row) {
    $scheduleData[$row['Day']][$row['Time_Slot']] = $row['Course_Name']; // Overwrite or add the course
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Schedule</title>
    <link rel="stylesheet" href="css/stylehome.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<style>
    .main-content{
        overflow-y:hidden;
    }
/* Similar styling as schedulestudent.php */
.flashcard-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Adjusts the spacing between cards */
    justify-content: flex-start; /* Aligns the cards to the left */
    margin-top: 20px;
}

.flashcard {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 10px;
    width: 220px; /* Adjusted width to fit the screen */
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-align: center;
    margin-bottom: 20px; /* Provides spacing between rows */
    min-height: 180px; /* Ensure uniform height for cards */
}

.flashcard:hover {
    transform: translateY(-10px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}

.flashcard .course-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.flashcard .course-code {
    font-size: 14px;
    color: #555;
    margin-bottom: 10px;
}

.flashcard .time-slot,
.flashcard .day {
    font-size: 14px;
    color: #777;
}

/* Ensure the flashcards are responsive on smaller screens */
@media (max-width: 768px) {
    .flashcard {
        width: 100%; /* Makes the flashcards take full width on smaller screens */
        max-width: 300px; /* Max width for each flashcard */
    }
}
/* Highlight repeated courses */
.highlight {
    background-color: black; /* Yellow background for repeated courses */
    color: #000; /* Make text color darker */
    font-weight: bold; /* Optional: Make the text bold */
    border: 2px solid #f39c12; /* Add a border to make it stand out */
}

/* Optional: Add hover effect for the highlighted courses */
.highlight:hover {
    background-color: black; /* Darken the background on hover */
}
/* Scrollable section for the assigned courses */
.assignedcourselecturer {
    max-height: 300px; /* Adjust the height as needed */
    overflow-y: auto;  /* Enables vertical scrolling */
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #ccc;  /* Optional: Add a border around the container */
    border-radius: 10px;
    background-color: #f9f9f9; /* Optional: Add a background color */
    
    /* Adding drop shadow */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Horizontal, Vertical, Blur, Color */
}
.user-img {
  width: 50px;  /* Set your desired width */
  height: 50px; /* Set the same height to make it a square */
  background-color: white;
  padding: 2px;
  object-fit: contain;  /* Ensures the image maintains its aspect ratio while fitting inside the defined width/height */
}
</style>
<body>
<div class="sidebar">
    <div class="top">
        <div class="logo">
            <span>SMART System</span>
        </div>
        <i class="bx bx-menu" id="btn"></i>
    </div>
    <div class="user">
        <a href="home.php"><img src="css/logo uptm.png" alt="me" class="user-img"></a>
        <div>
            <p class="bold"><?php echo htmlspecialchars($userName); ?></p>
            <p><?php echo htmlspecialchars($userType); ?></p>
        </div>
    </div>
    <ul>
        <li>
            <a href="home.php">
                <i class="bx bxs-grid-alt"></i>
                <span class="nav-item">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>
        <?php if ($userType == "dean") : ?>
            <li>
                <a href="lecturerlist.php">
                    <i class='bx bxs-user-pin'></i>
                    <span class="nav-item">Lecturer</span>
                </a>
                <span class="tooltip">Lecturer</span>
            </li>
            <li>
                <a href="schedulelist.php">
                    <i class='bx bx-table'></i>
                    <span class="nav-item">Schedule</span>
                </a>
                <span class="tooltip">Schedule</span>
            </li>
            <li>
                <a href="courselist.php">
                    <i class="bx bx-book"></i>
                    <span class="nav-item">Courses</span>
                </a>
                <span class="tooltip">Courses</span>
            </li>
        <?php endif; ?>
        <?php if ($userType == "coordinator") : ?>
            <li>
                <a href="studentlist.php">
                    <i class="bx bxs-user-detail"></i>
                    <span class="nav-item">Students</span>
                </a>
                <span class="tooltip">Students</span>
            </li>
        <?php endif; ?>
        <li>
            <a href="settings.php">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Settings</span>
            </a>
            <span class="tooltip">Settings</span>
        </li>
        <li>
            <a href="logout.php">
                <i class="bx bx-log-out"></i>
                <span class="nav-item">Logout</span>
            </a>
            <span class="tooltip">Logout</span>
        </li>
    </ul>
</div>
<div class="main-content">
    <header>
        <div class="logo">
            <img src="icon uptm.png" alt="uptm" class="logo-small">
        </div>
    </header>

    <div class="middle">
        <h1>Schedule for <?php echo htmlspecialchars($lecturer_name); ?></h1>
        <!-- Regular Schedule -->
<table class="schedule">
    <thead>
        <tr>
            <th>Day</th>
            <th><span class="font-big"> 8 </span><br><span class="font-small"> 08:00 - 09:00</span></th>
            <th><span class="font-big"> 9 </span><br><span class="font-small"> 09:00 - 10:00</span></th>
            <th><span class="font-big"> 10 </span><br><span class="font-small"> 10:00 - 11:00</span></th>
            <th><span class="font-big"> 11 </span><br><span class="font-small"> 11:00 - 12:00</span></th>
            <th><span class="font-big"> 12 </span><br><span class="font-small"> 12:00 - 13:00</span></th>
            <th><span class="font-big"> 1 </span><br><span class="font-small"> 13:00 - 14:00</span></th>
            <th><span class="font-big"> 2 </span><br><span class="font-small"> 14:00 - 15:00</span></th>
            <th><span class="font-big"> 3 </span><br><span class="font-small"> 15:00 - 16:00</span></th>
            <th><span class="font-big"> 4 </span><br><span class="font-small"> 16:00 - 17:00</span></th>
            <th><span class="font-big"> 5 </span><br><span class="font-small"> 17:00 - 18:00</span></th>
            <th><span class="font-big"> 6 </span><br><span class="font-small"> 18:00 - 19:00</span></th>
        </tr>
    </thead>
            <tbody>
                <?php foreach ($scheduleData as $day => $timeSlots): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($day); ?></td>
                        <?php foreach ($timeSlots as $timeSlot => $courseName): ?>
                            <td class="<?php 
                                // Highlight repeated courses
                                foreach ($repeated_courses as $repeated_course) {
                                    if ($repeated_course['Course_Name'] === $courseName && $repeated_course['Time_Slot'] === $timeSlot) {
                                        echo 'highlight';
                                        break;
                                    }
                                }
                            ?>">
                                <?php echo htmlspecialchars($courseName) ?: ''; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="assignedcourselectureratas">
            <!-- Repeated Courses -->
            <h2>Assigned Courses:</h2>
            <div class="assignedcourselecturer">
                <?php if (count($repeated_courses) > 0): ?>
                    <div class="flashcard-container">
                        <?php foreach ($repeated_courses as $repeated_course): ?>
                            <div class="flashcard">
                                <div class="course-name"><?php echo htmlspecialchars($repeated_course['Course_Name']); ?></div>
                                <div class="course-code"><?php echo htmlspecialchars($repeated_course['Course_Code']); ?></div>
                                <div class="time-slot">Time Slot: <?php echo htmlspecialchars($repeated_course['Time_Slot']); ?></div>
                                <div class="day">Day: <?php echo htmlspecialchars($repeated_course['Day']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No repeated courses found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
