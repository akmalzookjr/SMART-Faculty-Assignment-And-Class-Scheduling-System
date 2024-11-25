<?php
session_start();

// Check if the user is logged in and has the role 'coordinator'
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'coordinator') {
    // Redirect to login page if not logged in or not a coordinator
    header('Location: login.php');
    exit();
}

// Retrieve session data
$userName = $_SESSION["name"] ?? "User";
$userType = $_SESSION["type"] ?? "User Type";  // Changed to 'role' for consistency

// Include the database connection
include 'connect.php';

// Fetch students with necessary details
$query = "SELECT 
    Student.Stud_ID, 
    Student.Stud_Name, 
    Section.Section_Number, 
    Semester.Sem_Number, 
    Level.Level_Name, 
    COUNT(Course_Student.Course_ID) AS Total_Repeated_Courses
FROM student 
INNER JOIN section ON Student.Section_ID = Section.Section_ID
INNER JOIN semester ON Section.Sem_ID = Semester.Sem_ID
INNER JOIN level ON Semester.Level_ID = Level.Level_ID
LEFT JOIN course_student ON course_student.Stud_ID = student.Stud_ID
GROUP BY Student.Stud_ID, Student.Stud_Name, Section.Section_Number, Semester.Sem_Number, Level.Level_Name
ORDER BY Section.Section_Number, Student.Stud_Name";

$result = mysqli_query($conn, $query);

$coursesResult = mysqli_query($conn, "SELECT Course_ID, Course_Name, Course_Code FROM course");
$courses = $coursesResult ? mysqli_fetch_all($coursesResult, MYSQLI_ASSOC) : [];


if ($result) {
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $students = []; // Set $students to an empty array if the query fails
}

// Fetch sections, semesters, and levels
$sectionsResult = mysqli_query($conn, "SELECT Section_ID, Section_Number FROM section");
$semestersResult = mysqli_query($conn, "SELECT Sem_ID, Sem_Number FROM semester");
$levelsResult = mysqli_query($conn, "SELECT Level_ID, Level_Name FROM level");

$sections = $sectionsResult ? mysqli_fetch_all($sectionsResult, MYSQLI_ASSOC) : [];
$semesters = $semestersResult ? mysqli_fetch_all($semestersResult, MYSQLI_ASSOC) : [];
$levels = $levelsResult ? mysqli_fetch_all($levelsResult, MYSQLI_ASSOC) : [];

// Close the connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="css/stylehome.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<style>
/* Add course container */
.add-course-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap; /* Allows wrapping if needed */
}

/* Limit the width of the dropdown */
#courseDropdown {
    max-width: 300px; /* Adjust width as needed */
    width: 250%; /* Ensures it adjusts within the container */
    overflow: hidden;
    text-overflow: ellipsis; /* Adds ellipsis for long text */
    white-space: nowrap; /* Prevents text wrapping */
}

