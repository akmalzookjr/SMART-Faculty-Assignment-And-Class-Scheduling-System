<?php
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

.sections-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.sections-table th, .sections-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.sections-table th {
    background-color: #4678b8;
    color: #fff;
}

.sections-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* General container for tables */
.sections-table-container {
    max-height: 200px; /* Ensure a similar height */
    overflow-y: auto; /* Scroll for overflow */
    margin-top: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

/* Style for the table headers */
.sections-table th,
.sections-table td {
    text-align: left;
    padding: 10px;
    border: 1px solid #ddd;
}

/* Header style consistent with other sections */
.section-header {
    background-color: #4CAF50; /* Use green for a consistent look */
    color: white;
    font-weight: bold;
}

/* Alternate row styling */
.sections-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Row hover effect */
.sections-table tr:hover {
    background-color: #eaf4f9;
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

</style>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="student-detail" id="studentDetail">
            <!-- Student details will be dynamically inserted here -->
        </div>
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

</body>
<script>
    // SEARCH STUDENT
    function searchStudents() {
        const searchQuery = document.getElementById('searchBar').value.toLowerCase();
        const rows = document.querySelectorAll('#studentTableBody tr');

        rows.forEach(row => {
            const studentName = row.cells[0].innerText.toLowerCase();
            if (studentName.includes(searchQuery)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

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
                                <tr>
                                    <td>${course.Course_Code}</td>
                                    <td>${course.Course_Name}</td>
                                </tr>
                              `).join('')
                            : `<tr><td colspan="2">No enrolled courses.</td></tr>`;

                        studentDetail.innerHTML = `
                            <h2 class="course-detail-title">${student.Stud_Name}</h2>
                            <p class="course-detail-info">
                                <strong>ID:</strong> ${student.Stud_ID} <br>
                                <strong>Section:</strong> ${student.Section_Number} <br>
                                <strong>Semester:</strong> ${student.Sem_Number} <br>
                                <strong>Level:</strong> ${student.Level_Name}
                            </p>
                            <h3>Enrolled Courses</h3>
                            <div class="sections-table-container">
                                <table class="sections-table">
                                    <thead>
                                        <tr>
                                            <th class="section-header">Course Code</th>
                                            <th class="section-header">Course Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${coursesHtml}
                                    </tbody>
                                </table>
                            </div>
                            <div class="detail-button-container">
                                <button class="student-detail-button" onclick="viewStudentDetails(${studentId})">Detail</button>
                            </div>
                        `;

                        studentDetail.classList.add("active");
                    })
                    .catch(error => console.error('Error fetching student details:', error));
            }
        });
    });
});

function closeStudentDetailModal() {
    const modal = document.getElementById('studentDetailModal');
    modal.style.display = 'none';

    // Reset edit mode
    isEditMode = false;
    toggleEditMode();
}


// Close modal if clicked outside the modal content
window.onclick = function (event) {
    const modal = document.getElementById('studentDetailModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};
</script>
</html>
