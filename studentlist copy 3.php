<?php
include("connect.php");

if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
}

// Query for fetching students
// Query for fetching students
$sql = 'SELECT Student.Stud_ID, Student.Stud_Name, 
Section.Section_Number, Section.Sem_ID, 
Semester.Sem_ID, Semester.Sem_Number, Semester.Level_ID, 
Level.Level_ID, Level.Level_Name, 
COUNT(Course_Student.Course_ID) AS Total_Course
FROM Student 
INNER JOIN Section ON Student.Section_ID = Section.Section_ID 
INNER JOIN Semester ON Section.Sem_ID = Semester.Sem_ID 
INNER JOIN Level ON Semester.Level_ID = Level.Level_ID 
LEFT JOIN Course_Student ON Course_Student.Stud_ID = Student.Stud_ID 
LEFT JOIN Course ON Course.Course_ID = Course_Student.Course_ID
GROUP BY Student.Stud_ID, Student.Stud_Name, 
Section.Section_Number, Section.Sem_ID, 
Semester.Sem_ID, Semester.Sem_Number, Semester.Level_ID, 
Level.Level_ID, Level.Level_Name
ORDER BY Student.Section_ID';

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


// Fetch students
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_all($result, MYSQLI_ASSOC);

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

// Manually add 'Test Course' to Monday's schedule for 4-5, 5-6, and 6-7 time slots
// $scheduleData['Monday']['4-5'] = 'Test Course';
// $scheduleData['Monday']['5-6'] = 'Test Course';
// $scheduleData['Monday']['6-7'] = 'Test Course';