/* Add course button */
.add-course-btn {
    background-color: #4CAF50; /* Green background */
    color: white;
    border: none;
    width: 100%;
    border-radius: 5px;
    padding: 8px 15px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.add-course-btn:hover {
    background-color: #45a049; /* Slightly darker green on hover */
}

/* Selected courses list */
.selected-courses-list {
    list-style-type: none;
    padding: 0;
    margin-top: 10px;
    max-height: 150px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    background-color: #f9f9f9;
}

/* Individual course item */
.selected-courses-list li {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 5px;
    background-color: #ffffff;
}

/* Remove button for courses */
.selected-courses-list li button {
    background-color: #ff4d4d; /* Red background */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 12px;
    transition: background-color 0.3s ease;
}

.selected-courses-list li button:hover {
    background-color: #e60000; /* Darker red on hover */
}

.studentlist {
    transition: all 0.5s ease;
}

.studentlist.active {
    width: 60%; /* Shrinks the student list container */
}

.student-detail {
    width: 0;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.student-detail.active {
    width: 35%;
    opacity: 1;
    visibility: visible;
    padding: 20px;
    margin-left: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.student-detail-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
}

.student-detail-info {
    font-size: 16px;
    margin-bottom: 15px;
    color: #555;
}

/* Header style consistent with other sections */
.section-header {
    background-color: #4CAF50; /* Use green for a consistent look */
    color: white;
    font-weight: bold;
}

.detail-button-container {
    display: flex;
    justify-content: space-between; /* Distributes buttons evenly */
    gap: 10px; /* Adds space between buttons */
    margin-top: 15px;
  
}

.detail-button {
    background-color: #4678b8; /* Button background color */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

.detail-button:hover {
    background-color: #345b91; /* Slightly darker on hover */
}

.add-course-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.remove-course-btn {
    background-color: #ff4d4d;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.remove-course-btn:hover {
    background-color: #e60000;
}

.course-entry {
    margin-bottom: 20px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.timeslot-list {
    list-style-type: none;
    padding: 0;
}

.timeslot-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 10px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
}

.timeslot-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}

.timeslot-btn:hover {
    background-color: #45a049;
}

.student-info {
    margin-bottom: 20px;
    font-size: 16px;
    line-height: 1.5;
    color: #444;
}

.courses-container-vertical {
    display: flex;
    flex-direction: column;
    gap: 15px; /* Space between course cards */
}

.course-card-vertical {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    width: 100%; /* Full width of the container */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.course-card-vertical:hover {
    transform: translateY(-5px);
}

.course-card-vertical h4 {
    margin: 0 0 10px;
    font-size: 18px;
    color: #333;
}

.course-card-vertical p {
    margin: 5px 0;
    font-size: 14px;
    color: #555;
}

.course-card-vertical p strong {
    color: #333;
}
.detail-button-container {
    width: 100%;
}
.student-detail-button {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    font-size: 16px;
    color: #fff;
    background-color: #4CAF50;
    border: none;
    border-radius: 5px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s;
    text-align: center;
}

.student-detail-button:hover {
    background-color: #45a049;
}

/* Assign Timeslot Modal */
#assignTimeslotModal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scrolling if needed */
    background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
}

/* Modal Content */
#assignTimeslotModal .modal-content {
    background-color: #fff;
    margin: 10% auto; /* Center vertically and horizontally */
    padding: 20px;
    border: 1px solid #888;
    width: 60%; /* Default width */
    max-height: 70vh; /* Limit height to 70% of the viewport */
    overflow-y: auto; /* Enable vertical scrolling */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
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
        <div class="header-container">
            <div class="title-and-search">
                <h1>Student List</h1>
                <div class="search-container">
                    <input type="text" id="searchBar" placeholder="Search by student name..." onkeyup="searchStudents()">
                </div>
            </div>
            <button class="add-btn" onclick="openAddStudentModal()"><i class="fas fa-plus"></i></button>
        </div>
        <div class="studentlist">
            <table class="studentlist">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Section Number</th>
                        <th>Semester</th>
                        <th>Level</th>
                        <th>Total Repeated Courses</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    <?php foreach ($students as $student): ?>
                        <tr class="student-row" 
                            data-student-id="<?php echo $student['Stud_ID']; ?>"> 
                            <td><?php echo htmlspecialchars($student['Stud_Name']); ?></td>
                            <td><?php echo htmlspecialchars($student['Section_Number']); ?></td>
                            <td><?php echo htmlspecialchars($student['Sem_Number']); ?></td>
                            <td><?php echo htmlspecialchars($student['Level_Name']); ?></td>
                            <td><?php echo htmlspecialchars($student['Total_Repeated_Courses']); ?></td>
                            <td>
                                                        <button class="edit-btn" onclick="openEditStudentModal(<?php echo $student['Stud_ID']; ?>, <?php echo $student['Total_Repeated_Courses']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>

                                <button class="delete-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <button class="schedule-btn" onclick="viewStudentSchedule(<?php echo $student['Stud_ID']; ?>)">
                                    <i class="fas fa-calendar-check"></i>
                                </button>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination-wrapper">
                <div id="paginationControls" class="pagination-controls"></div>
            </div>
        </div>
        <div class="student-detail" id="studentDetail">
            <!-- Student details will be dynamically inserted here -->
        </div>
    </div>
</div>
<!-- Edit Student Modal -->
<div id="editStudentModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditStudentModal()">&times;</span>
        <h2>Edit Student Details</h2>
        <form id="editStudentForm">
            <div class="form-group">
                <label for="editStudentName">Student Name:</label>
                <input type="text" id="editStudentName" name="studentName" required>
            </div>
            <div class="form-group">
                <label for="editLevel">Level:</label>
                <select id="editLevel" name="level" required onchange="loadSemestersForEdit(this.value)">
                    <option value="">Select Level</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level['Level_ID']); ?>">
                            <?php echo htmlspecialchars($level['Level_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="editSemester">Semester:</label>
                <select id="editSemester" name="semester" required disabled onchange="loadSectionsForEdit(this.value)">
                    <option value="">Select Semester</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editSectionNumber">Section Number:</label>
                <select id="editSectionNumber" name="sectionNumber" required disabled>
                    <option value="">Select Section</option>
                </select>
            </div>
            <button type="submit" class="submit-btn">Save Changes</button>
        </form>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeAddStudentModal()">&times;</span>
        <h2>Add New Student</h2>
        <form id="addStudentForm">
            <div class="form-group">
                <label for="studentName">Student Name:</label>
                <input type="text" id="studentName" name="studentName" required>
            </div>
            <div class="form-group">
                <label for="level">Level:</label>
                <select id="level" name="level" required onchange="loadSemesters(this.value)">
                    <option value="">Select Level</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level['Level_ID']); ?>">
                            <?php echo htmlspecialchars($level['Level_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="semester">Semester:</label>
                <select id="semester" name="semester" required disabled onchange="loadSections(this.value)">
                    <option value="">Select Semester</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sectionNumber">Section Number:</label>
                <select id="sectionNumber" name="sectionNumber" required disabled>
                    <option value="">Select Section</option>
                </select>
            </div>
            <div class="form-group">
                <label for="courseDropdown">Add Course:</label>
                <div class="add-course-container">
                    <select id="courseDropdown" style="width: 100%;">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course['Course_ID']); ?>">
                                <?php echo htmlspecialchars($course['Course_Code']) . ' - ' . htmlspecialchars($course['Course_Name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="add-course-btn" onclick="addCourse()">Add</button>
                </div>
            </div>
            <div class="form-group">
                <label>Selected Courses:</label>
                <ul id="selectedCourses" class="selected-courses-list"></ul>
            </div>
            <button type="submit" class="submit-btn">Add Student</button>
        </form>
    </div>
</div>
<!-- Detail Modal -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeDetailModal()">&times;</span>
        <h2>Student Detailed Information</h2>
        <div id="detailModalContent">
            <!-- Dynamic student details will load here -->
        </div>
        <div class="detail-button-container">
            
            <button id="saveButton" class="detail-button" onclick="saveStudentDetails()" style="display: none;">Save</button>
        </div>
    </div>
</div>
<div id="editCoursesModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditCoursesModal()">&times;</span>
        <h2>Edit Courses</h2>
        <form id="editCoursesForm">
            <div class="form-group">
                <label for="editCourseDropdown">Add Course:</label>
                <select id="editCourseDropdown" style="width: 100%;">
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['Course_ID']); ?>">
                            <?php echo htmlspecialchars($course['Course_Code']) . ' - ' . htmlspecialchars($course['Course_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="add-course-btn" onclick="addCourseToStudent()">Add</button>
            </div>
            <div class="form-group">
                <h3>Selected Courses</h3>
                <ul id="selectedCoursesList" class="selected-courses-list">
                    <!-- Populated dynamically -->
                </ul>
            </div>
            <button type="button" class="submit-btn" onclick="saveStudentCourses()">Save</button>
        </form>
    </div>
</div>
<!-- Assign Timeslot Modal -->
<div id="assignTimeslotModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeAssignTimeslotModal()">&times;</span>
        <h2>Assign Timeslot</h2>
        <div id="timeslotDetails">
            <!-- Courses with timeslot selectors will be populated here -->
        </div>
        <!-- <button class="submit-btn" onclick="submitTimeslot()">Submit</button> -->
    </div>
</div>





</body>
<script>

function viewStudentSchedule(studentId) {
    window.location.href = 'schedulestudent.php?student_id=' + studentId;
}

    document.addEventListener("DOMContentLoaded", function () {
    const studentRows = Array.from(document.querySelectorAll('.student-row'));
    const searchBar = document.getElementById('searchBar');
    const itemsPerPage = 12; // Number of students per page
    let currentPage = 1;
    let filteredStudents = studentRows; // Initially, all students are visible

    // Function to search and filter students
    function searchStudents() {
        const searchQuery = searchBar.value.toLowerCase();

        // Filter the students based on the search query
        filteredStudents = studentRows.filter(row => {
            const studentName = row.cells[0].innerText.toLowerCase();
            return studentName.includes(searchQuery);
        });

        currentPage = 1; // Reset to the first page after searching
        renderPage(currentPage); // Re-render the page to update pagination based on filtered rows
    }

    // Render the current page of students
    function renderPage(page) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Hide all students, then display only the ones for the current page
        studentRows.forEach(row => row.style.display = 'none'); // Hide all students first
        filteredStudents.forEach((student, index) => {
            if (index >= start && index < end) {
                student.style.display = ''; // Show only the ones in the current page
            }
        });

        renderPaginationControls();
    }

    // Render pagination controls based on the filtered students
    function renderPaginationControls() {
        const totalPages = Math.ceil(filteredStudents.length / itemsPerPage);
        const paginationControls = document.getElementById('paginationControls');
        paginationControls.innerHTML = ''; // Clear previous pagination controls

        if (filteredStudents.length === 0) {
            paginationControls.innerHTML = ''; // Clear pagination controls when no students are found
            return;
        }

        // Previous button
        const prevButton = document.createElement('button');
        prevButton.innerText = 'Previous';
        prevButton.disabled = (currentPage === 1);
        prevButton.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage(currentPage);
            }
        };
        paginationControls.appendChild(prevButton);

        // Page buttons
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.innerText = i;
            pageButton.classList.toggle('active', i === currentPage);
            pageButton.onclick = () => {
                currentPage = i;
                renderPage(currentPage);
            };
            paginationControls.appendChild(pageButton);
        }

        // Next button
        const nextButton = document.createElement('button');
        nextButton.innerText = 'Next';
        nextButton.disabled = (currentPage === totalPages);
        nextButton.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage(currentPage);
            }
        };
        paginationControls.appendChild(nextButton);
    }

    // Add the event listener to search bar
    searchBar.addEventListener('input', searchStudents);

    // Initial render of pagination with all students
    renderPage(currentPage);
});











    // ADD STUDENT
    function loadLevels() {
        fetch('studentlist_fetch_levels.php')
            .then(response => response.json())
            .then(data => {
                const levelDropdown = document.getElementById('level');
                levelDropdown.innerHTML = '<option value="">Select Level</option>';
                data.forEach(level => {
                    levelDropdown.innerHTML += `<option value="${level.Level_ID}">${level.Level_Name}</option>`;
                });
            })
            .catch(error => console.error('Error loading levels:', error));
    }


    // Load semesters based on selected level
    function loadSemesters(levelId) {
        const semesterDropdown = document.getElementById('semester');
        const sectionDropdown = document.getElementById('sectionNumber');

        // Reset dropdowns
        semesterDropdown.innerHTML = '<option value="">Select Semester</option>';
        sectionDropdown.innerHTML = '<option value="">Select Section</option>';
        semesterDropdown.disabled = true;
        sectionDropdown.disabled = true;

        if (levelId) {
            fetch(`studentlist_fetch_semesters.php?level_id=${levelId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        data.forEach(semester => {
                            semesterDropdown.innerHTML += `<option value="${semester.Sem_ID}">${semester.Sem_Number}</option>`;
                        });
                        semesterDropdown.disabled = false;
                    } else {
                        alert('No semesters found for the selected level.');
                    }
                })
                .catch(error => console.error('Error loading semesters:', error));
        }
    }

    // Load sections based on selected semester
    function loadSections(semesterId) {
        const sectionDropdown = document.getElementById('sectionNumber');

        // Reset dropdown
        sectionDropdown.innerHTML = '<option value="">Select Section</option>';
        sectionDropdown.disabled = true;

        if (semesterId) {
            fetch(`studentlist_fetch_sections.php?semester_id=${semesterId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        data.forEach(section => {
                            sectionDropdown.innerHTML += `<option value="${section.Section_ID}">${section.Section_Number}</option>`;
                        });
                        sectionDropdown.disabled = false;
                    } else {
                        alert('No sections found for the selected semester.');
                    }
                })
                .catch(error => console.error('Error loading sections:', error));
        }
    }

    // Modal functionality
    const addStudentModal = document.getElementById('addStudentModal');

    function openAddStudentModal() {
    loadLevels(); // Dynamically load levels when the modal opens
    const addStudentModal = document.getElementById('addStudentModal');
    addStudentModal.style.display = 'block';
}

