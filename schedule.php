<?php
include("connect.php");

if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
}

// Query for fetching assign schedule
$sql_table = 'SELECT Assign_Schedule.Assign_Sche_ID, Assign_Schedule.Sche_ID, Assign_Schedule.Course_Section_ID, 
Schedule.Sche_ID, Schedule.Time_Slot, Schedule.Day, 
Course_Section.Course_Section_ID, Course_Section.Course_ID, Course_Section.Course_Section, 
Course.Course_ID, Course.Course_Name, Course.Course_Code, Course.Course_CH, 
Lecturer_Assignment.Assignment_ID, Lecturer_Assignment.Lect_ID, Lecturer_Assignment.Assign_Sche_ID, 
Section.Section_ID, Section.Section_Number, Section.Sem_ID, 
Semester.Sem_ID, Semester.Sem_Number, Semester.Level_ID, 
Level.Level_ID, Level.Level_Name, 
Lecturer.Lect_Name
FROM Assign_Schedule
INNER JOIN Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
INNER JOIN Course_Section ON Course_Section.Course_Section_ID = Assign_Schedule.Course_Section_ID
INNER JOIN Course ON Course.Course_ID = Course_Section.Course_ID
LEFT JOIN Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
LEFT JOIN Lecturer ON Lecturer.Lect_ID = Lecturer_Assignment.Lect_ID
INNER JOIN Section ON Assign_Schedule.Section_ID = Section.Section_ID
INNER JOIN Semester ON Section.Sem_ID = Semester.Sem_ID 
INNER JOIN Level ON Semester.Level_ID = Level.Level_ID
WHERE Assign_Schedule.Section_ID = 13';

// Fetch levels
$sql_levels = 'SELECT Level_ID, Level_Name FROM Level';
$result_levels = mysqli_query($conn, $sql_levels);
$levels = mysqli_fetch_all($result_levels, MYSQLI_ASSOC);

// Fetch assign schedule data
$result_table = mysqli_query($conn, $sql_table);
$assignSchedule = mysqli_fetch_all($result_table, MYSQLI_ASSOC);

// Prepare the schedule data
$scheduleData = [
    'Monday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Tuesday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Wednesday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Thursday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Friday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
];