// Close the connection (optional)
mysqli_free_result($result);
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
        <header>
            <div class="logo">
                <img src="icon uptm.png" alt="uptm" class="logo-small">
            </div>
            <ul class="nav_links">
                <button class="icon bg-icon"><i class="fas fa-moon"></i></button>
                <button class="icon bg-icon"><i class="fas fa-bell"></i></button>
                <button class="icon bg-icon"><i class="fas fa-cog"></i></button>
                <button class="icon bg-icon"><i class="fas fa-calendar"></i></button>
            </ul>
        </header>
        <div class="middle">
            <div class="header-container">
                <div class="title-and-search">
                    <h1>Student List</h1>
                    <div class="search-container">
                        <input type="text" id="searchBar" placeholder="Search by student name...">
                    </div>
                </div>
                <button class="add-btn"><i class="fas fa-plus"></i></button>
            </div>
        <!-- Filter dropdowns -->
        <div class="filter-container">
            <!-- Repeated Course Toggle and Reset Button -->
            <div class="top-filters">
                <label class="switch">
                    <input type="checkbox" id="repeatedCourseFilter" onchange="filterStudents()">
                    <span class="slider round"></span>
                </label>
                <label for="repeatedCourseFilter" class="toggle-label">Repeated Course</label>
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

        <div id="addStudentModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h2>Add New Student</h2>
            <form id="addStudentForm">
            <div class="form-group">
                <label for="studentName">Student Name:</label>
                <input type="text" id="studentName" name="studentName" required>
            </div>
            <div class="form-group">
                <label for="levelSelect">Level:</label>
                <select id="levelSelect" name="levelSelect" required onchange="loadSemesters(this.value)">
                    <option value="">Select Level</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level['Level_ID']); ?>">
                            <?php echo htmlspecialchars($level['Level_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="semesterSelect">Semester:</label>
                <select id="semesterSelect" name="semesterSelect" required disabled onchange="loadSections(this.value)">
                    <option value="">Select Semester</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sectionSelect">Section:</label>
                <select id="sectionSelect" name="sectionSelect" required disabled>
                    <option value="">Select Section</option>
                </select>
            </div>
            <button type="submit" class="submit-btn">Add Student</button>
        </form>
            </div>
        </div>
        <!-- Student list table -->
        <table class="studentlist" id="studentTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Section Number</th>
                    <th>Semester</th>
                    <th>Level</th>
                    <th>Repeated Course</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php foreach($student as $s): ?>
                    <tr class="student-row" 
                        data-stud-id="<?php echo $s['Stud_ID']; ?>"
                        data-stud-name="<?php echo htmlspecialchars($s['Stud_Name']); ?>"
                        data-section-number="<?php echo htmlspecialchars($s['Section_Number']); ?>"
                        data-semester="<?php echo htmlspecialchars($s['Sem_Number']); ?>"
                        data-level="<?php echo htmlspecialchars($s['Level_Name']); ?>"
                        data-repeated-course="<?php echo htmlspecialchars($s['Total_Course']); ?>">
                        <td><?php echo htmlspecialchars($s['Stud_Name']); ?></td>
                        <td><?php echo htmlspecialchars($s['Section_Number']); ?></td>
                        <td><?php echo htmlspecialchars($s['Sem_Number']); ?></td>
                        <td><?php echo htmlspecialchars($s['Level_Name']); ?></td>
                        <td><?php echo htmlspecialchars($s['Total_Course']); ?></td>
                    </tr>

                <?php endforeach; ?>
            </tbody> 
        </table>
        <div class="student-detail" id="studentDetail">
            <!-- Details will be dynamically inserted here -->
            <h1>akmal</h1>
        </div>
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
</div>
<div id="studentModal" class="list-modal">
    <div class="list-modal-content">
        <span class="close">&times;</span>
        <div id="modalBody">Details will be displayed here...</div>
    </div>
</div>

</body>

<script>
    let btn = document.querySelector('#btn')
    let sidebar = document.querySelector('.sidebar')

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };

    document.addEventListener("DOMContentLoaded", function() {
        const searchBar = document.getElementById('searchBar');
        const studentRows = document.querySelectorAll('.student-row');

        searchBar.addEventListener('input', function() {
            const searchText = searchBar.value.toLowerCase();

            studentRows.forEach(row => {
                const studentName = row.dataset.studName.toLowerCase();
                
                if (studentName.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // FILTER
        function filterStudents() {
    const levelID = document.getElementById("levelFilter").value;
    const semesterID = document.getElementById("semesterFilter").value;
    const sectionID = document.getElementById("sectionFilter").value;
    const repeatedCourse = document.getElementById("repeatedCourseFilter").checked ? 1 : 0;

    fetch(`filter_students.php?level_id=${levelID}&semester_id=${semesterID}&section_id=${sectionID}&repeated_course=${repeatedCourse}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);  // Debugging line
            const studentTableBody = document.getElementById("studentTableBody");
            if (Array.isArray(data)) {
                studentTableBody.innerHTML = data.map(student => `
                    <tr class="student-row" 
                        data-stud-id="${student.Stud_ID}"
                        data-stud-name="${student.Stud_Name}"
                        data-section-number="${student.Section_Number}"
                        data-semester="${student.Sem_Number}"
                        data-level="${student.Level_Name}"
                        data-repeated-course="${student.Total_Course}">
                        <td>${student.Stud_Name}</td>
                        <td>${student.Section_Number}</td>
                        <td>${student.Sem_Number}</td>
                        <td>${student.Level_Name}</td>
                        <td>${student.Total_Course}</td>
                    </tr>
                `).join('');
            } else {
                studentTableBody.innerHTML = `<tr><td colspan="5">No data found</td></tr>`;
            }
            // Reattach click listeners to the student rows
            attachStudentRowClickListeners();
        })
        .catch(error => console.error('Error fetching student data:', error));
}

// Function to attach click event listeners to student rows
function attachStudentRowClickListeners() {
    document.querySelectorAll('.student-row').forEach(row => {
        row.addEventListener('click', function () {
            const studentId = this.dataset.studId; // Get the student ID from the clicked row
            const studentList = document.querySelector('.studentlist');
            const studentDetail = document.getElementById('studentDetail');
            const currentActive = document.querySelector('.student-row.active');

            if (currentActive === this && studentDetail.classList.contains('active')) {
                this.classList.remove('active');
                studentDetail.classList.remove('active');
                studentList.classList.remove('active');
                studentDetail.innerHTML = '';
            } else {
                if (currentActive) {
                    currentActive.classList.remove('active');
                }
                this.classList.add('active');

                fetch(`table_get_courses.php?student_id=${studentId}`)
                    .then(response => response.json())
                    .then(courses => {
                        const coursesHtml = courses.map(course => `
                            <tr>
                                <td>${course.Course_Name}</td>
                                <td>${course.Course_CH}</td>
                                <td>${course.Assign_Sche_ID ? 'Assigned' : 'Unassigned'}</td>
                            </tr>
                        `).join('');

                        studentDetail.innerHTML = `
                            <h1>Details for ${this.dataset.studName}</h1>
                            <p>Section: ${this.dataset.sectionNumber}</p>
                            <p>Semester: ${this.dataset.semester}</p>
                            <p>Level: ${this.dataset.level}</p>
                            <p>Repeated Courses: ${this.dataset.repeatedCourse}</p>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Credit Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>${coursesHtml}</tbody>
                            </table>
                            <button class="detail-button" onclick="showDetails(${studentId})">Detail</button>
                        `;
                        studentDetail.classList.add('active');
                        studentList.classList.add('active');
                    })
                    .catch(error => console.error('Error fetching courses:', error));
            }
        });
    });
}

    document.getElementById("levelFilter").addEventListener("change", () => {
        const levelID = document.getElementById("levelFilter").value;
        document.getElementById("semesterFilter").disabled = !levelID;
        document.getElementById("sectionFilter").disabled = true;

        if (levelID) {
            fetch(`fetch_semesters.php?level_id=${levelID}`)
                .then(response => response.json())
                .then(data => {
                    const semesterSelect = document.getElementById("semesterFilter");
                    semesterSelect.innerHTML = '<option value="">All Semesters</option>';
                    data.forEach(semester => {
                        semesterSelect.innerHTML += `<option value="${semester.Sem_ID}">${semester.Sem_Number}</option>`;
                    });
                });
        }
    });

    document.getElementById("semesterFilter").addEventListener("change", () => {
        const semesterID = document.getElementById("semesterFilter").value;
        document.getElementById("sectionFilter").disabled = !semesterID;

        if (semesterID) {
            fetch(`fetch_sections.php?sem_id=${semesterID}`)
                .then(response => response.json())
                .then(data => {
                    const sectionSelect = document.getElementById("sectionFilter");
                    sectionSelect.innerHTML = '<option value="">All Sections</option>';
                    data.forEach(section => {
                        sectionSelect.innerHTML += `<option value="${section.Section_ID}">${section.Section_Number}</option>`;
                    });
                });
        }
    });

    function resetFilters() {
    // Clear the checkbox
    document.getElementById("repeatedCourseFilter").checked = false;

    // Reset the dropdowns
    document.getElementById("levelFilter").value = "";
    document.getElementById("semesterFilter").value = "";
    document.getElementById("sectionFilter").value = "";

    // Disable the semester and section filters
    document.getElementById("semesterFilter").disabled = true;
    document.getElementById("sectionFilter").disabled = true;

    // Trigger filterStudents to reload the student list with all data
    filterStudents();
}


    // ADD STUDENT
    document.addEventListener("DOMContentLoaded", function() {
    const addButton = document.querySelector(".add-btn");
    const modal = document.getElementById("addStudentModal");
    const closeModal = document.querySelector(".close-modal");
    const addStudentForm = document.getElementById("addStudentForm");
    let isSubmitting = false; // Flag to prevent multiple submissions

    // Open the modal
    addButton.onclick = function () {
        modal.style.display = "block";
        isSubmitting = false; // Reset the flag when modal opens
    };

    // Close the modal when clicking the close button
    closeModal.onclick = function () {
        modal.style.display = "none";
        addStudentForm.reset();
    };

    // Close the modal when clicking outside the modal content
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            addStudentForm.reset();
        }
    };

    // Ensure only one event listener is attached to the form
    addStudentForm.addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent default form submission

        // Check if a submission is already in progress
        if (isSubmitting) return;

        // Set the flag to indicate a submission is in progress
        isSubmitting = true;

        const formData = new FormData(addStudentForm);

        fetch("add_student.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Show success message
                modal.style.display = "none"; // Close modal
                addStudentForm.reset(); // Reset the form fields
                location.reload(); // Reload to update the student list
            } else {
                alert(data.message); // Show error message
                if (data.debug) {
                    console.log("Debug Info:", data.debug);
                }
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while adding the student.");
        })
        .finally(() => {
            // Reset the flag after submission completes
            isSubmitting = false;
        });
    }, { once: true }); // Add event listener with { once: true } to ensure it runs only once
});



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

    // STUDENT DETAILS
    document.querySelectorAll('.student-row').forEach(row => {
        row.addEventListener('click', function () {
            const studentId = this.dataset.studId; // Get the student ID from the clicked row
            const studentList = document.querySelector('.studentlist');
            const studentDetail = document.getElementById('studentDetail');
            const currentActive = document.querySelector('.student-row.active');

            if (currentActive === this && studentDetail.classList.contains('active')) {
                this.classList.remove('active');
                studentDetail.classList.remove('active');
                studentList.classList.remove('active');
                studentDetail.innerHTML = '';
            } else {
                if (currentActive) {
                    currentActive.classList.remove('active');
                }
                this.classList.add('active');

                fetch(`table_get_courses.php?student_id=${studentId}`)
                    .then(response => response.json())
                    .then(courses => {
                        const coursesHtml = courses.map(course => `
                            <tr>
                                <td>${course.Course_Name}</td>
                                <td>${course.Course_CH}</td>
                                <td>${course.Assign_Sche_ID ? 'Assigned' : 'Unassigned'}</td>
                            </tr>
                        `).join('');

                        studentDetail.innerHTML = `
                            <h1>Details for ${this.dataset.studName}</h1>
                            <p>Section: ${this.dataset.sectionNumber}</p>
                            <p>Semester: ${this.dataset.semester}</p>
                            <p>Level: ${this.dataset.level}</p>
                            <p>Repeated Courses: ${this.dataset.repeatedCourse}</p>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Credit Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>${coursesHtml}</tbody>
                            </table>
                            <button class="detail-button" onclick="showDetails(${studentId})">Detail</button>
                        `;
                        studentDetail.classList.add('active');
                        studentList.classList.add('active');
                    })
                    .catch(error => console.error('Error fetching courses:', error));
            }
        });
    });

    function showDetails(studentId) {
        const modal = document.getElementById('studentModal');
        const modalBody = document.getElementById('modalBody');

        fetch(`table_student_detail.php?student_id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    modalBody.innerHTML = `<p>Error: ${data.error}</p>`;
                } else {
                    // Display courses with action icons and toggleable schedule tables
                    const coursesHTML = data.Courses.split(', ').map(courseInfo => {
                        const [courseName, creditHours, assignScheId] = courseInfo.split('|');
                        const status = assignScheId !== '0' ? 'Assigned' : 'Unassigned';
                        let actions = `<i class="fas fa-table" style="cursor:pointer;" onclick="toggleCourseSchedule('${assignScheId}', '${courseName}')"></i>`;
                        if (assignScheId !== '0') {
                            actions += ` <i class="fas fa-trash-alt" style="cursor:pointer;color:red;" onclick="unassignCourse('${assignScheId}', ${studentId}, '${courseName}')"></i>`;
                        }
                        return `<tr>
                                    <td>${courseName}</td>
                                    <td>${creditHours}</td>
                                    <td>${status}</td>
                                    <td>${actions}</td>
                                </tr>
                                <tr id="schedule-${assignScheId}" style="display:none;">
                                    <td colspan="4">
                                        <div id="course-schedule-${assignScheId}">Loading schedule...</div>
                                    </td>
                                </tr>`;
                    }).join('');

                    // Filter unassigned courses
                    const unassignedCourses = data.Courses.split(', ').filter(courseInfo => {
                        const [ , , assignScheId] = courseInfo.split('|');
                        return assignScheId === '0'; // Only keep unassigned courses
                    });

                    let timeslotsHTML = '';
                    if (unassignedCourses.length > 0) {
                        // If there are unassigned courses
                        timeslotsHTML = unassignedCourses.map(courseInfo => {
                            const [courseName] = courseInfo.split('|');
                            const courseTimeslots = data.Timeslots.filter(slot => slot.Course === courseName);

                            if (courseTimeslots.length > 0) {
                                return courseTimeslots.map(slot => {
                                    return `<tr>
                                                <td>${slot.Course}</td>
                                                <td>${slot.Course_Section}</td>
                                                <td>${slot.Sem_Number}</td>
                                                <td>${slot.Section_Number}</td>
                                                <td>${slot.Day}</td>
                                                <td>${slot.Time_Slot}</td>
                                                <td><button onclick="assignToTimeslot('${slot.Assign_Sche_ID}', ${studentId}, '${courseName}', ${slot.Course_ID})">Assign</button></td>
                                            </tr>`;
                                }).join('');
                            } else {
                                return `<tr><td colspan="7">No schedule available for ${courseName}</td></tr>`;
                            }
                        }).join('');

                    } else {
                        // If all courses are assigned
                        timeslotsHTML = `<tr><td colspan="7">All courses are assigned.</td></tr>`;
                    }

                    // Build the final modal structure
                    modalBody.innerHTML = `
                        <h2>Details for ${data.Stud_Name}</h2>
                        <div>
                            <h3>Courses:</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Credit Hours</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>${coursesHTML}</tbody>
                            </table>
                        </div>
                        <div>
                            <h3>Available Timeslots (for Unassigned Courses):</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Course Section</th>
                                        <th>Semester</th>
                                        <th>Class Section</th>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>${timeslotsHTML}</tbody>
                            </table>
                        </div>
                    `;

                    modal.style.display = "block";
                }
            })
            .catch(error => {
                console.error('Failed to fetch details:', error);
                modalBody.innerHTML = `<p>Error: Unable to fetch details</p>`;
            });
    }

    // Function to toggle and load the course's schedule table
    function toggleCourseSchedule(assignScheId, courseName) {
        const scheduleRow = document.getElementById(`schedule-${assignScheId}`);
        const scheduleContainer = document.getElementById(`course-schedule-${assignScheId}`);

        // Toggle display of the schedule row
        if (scheduleRow.style.display === "none") {
            scheduleRow.style.display = "table-row";
            // Fetch the course schedule if it's not already loaded
            if (!scheduleContainer.dataset.loaded) {
                fetch(`fetch_course_schedule.php?assign_sche_id=${assignScheId}&course_name=${courseName}`)
                    .then(response => response.json())
                    .then(scheduleData => {
                        if (scheduleData.length > 0) {
                            let scheduleHTML = `
                                <table class="course-schedule">
                                    <thead>
                                        <tr>
                                            <th>Semester</th>
                                            <th>Class Section</th>
                                            <th>Day</th>
                                            <th>Time Slot</th>
                                            <th>Course Code</th>
                                            <th>Course Section</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;
                            scheduleData.forEach(slot => {
                                scheduleHTML += `
                                    <tr>
                                        <td>${slot.Semester}</td>
                                        <td>${slot.Section_Number}</td>
                                        <td>${slot.Day}</td>
                                        <td>${slot.Time_Slot}</td>
                                        <td>${slot.Course_Code}</td>
                                        <td>${slot.Course_Section}</td>
                                    </tr>`;
                            });
                            scheduleHTML += '</tbody></table>';
                            scheduleContainer.innerHTML = scheduleHTML;
                        } else {
                            scheduleContainer.innerHTML = `<p>No schedule available for ${courseName}</p>`;
                        }
                        scheduleContainer.dataset.loaded = true;
                    })
                    .catch(error => {
                        console.error('Failed to fetch course schedule:', error);
                        scheduleContainer.innerHTML = `<p>Error loading schedule</p>`;
                    });
            }
        } else {
            scheduleRow.style.display = "none";
        }
    }

    // ASSIGN COURSE TO TIMESLOT
    function assignToTimeslot(assignScheId, studentId, courseName, courseId) {
        console.log(`Assigning: ${courseName} with Course ID: ${courseId}`);  // Check what is being logged here

        if (confirm(`Want to assign the course '${courseName}' to this timeslot?`)) {
            fetch('table_assign_course_to_timeslot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ assignScheId, studentId, courseId })  // Make sure courseId is included here
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`The course '${courseName}' has been successfully assigned to the timeslot.`);
                    location.reload();  // Reload to see the changes
                } else {
                    alert('Failed to assign the course: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error assigning course to timeslot:', error);
                alert('An error occurred while assigning the course.');
            });
        }
    }

    // UNNASIGNED COURSE FROM TIMESLOT
    function unassignCourse(assignScheId, studentId, courseName) {
        if (confirm(`Do you want to unassign the course '${courseName}' from its timeslot?`)) {
            fetch('table_unassign_course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ assignScheId, studentId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`The course '${courseName}' has been successfully unassigned.`);
                    location.reload(); // Refresh to show changes
                } else {
                    alert('Failed to unassign the course: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error unassigning the course:', error);
                alert('An error occurred while unassigning the course.');
            });
        }
    }

    // Event listeners for closing the modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('studentModal');
        const closeButton = document.querySelector('.close');

        closeButton.onclick = function() {
            modal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    });
</script>

</html>