function assignCourseToTimeslot(courseId, studentId) {
    fetch('assign_timeslot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ courseId, studentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Course successfully assigned to the timeslot!');
            location.reload(); // Reload to update the UI
        } else {
            alert('Error assigning course: ' + data.message);
        }
    })
    .catch(error => console.error('Error assigning course:', error));
}

    function closeAddStudentModal() {
        addStudentModal.style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target === addStudentModal) {
            addStudentModal.style.display = 'none';
        }
    };

    let selectedCourses = [];

    function addCourseToDetail() {
        const courseDropdown = document.getElementById('courseDropdown');
        const selectedCourseId = courseDropdown.value;
        const selectedCourseText = courseDropdown.options[courseDropdown.selectedIndex]?.text;

        console.log("Selected Course ID:", selectedCourseId);
        console.log("Selected Course Text:", selectedCourseText);

        if (!selectedCourseId) {
            alert('Please select a course to add.');
            return;
        }

        // Check if the course is already in the enrolled list
        const existingCourses = document.querySelectorAll('#studentCourses tr td:first-child');
        for (const courseCell of existingCourses) {
            if (courseCell.textContent === selectedCourseId) {
                alert('This course is already enrolled.');
                return;
            }
        }

        // Add the selected course to the enrolled courses table
        const enrolledCoursesTable = document.getElementById('studentCourses');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${selectedCourseId}</td>
            <td>${selectedCourseText}</td>
            <td>
                <button type="button" class="remove-course-btn" onclick="removeCourseFromDetail(this)">Remove</button>
            </td>
        `;
        enrolledCoursesTable.appendChild(newRow);

        // Reset the dropdown after adding
        courseDropdown.value = '';
    }

// Function to remove a course from the detail modal
function removeCourseFromDetail(button) {
    const row = button.closest('tr');
    row.remove();
}


function addCourse() {
    const courseDropdown = $('#courseDropdown');
    const selectedCourseId = courseDropdown.val();
    const selectedCourseText = courseDropdown.find('option:selected').text();

    if (!selectedCourseId) return;

    // Check if the course is already added
    if (selectedCourses.some(course => course.id === selectedCourseId)) {
        alert('Course already selected');
        return;
    }

    // Add the course to the selected list
    selectedCourses.push({ id: selectedCourseId, name: selectedCourseText });

    // Update the displayed course list
    updateSelectedCoursesDisplay();
}

function removeCourse(courseId) {
    // Remove the course from the selected list
    selectedCourses = selectedCourses.filter(course => course.id !== courseId);

    // Update the displayed course list
    updateSelectedCoursesDisplay();
}

function updateSelectedCoursesDisplay() {
    const selectedCoursesList = document.getElementById('selectedCourses');
    selectedCoursesList.innerHTML = '';

    selectedCourses.forEach(course => {
        const listItem = document.createElement('li');
        listItem.textContent = course.name;

        const removeButton = document.createElement('button');
        removeButton.textContent = 'X';
        removeButton.onclick = () => removeCourse(course.id);

        listItem.appendChild(removeButton);
        selectedCoursesList.appendChild(listItem);
    });
}


// Include selected courses in the form submission
document.getElementById('addStudentForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission
    const formData = new FormData(this);

    // Add selected courses to the form data
    selectedCourses.forEach(course => formData.append('courses[]', course.id));

    fetch('add_student.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student added successfully!');
                location.reload(); // Reload the page to update the student list
            } else {
                alert('Failed to add student: ' + data.message);
            }
        })
        .catch(error => console.error('Error adding student:', error));
});

$(document).ready(function () {
        $('#courseDropdown').select2({
            placeholder: "Search Course by Code or Name",
            allowClear: true,
            width: 'resolve' // Adjust the width to fit the parent container
        });
    });

    // LEFT SIDE NAVBAR
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };

    document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("addStudentModal");
    const modalContent = modal.querySelector(".modal-content");
    const addStudentForm = document.getElementById("addStudentForm");
    const closeModal = document.querySelector(".close-modal");
    let hasUnsavedChanges = false;

    // Track changes in the form fields
    addStudentForm.addEventListener("input", () => {
        hasUnsavedChanges = true;
    });

    // Function to confirm unsaved changes
    function confirmUnsavedChanges() {
        if (hasUnsavedChanges) {
            return confirm("You have unsaved changes. Do you want to discard them?");
        }
        return true; // Safe to close as no changes
    }

    // Function to close modal
    function closeAddStudentModal() {
        if (hasUnsavedChanges) {
            const discardChanges = confirmUnsavedChanges();
            if (!discardChanges) {
                // User clicked "Cancel" in the confirmation dialog, do not close modal
                return;
            }
        }
        // Reset the form and close the modal
        modal.style.display = "none";
        addStudentForm.reset();
        hasUnsavedChanges = false; // Reset the unsaved changes flag
    }

    // Function to open modal
    function openAddStudentModal() {
        modal.style.display = "block";
        hasUnsavedChanges = false; // Reset the flag when opening modal
    }

    // Prevent modal closure on clicks inside the modal content
    modalContent.addEventListener("click", function (event) {
        event.stopPropagation(); // Prevent click from propagating to modal
    });

    // Close modal when clicking outside of the modal content
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            closeAddStudentModal();
        }
    });

    // Close modal on close button click
    closeModal.addEventListener("click", function () {
        closeAddStudentModal();
    });

    // Expose open modal function globally for the button
    window.openAddStudentModal = openAddStudentModal;
});
function closeEditCoursesModal() {
    const editCoursesModal = document.getElementById("editCoursesModal");
    editCoursesModal.style.display = "none";
}

// STUDENT DETAIL
document.addEventListener("DOMContentLoaded", function () {
    const studentRows = document.querySelectorAll(".student-row");
    const studentList = document.querySelector(".studentlist");
    const studentDetail = document.getElementById("studentDetail");

    studentRows.forEach(row => {
        row.addEventListener("click", function () {
            const studentId = this.dataset.studentId;
            const isActive = this.classList.contains("active");

            // Reset active state for all rows
            studentRows.forEach(r => r.classList.remove("active"));
            studentList.classList.remove("active");
            studentDetail.classList.remove("active");

            if (!isActive) {
                this.classList.add("active");
                studentList.classList.add("active");

                // Fetch and display student details
                fetch(`fetch_student_details.php?student_id=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        const student = data.student;
                        const courses = data.courses;

                        const coursesHtml = courses.length > 0
                            ? courses.map(course => `
                                <tr class="section-row">
                                    <td class="section-cell">${course.Course_Code}</td>
                                    <td class="section-cell">${course.Course_Name}</td>
                                    <td class="section-cell">${course.Assignment_Status}</td>
                                </tr>
                              `).join('')
                            : `<tr class="no-section-row"><td class="no-section-cell" colspan="1">No enrolled courses.</td></tr>`;

                        studentDetail.innerHTML = `
                            <h2 class="course-detail-title">${student.Stud_Name}</h2>
                            <p class="course-detail-code"><strong>ID:</strong> ${student.Stud_ID}</p>
                            <p class="course-detail-code"><strong>Section:</strong> ${student.Section_Number}</p>
                            <p class="course-detail-code"><strong>Semester:</strong> ${student.Sem_Number}</p>
                            <p class="course-detail-code"><strong>Level:</strong> ${student.Level_Name}</p>
                            <p class="course-sections-title">Enrolled Courses:</p>
                                <table class="sections-table">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${coursesHtml}
                                    </tbody>
                                </table>
                            <div class="detail-button-container">
                                <button class="student-detail-button" onclick="viewStudentDetails(${studentId})">Detail</button>
                                <button class="student-detail-button" onclick="openAssignTimeslotModal(${studentId})">Assign Timeslot</button>
                                <button class="student-detail-button" onclick="openEditCoursesModal(${studentId})">Edit Courses</button>
                            </div>
                        `;
                        studentDetail.classList.add("active");
                    })
                    .catch(error => console.error('Error fetching student details:', error));
            }
        });
    });
});


