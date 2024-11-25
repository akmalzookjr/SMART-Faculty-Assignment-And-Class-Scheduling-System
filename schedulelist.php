<?php
include("connect.php");

session_start();

// Check if the user is logged in and has the role 'dean'
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'dean') {
    // Redirect to login page if not logged in or not a dean
    header('Location: login.php');
    exit();
}

// Retrieve session data
$userName = $_SESSION["name"] ?? "User";
$userType = $_SESSION["type"] ?? "User Type";  // Changed to 'role' for consistency

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

// Fetch all courses
$sql_courses = 'SELECT Course_ID, Course_Code, Course_Name FROM Course';
$result_courses = mysqli_query($conn, $sql_courses);
$courses = mysqli_fetch_all($result_courses, MYSQLI_ASSOC);


// Fetch all course sections
$sql_course_sections = 'SELECT Course_Section_ID, Course_ID, Course_Section FROM Course_Section';
$result_course_sections = mysqli_query($conn, $sql_course_sections);
$courseSections = mysqli_fetch_all($result_course_sections, MYSQLI_ASSOC);

// Fetch all lecturers
$sql_lecturers = 'SELECT Lect_ID, Lect_Name FROM Lecturer';
$result_lecturers = mysqli_query($conn, $sql_lecturers);
$lecturers = mysqli_fetch_all($result_lecturers, MYSQLI_ASSOC);

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<style>
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
            <div class="header-container">
                <div class="title-and-search">
                    <h1>Schedule List</h1>
                </div>
            </div>
            <div class="filter-container">
                <div class="">
                    <div class="top-filters">
                        <button type="button" onclick="resetFilters()" class="reset-btn">Reset</button>
                    </div>

                    <!-- Dropdown Filters -->
                    <div class="dropdown-filters">
                        <div class="filter">
                            <label for="levelFilter">Level:</label>
                            <select id="levelFilter" onchange="filterStudents()">
                                <option value="">All Levels</option>
                                <?php foreach ($levels as $level): ?>
                                <option value="<?php echo htmlspecialchars($level['Level_ID']); ?>">
                                    <?php echo htmlspecialchars($level['Level_Name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter">
                            <label for="semesterFilter">Semester:</label>
                            <select id="semesterFilter" onchange="filterStudents()">
                                <option value="">All Semesters</option>
                            </select>
                        </div>
                        <div class="filter">
                            <label for="sectionFilter">Section:</label>
                            <select id="sectionFilter" onchange="filterStudents()">
                                <option value="">All Sections</option>
                            </select>
                        </div>
                    </div>
                </div>
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
    </div>
    <!-- Modal Structure -->
    <div id="scheduleModal" class="schedule-modal">
        <div class="schedule-modal-content">
            <span class="schedule-close-modal">&times;</span>
            <h2>Assign Course to Schedule</h2>

            <!-- Display Selected Level, Semester, and Section -->
            <div class="modal-details">
    <p><strong>Level:</strong> <span id="modalLevel"></span></p>
    <p><strong>Semester:</strong> <span id="modalSemester"></span></p>
    <p><strong>Section:</strong> <span id="modalSection"></span></p>
    <p><strong>Day:</strong> <span id="modalDayDisplay"></span></p>
    <p><strong>Time Slot:</strong> <span id="modalTimeSlotDisplay"></span></p>
</div>


            <form id="assignCourseForm">
                <input type="hidden" id="modalDay" name="day">
                <input type="hidden" id="modalTimeSlot" name="timeSlot">

                <div class="form-group">
                    <label for="courseSelect">Select Course:</label>
                    <select id="courseSelect" required>
                        <option value="">Select Course</option>
                        <!-- Populate courses dynamically via AJAX -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="courseSectionSelect">Select Course Section:</label>
                    <select id="courseSectionSelect" required>
                        <option value="">Select Section</option>
                        <!-- Sections will be populated based on the selected course -->
                    </select>
                </div>
                <div class="form-group">
    <label for="lecturerSelect">Select Lecturer:</label>
    <select id="lecturerSelect" required>
        <option value="">Select Lecturer</option>
        <!-- Populate lecturers dynamically via AJAX -->
    </select>
    <div>
        <input type="checkbox" id="noLecturerCheckbox" />
        <label for="noLecturerCheckbox">No Lecturer</label>
    </div>
</div>


                <button type="submit" class="schedule-submit-btn">Assign Course</button>
                <button type="button" id="clearTimeSlotBtn" class="schedule-submit-btn" style="margin-top: 10px">Clear Time Slot</button>

            </form>
        </div>
    </div>
</div>
</html>
<script>
    
    let btn = document.querySelector('#btn')
    let sidebar = document.querySelector('.sidebar')

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };
    // FILTER
    function filterStudents() {
    const level = document.getElementById('levelFilter').value;
    const semester = document.getElementById('semesterFilter').value;
    const section = document.getElementById('sectionFilter').value;

    console.log('Level:', level, 'Semester:', semester, 'Section:', section);

    if (!level || !semester || !section) {
        console.warn('Please select Level, Semester, and Section.');
        return;
    }

    // Call the function to load the schedule based on the selected filters
    loadSchedule(section);
}


    //TABLE FUNCTIONING
    document.addEventListener("DOMContentLoaded", function () {
        const levelFilter = document.getElementById("levelFilter");
        const semesterFilter = document.getElementById("semesterFilter");
        const sectionFilter = document.getElementById("sectionFilter");

        // Initially disable semester and section dropdowns
        semesterFilter.disabled = true;
        sectionFilter.disabled = true;

        // Load semesters based on selected level
        levelFilter.addEventListener("change", function () {
            const levelID = this.value;
            if (levelID) {
                semesterFilter.disabled = false;
                semesterFilter.innerHTML = '<option value="">Select Semester</option>';
                sectionFilter.innerHTML = '<option value="">Select Section</option>';
                sectionFilter.disabled = true;

                fetch(`table_fetch_sem.php?level_id=${levelID}`)
                    .then(response => response.json())
                    .then(data => {
                        data.semesters.forEach(semester => {
                            const option = document.createElement("option");
                            option.value = semester.Sem_ID;
                            option.textContent = semester.Sem_Number;
                            semesterFilter.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error fetching semesters:", error));
            } else {
                semesterFilter.disabled = true;
                sectionFilter.disabled = true;
                semesterFilter.innerHTML = '<option value="">Select Semester</option>';
                sectionFilter.innerHTML = '<option value="">Select Section</option>';
            }
        });

        // Load sections based on selected semester
        semesterFilter.addEventListener("change", function () {
            const semID = this.value;
            if (semID) {
                sectionFilter.disabled = false;
                sectionFilter.innerHTML = '<option value="">Select Section</option>';

                fetch(`table_fetch_sections.php?sem_id=${semID}`)
                    .then(response => response.json())
                    .then(data => {
                        data.sections.forEach(section => {
                            const option = document.createElement("option");
                            option.value = section.Section_ID;
                            option.textContent = section.Section_Number;
                            sectionFilter.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error fetching sections:", error));
            } else {
                sectionFilter.disabled = true;
                sectionFilter.innerHTML = '<option value="">Select Section</option>';
            }
        });

        // Load schedule based on selected section
        sectionFilter.addEventListener("change", function () {
            const sectionID = this.value;
            if (sectionID) {
                loadSchedule(sectionID);
            }
        });

        // Reset button functionality
        document.querySelector(".reset-btn").addEventListener("click", function () {
            levelFilter.value = "";
            semesterFilter.value = "";
            sectionFilter.value = "";
            semesterFilter.disabled = true;
            sectionFilter.disabled = true;
            semesterFilter.innerHTML = '<option value="">Select Semester</option>';
            sectionFilter.innerHTML = '<option value="">Select Section</option>';
            clearSchedule(); // Clear the schedule when reset
        });
    });

    // Function to load schedule for the selected section
    function loadSchedule(sectionID) {
        fetch(`table_fetch_schedule.php?section_id=${sectionID}`)
            .then(response => response.json())
            .then(data => {
                const scheduleTableBody = document.querySelector('.schedule tbody');
                scheduleTableBody.innerHTML = ""; // Clear current schedule

                for (const [day, timeSlots] of Object.entries(data.schedule)) {
                    const row = document.createElement("tr");
                    const dayCell = document.createElement("td");
                    dayCell.textContent = day;
                    row.appendChild(dayCell);

                    for (const [timeSlot, courseName] of Object.entries(timeSlots)) {
                        const cell = document.createElement("td");
                        cell.textContent = courseName || ""; // Empty if no course assigned
                        row.appendChild(cell);
                    }

                    scheduleTableBody.appendChild(row);
                }

                // Reapply event listeners to new schedule cells
                applyScheduleCellClickListeners();
            })
            .catch(error => console.error("Error loading schedule:", error));
    }

    // Function to reapply click listeners on schedule cells
function applyScheduleCellClickListeners() {
    document.querySelectorAll('.schedule tbody td').forEach(cell => {
        cell.addEventListener('click', function () {
            const day = this.parentElement.firstChild.textContent; // Get the day from the row
            const timeSlotIndex = this.cellIndex - 1; // Get time slot index (minus 1 to skip the "Day" column)
            const courseName = this.textContent.trim(); // Get the current content of the cell

            // Get the current selected filters (Level, Semester, Section)
            const level = document.getElementById('levelFilter').value;
            const semester = document.getElementById('semesterFilter').value;
            const section = document.getElementById('sectionFilter').value;

            // Set hidden fields in the modal
            document.getElementById('modalDay').value = day;
            document.getElementById('modalTimeSlot').value = timeSlotIndex;

            // Display day and time slot in modal
            document.getElementById('modalDayDisplay').textContent = day;
            document.getElementById('modalTimeSlotDisplay').textContent = `${timeSlotIndex + 8}:00 - ${timeSlotIndex + 9}:00`;

            // Display Level, Semester, and Section in the modal
            document.getElementById('modalLevel').textContent = level ? document.querySelector(`#levelFilter option[value="${level}"]`).text : 'Not selected';
            document.getElementById('modalSemester').textContent = semester ? document.querySelector(`#semesterFilter option[value="${semester}"]`).text : 'Not selected';
            document.getElementById('modalSection').textContent = section ? document.querySelector(`#sectionFilter option[value="${section}"]`).text : 'Not selected';

            // Fetch existing assignment data if the cell has content
            if (courseName) {
                const sectionId = document.getElementById('sectionFilter').value; // Current section

                // Fetch course, section, and lecturer details
                fetch(`fetch_course_lecturer_history.php?day=${day}&timeSlotIndex=${timeSlotIndex}&section=${sectionId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Prepopulate course
                        if (data.courseId) {
                            document.getElementById('courseSelect').value = data.courseId;
                            $('#courseSelect').trigger('change'); // Trigger change for dependent dropdowns
                        }

                        // Prepopulate course section
                        if (data.courseSectionId) {
                            setTimeout(() => {
                                document.getElementById('courseSectionSelect').value = data.courseSectionId;
                                $('#courseSectionSelect').trigger('change');
                            }, 500); // Wait for course sections to populate
                        }

                        // Prepopulate lecturer
                        if (data.lecturerId) {
                            document.getElementById('lecturerSelect').value = data.lecturerId;
                            $('#lecturerSelect').trigger('change');
                        } else {
                            document.getElementById('noLecturerCheckbox').checked = true;
                            document.getElementById('lecturerSelect').disabled = true;
                        }
                    })
                    .catch(error => console.error('Error fetching course and lecturer details:', error));
            } else {
                // Reset modal if no data
                document.getElementById('courseSelect').value = '';
                document.getElementById('courseSectionSelect').value = '';
                document.getElementById('lecturerSelect').value = '';
                document.getElementById('noLecturerCheckbox').checked = false;
                document.getElementById('lecturerSelect').disabled = false;
            }

            // Open the modal
            document.getElementById('scheduleModal').style.display = 'flex';
        });
    });
}


    let assignedCourses = [];

function openModal(day, timeSlot, sectionId) {
    // Fetch assigned courses for this section
    fetch(`fetch_assigned_courses.php?section_id=${sectionId}`)
        .then(response => response.json())
        .then(data => {
            assignedCourses = data.courses.map(course => course.Course_ID); // Update global assignedCourses

            // Populate modal fields (if necessary)
            document.getElementById('modalDay').value = day;
            document.getElementById('modalTimeSlot').value = timeSlot;

            // Reset and repopulate dropdowns
            $('#courseSelect').val('').trigger('change');
            $('#lecturerSelect').val('').trigger('change');
            $('#courseSectionSelect').val('').trigger('change');

            // Show the modal
            document.getElementById('scheduleModal').style.display = 'flex';
        })
        .catch(error => console.error('Error fetching assigned courses:', error));
}

// CLEAR
document.getElementById('clearTimeSlotBtn').addEventListener('click', function () {
    const day = document.getElementById('modalDay').value;
    const timeSlotIndex = document.getElementById('modalTimeSlot').value;
    const section = document.getElementById('sectionFilter').value;

    if (!day || !timeSlotIndex || !section) {
        alert('Please select a time slot to clear.');
        return;
    }

    const timeSlot = timeSlotMapping[timeSlotIndex]; // Map index to time slot string

    if (!confirm('Are you sure you want to clear this time slot?')) {
        return;
    }

    $.ajax({
        url: 'clear_timeslot.php',
        type: 'POST',
        data: { day, timeSlot, section },
        success: function (response) {
            console.log('Server response:', response);
            try {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            } catch (e) {
                console.error('Invalid JSON response:', response);
                alert('An unexpected error occurred.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            alert('Failed to clear the time slot. Check the console for details.');
        },
    });
});




    // Call the applyScheduleCellClickListeners function after the initial load
    document.addEventListener("DOMContentLoaded", function () {
        applyScheduleCellClickListeners();
    });

    // Reset button functionality
    document.querySelector(".reset-btn").addEventListener("click", function () {
        levelFilter.value = "";
        semesterFilter.value = "";
        sectionFilter.value = "";
        semesterFilter.disabled = true;
        sectionFilter.disabled = true;
        semesterFilter.innerHTML = '<option value="">Select Semester</option>';
        sectionFilter.innerHTML = '<option value="">Select Section</option>';
        clearSchedule(); // Clear the schedule when reset
    });

    // Function to clear the schedule but keep the table layout
    function clearSchedule() {
        const scheduleTableBody = document.querySelector('.schedule tbody');
        scheduleTableBody.innerHTML = ""; // Clear current content

        // Re-create the table structure without any content
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        const timeSlots = 11; // Number of time slots per day (8-9, 9-10, etc.)

        days.forEach(day => {
            const row = document.createElement("tr");
            const dayCell = document.createElement("td");
            dayCell.textContent = day;
            row.appendChild(dayCell);

            for (let i = 0; i < timeSlots; i++) {
                const cell = document.createElement("td");
                cell.textContent = ""; // Empty content for each time slot cell
                row.appendChild(cell);
            }

            scheduleTableBody.appendChild(row);
        });

        // Reapply event listeners to the cleared schedule cells
        applyScheduleCellClickListeners();
    }



    // SCHEDULE BOX MODAL
    // Open modal when clicking on a schedule cell
    document.querySelectorAll('.schedule tbody td').forEach(cell => {
        cell.addEventListener('click', function () {
            // Check if cell is empty before opening modal
            if (!this.textContent.trim()) {
                const day = this.parentElement.firstChild.textContent; // Get the day from the row
                const timeSlot = this.cellIndex - 1; // Get time slot index (minus 1 to skip the "Day" column)

                // Set hidden fields in the modal
                document.getElementById('modalDay').value = day;
                document.getElementById('modalTimeSlot').value = timeSlot;

                // Open the modal
                document.getElementById('scheduleModal').style.display = 'flex';
            }
        });
    });

    // Close modal
    document.querySelector('.schedule-close-modal').onclick = function () {
        document.getElementById('scheduleModal').style.display = 'none';
    };

    // Close modal when clicking outside of modal content
    window.onclick = function (event) {
        const modal = document.getElementById('scheduleModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Handle form submission for assigning a course
    const timeSlotMapping = [
    '8-9', '9-10', '10-11', '11-12', '12-1',
    '1-2', '2-3', '3-4', '4-5', '5-6', '6-7'
];


document.getElementById('assignCourseForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const level = document.getElementById('levelFilter').value;
    const semester = document.getElementById('semesterFilter').value;
    const section = document.getElementById('sectionFilter').value;
    const day = document.getElementById('modalDay').value;
    const timeSlotIndex = document.getElementById('modalTimeSlot').value;
    const courseSection = document.getElementById('courseSectionSelect').value;
    const lecturerSelect = document.getElementById('lecturerSelect');
    const noLecturerCheckbox = document.getElementById('noLecturerCheckbox');

    const timeSlot = timeSlotMapping[parseInt(timeSlotIndex)];
    const lecturer = noLecturerCheckbox.checked ? null : lecturerSelect.value;

    console.log({ level, semester, section, day, timeSlot, courseSection, lecturer });

    if (!level || !semester || !section || !day || !timeSlot || !courseSection || (!lecturer && !noLecturerCheckbox.checked)) {
        alert('All fields are required.');
        return;
    }

    $.ajax({
        url: 'save_assignment.php',
        type: 'POST',
        data: {
            level: level,
            semester: semester,
            section: section,
            day: day,
            timeSlot: timeSlot,
            courseSection: courseSection,
            lecturer: lecturer,
        },
        success: function (res) {
            if (res.status === 'success') {
                alert(res.message);

                // Store the selected filters in local storage
                localStorage.setItem('filterLevel', level);
                localStorage.setItem('filterSemester', semester);
                localStorage.setItem('filterSection', section);

                // Refresh the page after a slight delay
                setTimeout(() => location.reload(), 1000);
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            alert(`Failed to save the assignment. Server error: ${jqXHR.responseText}`);
        },
    });

    document.getElementById('scheduleModal').style.display = 'none';
});

// Apply saved filters on page load
document.addEventListener('DOMContentLoaded', function () {
    const savedLevel = localStorage.getItem('filterLevel');
    const savedSemester = localStorage.getItem('filterSemester');
    const savedSection = localStorage.getItem('filterSection');

    if (savedLevel) {
        document.getElementById('levelFilter').value = savedLevel;
        document.getElementById('levelFilter').dispatchEvent(new Event('change'));
    }

    if (savedSemester) {
        // Wait for the semester dropdown to be populated before selecting the saved value
        setTimeout(() => {
            document.getElementById('semesterFilter').value = savedSemester;
            document.getElementById('semesterFilter').dispatchEvent(new Event('change'));

            // Wait for sections to load before setting the section value
            setTimeout(() => {
                if (savedSection) {
                    document.getElementById('sectionFilter').value = savedSection;
                    document.getElementById('sectionFilter').dispatchEvent(new Event('change'));
                }
            }, 500); // Adjust delay to ensure sections are loaded
        }, 500); // Adjust delay to ensure semesters are loaded
    }
});





document.getElementById('noLecturerCheckbox').addEventListener('change', function () {
    const lecturerSelect = document.getElementById('lecturerSelect');
    lecturerSelect.disabled = this.checked;
    if (this.checked) {
        lecturerSelect.value = ""; // Clear the selection
    }
});





    // Helper function to get the row index based on the day
    function getDayRowIndex(day) {
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        return days.indexOf(day) + 1; // Add 1 because nth-child index starts from 1
    }


    const courses = <?php echo json_encode($courses); ?>;
    const lecturers = <?php echo json_encode($lecturers); ?>;
    const courseSections = <?php echo json_encode($courseSections); ?>;

    document.addEventListener("DOMContentLoaded", function () {
        const courseSelect = $('#courseSelect');
        const lecturerSelect = $('#lecturerSelect');
        const courseSectionSelect = $('#courseSectionSelect');

        // Populate course options with both Course Code and Course Name
        courseSelect.empty().append('<option value="">Select Course</option>');
        courses.forEach(course => {
            const optionText = `${course.Course_Code} - ${course.Course_Name}`;
            const option = new Option(optionText, course.Course_ID, false, false);
            courseSelect.append(option);
        });

        // Populate lecturer options
        lecturerSelect.empty().append('<option value="">Select Lecturer</option>');
        lecturers.forEach(lecturer => {
            const option = new Option(lecturer.Lect_Name, lecturer.Lect_ID, false, false);
            lecturerSelect.append(option);
        });

        // Initialize Select2 for searchable dropdowns
        courseSelect.select2({
            placeholder: "Select Course",
            allowClear: true
        });
        lecturerSelect.select2({
            placeholder: "Select Lecturer",
            allowClear: true
        });
        courseSectionSelect.select2({
            placeholder: "Select Section",
            allowClear: true
        });

        // Load sections based on the selected course
        courseSelect.on("change", function () {
            const selectedCourseId = $(this).val();
            courseSectionSelect.empty().append('<option value="">Select Section</option>'); // Clear previous options

            // Populate sections dynamically
            const filteredSections = courseSections.filter(section => section.Course_ID == selectedCourseId);
            filteredSections.forEach(section => {
                const option = new Option(`Section ${section.Course_Section}`, section.Course_Section_ID, false, false);
                courseSectionSelect.append(option);
            });

            // Trigger Select2 update for course sections
            courseSectionSelect.trigger('change');
        });
    });




</script>