// Close the connection (optional)
mysqli_free_result($result_table);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/stylehome.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<div class="sidebar">
        <div class="top">
            <div class="logo">
                <span>SMART system</span>
            </div>
            <i class="bx bx-menu" id="btn"></i>
        </div>
        <div class="user">
            <a href="home.php"><img src="akmal.jpg" alt="me" class="user-img"></a>
            <div>
                <p class="bold">Akmal</p>
                <p>Admin</p>
            </div>
        </div>
        <ul>
            <li>
                <a href="#">
                    <i class="bx bxs-grid-alt"></i>
                    <span class="nav-item">Dashboard</span>
                </a>
                <span class="tooltip">Dashboard</span>
            </li>
            <li>
                <a href="lecturerlist.php">
                    <i class='bx bxs-user-pin'></i>
                    <span class="nav-item">Lecturer</span>
                </a>
                <span class="tooltip">Lecturer</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-info-square'></i>
                    <span class="nav-item">Info</span>
                </a>
                <span class="tooltip">Info</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-table'></i>
                    <span class="nav-item">Schedule</span>
                </a>
                <span class="tooltip">Schedule</span>
            </li>
            <li>
                <a href="#">
                    <i class="bx bx-cog"></i>
                    <span class="nav-item">Settings</span>
                </a>
                <span class="tooltip">Settings</span>
            </li>
            <li>
                <a href="#">
                    <i class="bx bx-log-out"></i>
                    <span class="nav-item">Logout</span>
                </a>
                <span class="tooltip">Logout</span>
            </li>
        </ul>
    </div>
    <div class="main-content">
    <div class="">
            <h4>Schedule Table:</h4>
            <select id="levelSelect" onchange="loadSemesters(this.value)">
                <option value="">Select Level</option>
                <?php foreach ($levels as $level): ?>
                    <option value="<?php echo htmlspecialchars($level['Level_ID']); ?>">
                        <?php echo htmlspecialchars($level['Level_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="semesterSelect" onchange="loadSections(this.value)" disabled>
                <option value="">Select Semester</option>
                <!-- This will be populated dynamically based on selected level -->
            </select>

            <select id="sectionSelect" onchange="loadSchedule(this.value)" disabled>
                <option value="">Select Section</option>
                <!-- This will be populated dynamically based on selected semester -->
            </select>
    </div>
    <table class="schedule">
        <thead>
            <tr>
                <th>Day</th>
                <th><span class="font-big"> 8 </span><br><span class="font-small"> 08.00 - 09.00</span></th>
                <th><span class="font-big"> 9 </span><br><span class="font-small"> 09.00 - 10.00</span></th>
                <th><span class="font-big"> 10 </span><br><span class="font-small"> 10.00 - 11.00</span></th>
                <th><span class="font-big"> 11 </span><br><span class="font-small"> 11.00 - 12.00</span></th>
                <th><span class="font-big"> 12 </span><br><span class="font-small"> 12.00 - 13.00</span></th>
                <th><span class="font-big"> 1 </span><br><span class="font-small"> 13.00 - 14.00</span></th>
                <th><span class="font-big"> 2 </span><br><span class="font-small"> 14.00 - 15.00</span></th>
                <th><span class="font-big"> 3 </span><br><span class="font-small"> 15.00 - 16.00</span></th>
                <th><span class="font-big"> 4 </span><br><span class="font-small"> 16.00 - 17.00</span></th>
                <th><span class="font-big"> 5 </span><br><span class="font-small"> 17.00 - 18.00</span></th>
                <th><span class="font-big"> 6 </span><br><span class="font-small"> 18.00 - 19.00</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scheduleData as $day => $timeSlots): ?>
            <tr>
                <td><?php echo htmlspecialchars($day); ?></td>
                <?php foreach ($timeSlots as $timeSlot => $courseName): ?>
                    <td><?php echo $courseName ?: ''; ?></td> <!-- Empty string instead of 'Free' -->
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <script>
    let btn = document.querySelector('#btn')
    let sidebar = document.querySelector('.sidebar')

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };

    //TABLE FUNCTIONING
    function loadSemesters(levelID) {
        if (levelID) {
            document.getElementById('semesterSelect').disabled = false;

            // Send AJAX request to fetch semesters based on the selected level
            fetch(`table_fetch_sem.php?level_id=${levelID}`)
                .then(response => response.json())
                .then(data => {
                    let semesterSelect = document.getElementById('semesterSelect');
                    semesterSelect.innerHTML = '<option value="">Select Semester</option>'; // Clear old options
                    data.semesters.forEach(semester => {
                        let option = document.createElement('option');
                        option.value = semester.Sem_ID;
                        option.text = semester.Sem_Number;
                        semesterSelect.appendChild(option);
                    });
                })
            .catch(error => console.error('Error fetching semesters:', error));
        }
    }

    function loadSections(semID) {
        if (semID) {
            document.getElementById('sectionSelect').disabled = false;

            // Send AJAX request to fetch sections based on the selected semester
            fetch(`table_fetch_sections.php?sem_id=${semID}`)
                .then(response => response.json())
                .then(data => {
                    let sectionSelect = document.getElementById('sectionSelect');
                    sectionSelect.innerHTML = '<option value="">Select Section</option>'; // Clear old options
                    data.sections.forEach(section => {
                        let option = document.createElement('option');
                        option.value = section.Section_ID;
                        option.text = section.Section_Number;
                        sectionSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching sections:', error));
        }
    }

    function loadSchedule(sectionID) {
        if (sectionID) {
            // Send AJAX request to fetch the schedule for the selected section
            fetch(`table_fetch_schedule.php?section_id=${sectionID}`)
                .then(response => response.json())
                .then(data => {
                    let scheduleTableBody = document.querySelector('.schedule tbody');
                    scheduleTableBody.innerHTML = ''; // Clear old schedule

                    // Loop through the schedule data and populate the table
                    for (const [day, timeSlots] of Object.entries(data.schedule)) {
                        let row = document.createElement('tr');
                        let dayCell = document.createElement('td');
                        dayCell.textContent = day;
                        row.appendChild(dayCell);

                        for (const [timeSlot, courseName] of Object.entries(timeSlots)) {
                            let timeSlotCell = document.createElement('td');
                            timeSlotCell.textContent = courseName || ''; // Show empty if no course
                            row.appendChild(timeSlotCell);
                        }

                        scheduleTableBody.appendChild(row);
                    }
                })
                .catch(error => console.error('Error fetching schedule:', error));
        }
    }
    </script>