let removedCourses = [];

// References to the Edit Courses Modal and its form
const editCoursesModal = document.getElementById('editCoursesModal');
const editCoursesForm = document.getElementById('editCoursesForm');
const editCoursesContent = editCoursesModal.querySelector('.modal-content');

let hasUnsavedChangesInEdit = false; // Track unsaved changes for the Edit Courses Modal

// Detect changes in the Edit Courses form
editCoursesForm.addEventListener('input', () => {
    hasUnsavedChangesInEdit = true;
});

// Function to close the Edit Courses Modal with a confirmation check
function closeEditCoursesModal() {
    if (hasUnsavedChangesInEdit) {
        const discardChanges = confirm("You have unsaved changes. Do you want to discard them?");
        if (!discardChanges) {
            return; // Don't close the modal
        }
    }
    // Reset the form and close the modal
    editCoursesModal.style.display = 'none';
    editCoursesForm.reset(); // Reset form fields
    document.getElementById('selectedCoursesList').innerHTML = ''; // Clear selected courses list
    hasUnsavedChangesInEdit = false; // Reset the unsaved changes flag
}

// Open the Edit Courses Modal
function openEditCoursesModal(studentId) {
    fetch(`fetch_student_courses.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            const selectedCoursesList = document.getElementById('selectedCoursesList');
            selectedCoursesList.innerHTML = ''; // Clear existing items

            // Populate courses
            data.courses.forEach(course => {
                const listItem = document.createElement('li');
                listItem.textContent = `${course.Course_Code} - ${course.Course_Name}`;
                listItem.dataset.courseId = course.Course_ID;

                // Add a remove button
                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.classList.add('remove-course-btn');
                removeButton.onclick = () => removeCourseFromStudent(course.Course_ID, listItem);
                listItem.appendChild(removeButton);

                selectedCoursesList.appendChild(listItem);
            });

            // Store the studentId in the modal
            editCoursesModal.dataset.studentId = studentId;
            editCoursesModal.style.display = 'block';
            hasUnsavedChangesInEdit = false; // Reset unsaved changes flag
        })
        .catch(error => console.error('Error fetching courses:', error));
}

// Close modal when clicking outside the modal content
editCoursesModal.addEventListener('click', (event) => {
    if (event.target === editCoursesModal) {
        closeEditCoursesModal();
    }
});

// Prevent modal closure on clicks inside the modal content
editCoursesContent.addEventListener('click', (event) => {
    event.stopPropagation(); // Prevent click from propagating to modal
});

// Close modal on close button click
document.querySelector('#editCoursesModal .close-modal').addEventListener('click', closeEditCoursesModal);


// Open the modal and fetch data for timeslot assignment
function openAssignTimeslotModal(studentId) {
    const modal = document.getElementById('assignTimeslotModal');
    const timeslotDetails = document.getElementById('timeslotDetails');
    modal.style.display = 'block';

    fetch(`fetch_repeated_courses.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const groupedCourses = {};

                data.courses.forEach(course => {
                    const groupKey = course.Course_ID;

                    // Initialize group for this course if it doesn't exist
                    if (!groupedCourses[groupKey]) {
                        groupedCourses[groupKey] = {
                            Course_Name: course.Course_Name,
                            Course_Code: course.Course_Code,
                            Course_Stud_ID: course.Course_Stud_ID,
                            IsAssigned: course.Assignment_ID !== null, // Check if the course is assigned
                            Assignment_ID: course.Assignment_ID, // Store the current assignment ID
                            Assignments: course.AvailableAssignments || [] // Populate available timeslots
                        };
                    }
                });

                // Generate HTML
                const coursesHtml = Object.values(groupedCourses).map(group => `
                    <div class="course-entry">
                        <h3>${group.Course_Name} (${group.Course_Code})</h3>
                        <p><strong>Course Student ID:</strong> ${group.Course_Stud_ID || 'N/A'}</p>
                        ${group.IsAssigned 
                            ? `
                                <p class="assigned-message">The course is assigned</p>
                                <button onclick="unassignTimeslot(${group.Course_Stud_ID}, ${group.Assignment_ID})">Unassign</button>
                            `
                            : group.Assignments.length > 0
                                ? `
                                    <ul class="timeslot-list">
                                        ${group.Assignments.map(assignment => {
                                            const levelShort = assignment.Level_Name.substring(0, 3).toLowerCase();
                                            const semesterSection = `${assignment.Semester}.${assignment.Section}`;
                                            const timeslot = `${levelShort}.${semesterSection} ${assignment.AvailableTimeslot}`;
                                            return `
                                                <li class="timeslot-item">
                                                    ${timeslot}
                                                    <button onclick="assignTimeslot(${group.Course_Stud_ID}, ${assignment.Assignment_ID})">Assign</button>
                                                </li>
                                            `;
                                        }).join('')}
                                    </ul>
                                  `
                                : `<p>No available timeslots for this course.</p>`
                        }
                    </div>
                `).join('');

                timeslotDetails.innerHTML = coursesHtml || '<p>No unassigned courses available.</p>';
            } else {
                timeslotDetails.innerHTML = `<p>${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching courses:', error);
            timeslotDetails.innerHTML = '<p>Error fetching courses.</p>';
        });
}

function assignTimeslot(courseStudId, assignmentId) {
    fetch('assign_timeslot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ courseStudId, assignmentId, action: 'assign' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // Show success feedback
            location.reload();   // Reload the page to reflect changes
        } else {
            alert('Error: ' + data.message); // Show error feedback (timeslot clash message)
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred. Please try again.');
    });
}

function unassignTimeslot(courseStudId, assignmentId) {
    fetch('assign_timeslot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ courseStudId, assignmentId, action: 'unassign' })
    })
    .then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Timeslot unassigned successfully.');
        location.reload();  // Reload the page
    } else {
        alert('Error: ' + data.message);  // Handle error messages
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('Timeslot unassigned successfully.');
        location.reload();  // Reload the page
});


}







// Function to filter timeslots for a specific course
function filterTimeslotsByCourse(courseCode) {
    const levelFilter = document.querySelector(`#timeslot-list-${courseCode}`).parentElement.querySelector('.level-filter').value;
    const semesterFilter = document.querySelector(`#timeslot-list-${courseCode}`).parentElement.querySelector('.semester-filter').value;
    const sectionFilter = document.querySelector(`#timeslot-list-${courseCode}`).parentElement.querySelector('.section-filter').value;

    const timeslotList = document.getElementById(`timeslot-list-${courseCode}`);
    const timeslotItems = timeslotList.querySelectorAll('.timeslot-item');

    timeslotItems.forEach(item => {
        const matchesLevel = !levelFilter || item.dataset.level === levelFilter;
        const matchesSemester = !semesterFilter || item.dataset.semester === semesterFilter;
        const matchesSection = !sectionFilter || item.dataset.section === sectionFilter;

        if (matchesLevel && matchesSemester && matchesSection) {
            item.style.display = 'list-item'; // Show the timeslot
        } else {
            item.style.display = 'none'; // Hide the timeslot
        }
    });
}




// Close the modal
function closeAssignTimeslotModal() {
    const modal = document.getElementById('assignTimeslotModal');
    modal.style.display = 'none';
}



// Submit selected timeslots
function submitTimeslot() {
    const timeslotSelects = document.querySelectorAll('[id^="timeslot-"]');
    const assignments = Array.from(timeslotSelects).map(select => ({
        Course_ID: select.id.split('-')[1],
        Timeslot_ID: select.value
    }));

    fetch('assign_timeslot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(assignments)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Timeslots assigned successfully.');
            location.reload();
        } else {
            alert('Failed to assign timeslots: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error assigning timeslots:', error);
        alert('An error occurred while assigning timeslots.');
    });
}


let addedCourses = [];
function addCourseToStudent() {
    const courseDropdown = document.getElementById("editCourseDropdown");
    const courseId = courseDropdown.value;
    const courseText = courseDropdown.options[courseDropdown.selectedIndex]?.text;

    if (!courseId) {
        alert("Please select a valid course.");
        return;
    }

    // Check if the course is already in the selected list
    const selectedCoursesList = document.getElementById("selectedCoursesList");
    const existingCourses = Array.from(selectedCoursesList.children).map(
        item => item.dataset.courseId
    );

    if (existingCourses.includes(courseId)) {
        alert("The course is already assigned.");
        return;
    }

    // Add the course to the list
    const listItem = document.createElement("li");
    listItem.textContent = courseText;
    listItem.dataset.courseId = courseId;

    const removeButton = document.createElement("button");
    removeButton.textContent = "Remove";
    removeButton.classList.add("remove-course-btn");
    removeButton.onclick = () => removeCourseFromStudent(courseId, listItem);
    listItem.appendChild(removeButton);

    selectedCoursesList.appendChild(listItem);

    // Remove the course from the removedCourses list if it exists
    removedCourses = removedCourses.filter(id => id !== courseId);
}


function removeCourseFromStudent(courseId, listItem) {
    const selectedCoursesList = document.getElementById("selectedCoursesList");

    // Remove from the DOM
    selectedCoursesList.removeChild(listItem);

    // Add to removed courses if it was not newly added
    if (!addedCourses.includes(courseId)) {
        removedCourses.push(courseId);
    }

    // Remove from addedCourses if it was newly added
    addedCourses = addedCourses.filter(id => id !== courseId);
}

function saveStudentCourses() {
    const editCoursesModal = document.getElementById("editCoursesModal");
    const studentId = editCoursesModal.dataset.studentId; // Get the stored studentId

    if (!studentId) {
        alert("Student ID is missing.");
        return;
    }

    const courseIds = Array.from(document.getElementById("selectedCoursesList").children).map(
        item => item.dataset.courseId
    );

    console.log("Sending data:", { studentId, courseIds });

    fetch("update_student_courses.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ studentId, courseIds }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Response data:", data);
            if (data.success) {
                alert("Courses updated successfully!");
                closeEditCoursesModal(); // Close the modal after success
                location.reload();
            } else {
                alert("Failed to update courses: " + data.message);
            }
        })
        .catch(error => console.error("Error updating courses:", error));
}





// EDIT STUDENT DETAIL
let studentDetails = {}; // Store student details for saving

function viewStudentDetails(studentId) {
    fetch(`fetch_student_details.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            studentDetails = data.student;

            // Sort courses: assigned courses come first
            const sortedCourses = data.courses.sort((a, b) => {
                if (a.Assignment_Status === "Assigned" && b.Assignment_Status !== "Assigned") {
                    return -1;
                }
                if (a.Assignment_Status !== "Assigned" && b.Assignment_Status === "Assigned") {
                    return 1;
                }
                return 0;
            });

            // Populate course details
            const coursesHtml = sortedCourses.length > 0
                ? sortedCourses.map(course => `
                    <div class="course-card-vertical">
                        <h4>${course.Course_Code} - ${course.Course_Name}</h4>
                        <p><strong>Status:</strong> ${course.Assignment_Status}</p>
                        ${
                            course.Assignment_Status === "Assigned" 
                            ? `
                                <p><strong>Level:</strong> ${course.Level_Name}</p>
                                <p><strong>Semester:</strong> ${course.Sem_Number}</p>
                                <p><strong>Section:</strong> ${course.Section_Number}</p>
                                <p><strong>Day:</strong> ${course.Day || "N/A"}</p>
                                <p><strong>Timeslot:</strong> ${course.Time_Slot || "N/A"}</p>
                            ` 
                            : `<p style="color: red;">This course has not been assigned yet.</p>`
                        }
                    </div>
                `).join("")
                : "<p>No enrolled courses found.</p>";

            // Populate student details
            const detailHtml = `
                <div class="student-info">
                    <p><strong>Name:</strong> ${studentDetails.Stud_Name}</p>
                    <p><strong>ID:</strong> ${studentDetails.Stud_ID}</p>
                    <p><strong>Level:</strong> ${studentDetails.Level_Name}</p>
                    <p><strong>Semester:</strong> ${studentDetails.Sem_Number}</p>
                    <p><strong>Section:</strong> ${studentDetails.Section_Number}</p>
                </div>
                <h3>Enrolled Courses:</h3>
                <div class="courses-container-vertical">
                    ${coursesHtml}
                </div>
            `;

            // Insert details into the modal
            document.getElementById("detailModalContent").innerHTML = detailHtml;

            

            // Display the modal
            document.getElementById("detailModal").style.display = "block";
        })
        .catch(error => console.error("Error fetching student details:", error));
}

function loadSemestersForEdit(levelId, selectedSemesterId = null) {
    const semesterDropdown = document.getElementById("editSemester");
    if (!levelId) {
        semesterDropdown.innerHTML = "<option value=''>Select Semester</option>";
        semesterDropdown.disabled = true;
        return;
    }

    fetch(`studentlist_fetch_semesters.php?level_id=${levelId}`)
        .then(response => response.json())
        .then(data => {
            semesterDropdown.innerHTML = "<option value=''>Select Semester</option>";
            data.forEach(semester => {
                semesterDropdown.innerHTML += `
                    <option value="${semester.Sem_ID}" ${semester.Sem_ID == selectedSemesterId ? "selected" : ""}>
                        ${semester.Sem_Number}
                    </option>`;
            });
            semesterDropdown.disabled = false;
        })
        .catch(error => console.error("Error loading semesters:", error));
}

function loadSectionsForEdit(semesterId, selectedSectionId = null) {
    const sectionDropdown = document.getElementById("editSection");
    if (!semesterId) {
        sectionDropdown.innerHTML = "<option value=''>Select Section</option>";
        sectionDropdown.disabled = true;
        return;
    }

    fetch(`studentlist_fetch_sections.php?semester_id=${semesterId}`)
        .then(response => response.json())
        .then(data => {
            sectionDropdown.innerHTML = "<option value=''>Select Section</option>";
            data.forEach(section => {
                sectionDropdown.innerHTML += `
                    <option value="${section.Section_ID}" ${section.Section_ID == selectedSectionId ? "selected" : ""}>
                        ${section.Section_Number}
                    </option>`;
            });
            sectionDropdown.disabled = false;
        })
        .catch(error => console.error("Error loading sections:", error));
}
const levels = <?php echo json_encode($levels); ?>;


function saveStudentDetails() {
    const updatedDetails = {
        Stud_ID: studentDetails.Stud_ID,
        Stud_Name: document.getElementById("editName").value,
        Level_ID: document.getElementById("editLevel").value,
        Sem_ID: document.getElementById("editSemester").value,
        Section_ID: document.getElementById("editSection").value,
    };

    fetch("update_student_details.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(updatedDetails),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
    alert("Details updated successfully!");
    document.getElementById("detailModal").style.display = "none"; // Close modal
    setTimeout(() => {
        location.reload(); // Refresh after modal close animation
    }, 300); // Adjust timeout if necessary
}

        })
        .catch(error => {
            console.error("Error saving details:", error);
            alert("An error occurred while saving the details.");
        });
}

function updateStudentList() {
    fetch("studentlist_fetch.php") // Replace with the appropriate backend endpoint
        .then(response => response.text())
        .then(html => {
            document.getElementById("studentTableBody").innerHTML = html;
        })
        .catch(error => console.error("Error updating student list:", error));
}

function closeDetailModal() {
    document.getElementById("detailModal").style.display = "none";
}




window.onclick = function (event) {
    const detailModal = document.getElementById("detailModal");
    if (event.target === detailModal) {
        detailModal.style.display = "none";
    }
};
let hasUnsavedChanges = false;

// Detect changes in the form
document.getElementById('editStudentForm').addEventListener('input', () => {
    hasUnsavedChanges = true;
});

// Open the Edit Student Modal
function openEditStudentModal(studentId, repeatedCoursesCount) {
    fetch(`fetch_student_details.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            // Fill the form with current student data
            document.getElementById('editStudentName').value = data.student.Stud_Name;
            document.getElementById('editLevel').value = data.student.Level_ID;

            // Load semesters based on level
            loadSemestersForEdit(data.student.Level_ID, data.student.Sem_ID);

            // Load sections based on semester
            loadSectionsForEdit(data.student.Sem_ID, data.student.Section_ID);

            // If there are repeated courses, disable the level, semester, and section fields
            if (repeatedCoursesCount > 0) {
                document.getElementById('editLevel').disabled = true;
                document.getElementById('editSemester').disabled = true;
                document.getElementById('editSectionNumber').disabled = true;
            } else {
                document.getElementById('editLevel').disabled = false;
                document.getElementById('editSemester').disabled = false;
                document.getElementById('editSectionNumber').disabled = false;
            }

            // Store studentId in modal for later use
            document.getElementById('editStudentModal').dataset.studentId = studentId;

            // Show the modal
            document.getElementById('editStudentModal').style.display = 'block';
        })
        .catch(error => console.error("Error fetching student details:", error));
}


// Close the Edit Student Modal
function closeEditStudentModal() {
    if (hasUnsavedChanges) {
        // Confirm discard if there are unsaved changes
        const discardChanges = confirm("You have unsaved changes. Do you want to discard them?");
        if (!discardChanges) {
            return; // Don't close the modal
        }
    }

     // Reset the fields to be enabled if there are no repeated courses
     document.getElementById('editLevel').disabled = false;
    document.getElementById('editSemester').disabled = false;
    document.getElementById('editSectionNumber').disabled = false;

    document.getElementById('editStudentModal').style.display = 'none';
    hasUnsavedChanges = false; // Reset the unsaved changes flag
}

// Add event listener for clicks outside the modal
window.addEventListener('click', (event) => {
    const modal = document.getElementById('editStudentModal');
    if (event.target === modal) {
        closeEditStudentModal();
    }
});

// Save Edited Student Details
document.getElementById('editStudentForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const studentId = document.getElementById('editStudentModal').dataset.studentId;
    const studentName = document.getElementById('editStudentName').value;
    const levelId = document.getElementById('editLevel').value;
    const semesterId = document.getElementById('editSemester').value;
    const sectionId = document.getElementById('editSectionNumber').value;

    const updatedDetails = {
        Stud_ID: studentId,
        Stud_Name: studentName,
        Level_ID: levelId,
        Sem_ID: semesterId,
        Section_ID: sectionId
    };

    fetch('update_student_details.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedDetails)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset the unsaved changes flag after successful save
                hasUnsavedChanges = false; 

                alert("Student details updated successfully!");
                closeEditStudentModal(); // Close the modal
                location.reload(); // Reload to reflect changes
            } else {
                alert("Failed to update student details: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error saving student details:", error);
            alert("An error occurred while saving the details.");
        });
});




// Load Semesters for Edit
function loadSemestersForEdit(levelId, selectedSemesterId = null) {
    const semesterDropdown = document.getElementById('editSemester');
    if (!levelId) {
        semesterDropdown.innerHTML = "<option value=''>Select Semester</option>";
        semesterDropdown.disabled = true;
        return;
    }

    fetch(`studentlist_fetch_semesters.php?level_id=${levelId}`)
        .then(response => response.json())
        .then(data => {
            semesterDropdown.innerHTML = "<option value=''>Select Semester</option>";
            data.forEach(semester => {
                semesterDropdown.innerHTML += `
                    <option value="${semester.Sem_ID}" ${semester.Sem_ID == selectedSemesterId ? "selected" : ""}>
                        ${semester.Sem_Number}
                    </option>`;
            });
            semesterDropdown.disabled = false;
        })
        .catch(error => console.error("Error loading semesters:", error));
}

// Load Sections for Edit
function loadSectionsForEdit(semesterId, selectedSectionId = null) {
    const sectionDropdown = document.getElementById('editSectionNumber');
    if (!semesterId) {
        sectionDropdown.innerHTML = "<option value=''>Select Section</option>";
        sectionDropdown.disabled = true;
        return;
    }

    fetch(`studentlist_fetch_sections.php?semester_id=${semesterId}`)
        .then(response => response.json())
        .then(data => {
            sectionDropdown.innerHTML = "<option value=''>Select Section</option>";
            data.forEach(section => {
                sectionDropdown.innerHTML += `
                    <option value="${section.Section_ID}" ${section.Section_ID == selectedSectionId ? "selected" : ""}>
                        ${section.Section_Number}
                    </option>`;
            });
            sectionDropdown.disabled = false;
        })
        .catch(error => console.error("Error loading sections:", error));
}
document.addEventListener("DOMContentLoaded", function () {
    // Add an event listener for the delete button click event
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            const studentRow = event.target.closest('tr'); // Find the parent row
            const studentId = studentRow.dataset.studentId; // Get the student ID from the row data attribute

            // Confirm if the user really wants to delete the student
            const confirmDelete = confirm("Are you sure you want to delete this student?");
            if (confirmDelete) {
                deleteStudent(studentId, studentRow); // Call deleteStudent function
            }
        });
    });
});

// Function to delete a student from the backend and frontend
function deleteStudent(studentId, studentRow) {
    // Send a request to the backend to delete the student from the database
    fetch(`delete_student.php?student_id=${studentId}`, {
        method: 'GET', // Using GET or POST based on your preference
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Student deleted successfully!");
            studentRow.remove(); // Remove the row from the table if deletion was successful
        } else {
            alert(data.message); // Show the specific message (e.g., "The student has repeated course assignments")
        }
    })
    .catch(error => {
        console.error('Error deleting student:', error);
        alert('An error occurred while deleting the student.');
    });
}



</script>
</html>
