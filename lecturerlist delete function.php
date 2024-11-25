<?php
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

// Create the connection using mysqli_connect
include("connect.php");

if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
    exit();
}

// Query for fetching lecturers with contact hours based on timeslots assigned
$sql = "SELECT 
    Lecturer.Lect_ID, 
    Lecturer.Lect_Name, 
    Lecturer.Lect_Email,
    Lecturer.Lect_CH,
    COALESCE(COUNT(DISTINCT Assign_Schedule.Sche_ID), 0) AS Assigned_CH
FROM 
    Lecturer
LEFT JOIN 
    Lecturer_Assignment ON Lecturer.Lect_ID = Lecturer_Assignment.Lect_ID
LEFT JOIN 
    Assign_Schedule ON Assign_Schedule.Assign_Sche_ID = Lecturer_Assignment.Assign_Sche_ID
GROUP BY 
    Lecturer.Lect_ID, Lecturer.Lect_Name, Lecturer.Lect_Email, Lecturer.Lect_CH;
";

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

$sql_timeslots = "
SELECT 
    Course.Course_Name,
    Course.Course_Code,
    Schedule.Day,
    MIN(Schedule.Time_Slot) as Start_Time,
    MAX(Schedule.Time_Slot) as End_Time,
    Assign_Schedule.Assign_Sche_ID,
    Section.Section_Number,
    Semester.Sem_Number,
    Level.Level_Name,
    COUNT(*) as Duration,
    (CASE WHEN Lecturer_Assignment.Lect_ID IS NULL THEN 'Available' ELSE 'Assigned' END) as Status
FROM 
    Schedule
INNER JOIN 
    Assign_Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
INNER JOIN 
    Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
INNER JOIN 
    Course ON Course.Course_ID = Course_Section.Course_ID
LEFT JOIN 
    Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
LEFT JOIN 
    Section ON Assign_Schedule.Section_ID = Section.Section_ID
LEFT JOIN 
    Semester ON Section.Sem_ID = Semester.Sem_ID
LEFT JOIN 
    Level ON Semester.Level_ID = Level.Level_ID
WHERE 
    Lecturer_Assignment.Lect_ID IS NULL
GROUP BY 
    Course.Course_ID, Schedule.Day
ORDER BY 
    Schedule.Day, MIN(Schedule.Time_Slot)
";



$result_timeslots = mysqli_query($conn, $sql_timeslots);
$timeslots = mysqli_fetch_all($result_timeslots, MYSQLI_ASSOC);



// Fetch lecturer
$result = mysqli_query($conn, $sql);

if ($result) {
    $lecturers = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    echo "Error: " . mysqli_error($conn);
}

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
<style>
/* Style for the flashcard container */
/* Style for the flashcard container */
/* Style for the flashcard container */
.flashcard {
    border: 1px solid #ccc;
    padding: 20px;
    margin: 15px;
    width: 260px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: inline-block;
    vertical-align: top;
    transition: all 0.3s ease-in-out;
    border-radius: 8px;
}

.flashcard:hover {
    transform: translateY(-8px); /* Lift the flashcard on hover */
}

/* Course Title */
.flashcard h3 {
    font-size: 18px;
    text-align: center;
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
}

/* Days Styling */
.day-list {
    margin-bottom: 20px;
    padding: 10px;
    background-color: #e6f2f7; /* Light blue background for each day */
    border-radius: 8px;
}

.day-header {
    font-size: 16px;
    font-weight: bold;
    color: #1f77b4; /* Blue color */
    margin-bottom: 5px;
    padding-left: 10px;
    padding-right: 10px;
    background-color: #cce7f5;
    border-radius: 5px;
    text-transform: uppercase;
}

/* Styling for time slots */
.time-slot-list {
    list-style-type: none;
    padding-left: 20px;
    margin: 0;
}

.time-slot {
    background-color: #f2f7fa;
    padding: 8px 12px;
    margin-bottom: 8px;
    border-radius: 6px;
    font-size: 14px;
    color: #34495e;
    transition: background-color 0.3s ease;
}

.time-slot:hover {
    background-color: #b0d6f1; /* Lighter blue on hover */
}

/* Styling for the assign button */
.flashcard button {
    display: block;
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.flashcard button:hover {
    background-color: #0056b3;
}

/* Additional styles for better spacing and alignment */
.flashcard p {
    font-size: 14px;
    color: #555;
    margin-bottom: 10px;
}

/* Modal content */
.list-modal-content {
    position: relative;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 80%; /* Adjust based on your design */
    max-width: 600px; /* Optional */
    margin: 10% auto;
}

/* Close button positioned at the top-right of the modal */
.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 24px;
    color: #000;
    cursor: pointer;
    background: transparent;
    border: none;
}

.close:hover {
    color: #ff0000; /* Change color on hover */
}

.list-modal-content {
    min-width: 80%;
}

.lecturercourselistr {
    background-color: #b0d6f1;
    width: 100%;
    min-height: 50%;
    border-radius: 15px;
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
            <a href="home.php"><img src="akmal.jpg" alt="me" class="user-img"></a>
            <div>
                <p class="bold"><?php echo htmlspecialchars($userName); ?></p>
                <p><?php echo htmlspecialchars($userType); ?></p>
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
            <ul class="nav_links">
                <button class="icon bg-icon"><i class="fas fa-moon"></i></button>
                <button class="icon bg-icon"><i class="fas fa-bell"></i></button>
                <button class="icon bg-icon"><i class="fas fa-calendar"></i></button>
            </ul>
        </header>
        <div class="middle">
            <div class="header-container">
                <div class="title-and-search">
                    <h1>Lecturer List</h1>
                    <div class="search-container">
                        <input type="text" id="searchBar" placeholder="Search by lecturer name..." class="search-bar">
                    </div>
                </div>
                <button class="add-btn"><i class="fas fa-plus"></i></button>
            </div>
            <div id="addLecturerModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>Add New Lecturer</h2>
                    <form id="addLecturerForm">
                        <div class="form-group">
                            <label for="lecturerName">Lecturer Name:</label>
                            <input type="text" id="lecturerName" name="lecturerName" required>
                        </div>
                        <div class="form-group">
                            <label for="lecturerEmail">Email:</label>
                            <input type="email" id="lecturerEmail" name="lecturerEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="lecturerPassword">Password:</label>
                            <input type="password" id="lecturerPassword" name="lecturerPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="creditHour">Credit Hour:</label>
                            <input type="number" id="creditHour" name="creditHour" min="1" required>
                            <small id="creditHourError" style="color: red; display: none;">Credit hour must be at least 1.</small>
                        </div>
                        <button type="submit" class="submit-btn">Add Lecturer</button>
                    </form>
                </div>
            </div>
            <div class="lecturerlist">
                <table class="lecturerlist">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Credit Hour</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="lecturerTableBody">
                        <?php foreach ($lecturers as $lecturer): ?>
                            <?php
                                // Determine the status based on credit hours
                                $status = "Free";
                                $statusClass = "status-free";
                                if ($lecturer['Assigned_CH'] >= $lecturer['Lect_CH']) {
                                    $status = "Full";
                                    $statusClass = "status-full";
                                }
                                if ($lecturer['Assigned_CH'] > $lecturer['Lect_CH']) {
                                    $status = "Overload";
                                    $statusClass = "status-overload";
                                }
                            ?>
                            <tr class="lecturer-row" 
                                data-lect-id="<?php echo htmlspecialchars($lecturer['Lect_ID']); ?>" 
                                data-lect-name="<?php echo htmlspecialchars($lecturer['Lect_Name']); ?>" 
                                data-lect-email="<?php echo htmlspecialchars($lecturer['Lect_Email']); ?>" 
                                data-total-ch="<?php echo htmlspecialchars($lecturer['Assigned_CH']); ?>"
                                data-max-ch="<?php echo htmlspecialchars($lecturer['Lect_CH']); ?>">
                            <td><?php echo htmlspecialchars($lecturer['Lect_Name']); ?></td>
                            <td class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
                            <td><?php echo htmlspecialchars($lecturer['Assigned_CH']) . '/' . htmlspecialchars($lecturer['Lect_CH']); ?></td>
                            <td>
                                <button class="edit-btn"><i class="fas fa-edit"></i></button>
                                <button class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                                <!-- Add schedule button -->
                                <button class="schedule-btn">
                                <a href="schedulelecturer.php?lecturer_id=<?php echo htmlspecialchars($lecturer['Lect_ID']); ?>">
                                    <i class="fas fa-calendar-check"></i>
                                </a>
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
            
            <div class="lecturer-detail" id="lecturerDetail">
                <!-- Details will be dynamically inserted here -->
                <h1>akmal</h1>
            </div>

        </div>
    </div>
</div>
<div id="lecturerModal" class="list-modal">
    <div class="list-modal-content">
        <span class="close">&times;</span>
        <div id="modalBody">Details will be displayed here...</div>
    </div>
</div>
<div id="editLecturerModal" class="modal">
    <div class="modal-content">
        <span class="close-modal close-edit-modal">&times;</span>
        <h2>Edit Lecturer</h2>
        <form id="editLecturerForm">
            <input type="hidden" id="editLecturerId" name="lecturerId">
            <div class="form-group">
                <label for="editLecturerName">Lecturer Name:</label>
                <input type="text" id="editLecturerName" name="lecturerName" required>
            </div>
            <div class="form-group">
                <label for="editLecturerEmail">Email:</label>
                <input type="email" id="editLecturerEmail" name="lecturerEmail" required>
            </div>
            <div class="form-group">
                <label for="editLecturerPassword">Password:</label>
                <input type="password" id="editLecturerPassword" name="lecturerPassword" placeholder="Leave blank to keep current password">
            </div>
            <div class="form-group">
                <label for="editLecturerCH">Credit Hour:</label>
                <input type="number" id="editLecturerCH" name="lecturerCH" min="1" required>
                <small id="editLecturerCHError" style="color: red; display: none;">Credit hour must be at least 1.</small>
            </div>
            <button type="submit" class="submit-btn">Save Changes</button>
        </form>
    </div>
</div>


</body>

<script>
    let btn = document.querySelector('#btn')
    let sidebar = document.querySelector('.sidebar')

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };

    document.addEventListener("DOMContentLoaded", function () {
    const lecturerRows = Array.from(document.querySelectorAll('.lecturer-row'));
    const searchBar = document.getElementById('searchBar');
    const itemsPerPage = 12;  // Number of items per page for pagination
    let currentPage = 1;
    let filteredLecturers = lecturerRows;  // Initialize with all lecturers

    // Function to render the current page of lecturers
    function renderPage(page) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Hide all lecturers, then display only the ones for the current page
        filteredLecturers.forEach((lecturer, index) => {
            lecturer.style.display = (index >= start && index < end) ? '' : 'none';
        });

        renderPaginationControls();
    }

    // Function to render pagination controls based on the filtered lecturers
    function renderPaginationControls() {
        const totalPages = Math.ceil(filteredLecturers.length / itemsPerPage);
        const paginationControls = document.getElementById('paginationControls');
        paginationControls.innerHTML = '';  // Clear the pagination controls

        // Previous button
        const prevButton = document.createElement('button');
        prevButton.innerText = 'Previous';
        prevButton.disabled = (currentPage === 1);
        prevButton.classList.toggle('disabled', currentPage === 1);
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
        nextButton.classList.toggle('disabled', currentPage === totalPages);
        nextButton.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage(currentPage);
            }
        };
        paginationControls.appendChild(nextButton);
    }

    // Filter lecturers based on the search input
    function filterLecturers() {
        const searchText = searchBar.value.toLowerCase();

        filteredLecturers = lecturerRows.filter(row => {
            const lecturerName = row.dataset.lectName.toLowerCase();
            return lecturerName.includes(searchText);  // Filter based on lecturer name
        });

        currentPage = 1;  // Reset to the first page after filtering
        renderPage(currentPage);
    }

    // Add event listener for search input
    searchBar.addEventListener('input', filterLecturers);

    // Initial render of all lecturers
    renderPage(currentPage);
});


    // ADD LECTURER MODAL
    document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.querySelector(".add-btn");
    const modal = document.getElementById("addLecturerModal");
    const closeModal = document.querySelector(".close-modal");
    const form = document.getElementById("addLecturerForm");
    let isDirty = false; // Tracks if there are unsaved changes in the form

    // Open the modal
    addButton.onclick = function () {
        modal.style.display = "block";
        isDirty = false; // Reset dirty state when opening the modal
    };

    // Track changes in the form to set the dirty state
    form.addEventListener("input", function () {
        isDirty = true;
    });

    // Function to confirm closing the modal if there are unsaved changes
    function confirmUnsavedChanges() {
        if (isDirty) {
            return confirm("You have unsaved changes. Do you want to discard them?");
        }
        return true;
    }

    // Close the modal on clicking the close button
    closeModal.onclick = function () {
        if (confirmUnsavedChanges()) {
            modal.style.display = "none";
            form.reset(); // Clear the form when closing
        }
    };

    // Close the modal on clicking outside of it
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            if (confirmUnsavedChanges()) {
                modal.style.display = "none";
                form.reset(); // Clear the form when closing
            }
        }
    });

    // Reset the form and close the modal after successful submission
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch("add_lecturer.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(data.message);
                    form.reset(); // Clear the form
                    modal.style.display = "none"; // Close the modal
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert("Failed to add lecturer: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred while adding the lecturer.");
            });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const editLecturerCHInput = document.getElementById('editLecturerCH');
    const editLecturerCHError = document.getElementById('editLecturerCHError');
    const editLecturerForm = document.getElementById('editLecturerForm');

    // Real-time validation for credit hours
    editLecturerCHInput.addEventListener('input', function () {
        const value = parseInt(this.value, 10);
        if (value < 1 || isNaN(value)) {
            editLecturerCHError.style.display = 'block'; // Show error message
            this.value = ''; // Clear invalid input
        } else {
            editLecturerCHError.style.display = 'none'; // Hide error message
        }
    });

    // Validation on form submission
    editLecturerForm.addEventListener('submit', function (event) {
        const value = parseInt(editLecturerCHInput.value, 10);
        if (value < 1 || isNaN(value)) {
            event.preventDefault(); // Prevent form submission
            editLecturerCHError.style.display = 'block'; // Show error message
            alert('Please ensure the credit hour is at least 1.'); // Display an alert
        } else {
            editLecturerCHError.style.display = 'none'; // Hide error message
        }
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const creditHourInput = document.getElementById('creditHour');
    const creditHourError = document.getElementById('creditHourError');
    const addLecturerForm = document.getElementById('addLecturerForm');

    // Check the value on input
    creditHourInput.addEventListener('input', function () {
        const value = parseInt(this.value, 10);
        if (value < 1 || isNaN(value)) {
            creditHourError.style.display = 'block';
            this.value = ''; // Clear invalid value
        } else {
            creditHourError.style.display = 'none';
        }
    });

    // Additional validation before submitting the form
    addLecturerForm.addEventListener('submit', function (event) {
        const value = parseInt(creditHourInput.value, 10);
        if (value < 1 || isNaN(value)) {
            event.preventDefault();
            creditHourError.style.display = 'block';
            alert('Please ensure the credit hour is at least 1.');
        } else {
            creditHourError.style.display = 'none';
        }
    });
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

    // LECTURER DETAILS
    document.addEventListener("DOMContentLoaded", function () {
    // Prevent edit button from triggering row click
    const editButtons = document.querySelectorAll(".edit-btn");
    editButtons.forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent the row click event
            const lecturerRow = this.closest(".lecturer-row");
            const lecturerId = lecturerRow.dataset.lectId;
            const lecturerName = lecturerRow.dataset.lectName;
            const lecturerEmail = lecturerRow.dataset.lectEmail;
            const lecturerCH = lecturerRow.dataset.maxCh;

            // Populate the modal form with the lecturer's data
            document.getElementById("editLecturerId").value = lecturerId;
            document.getElementById("editLecturerName").value = lecturerName;
            document.getElementById("editLecturerEmail").value = lecturerEmail;
            document.getElementById("editLecturerPassword").value = ""; // Clear password field
            document.getElementById("editLecturerCH").value = lecturerCH;

            const editModal = document.getElementById("editLecturerModal");
            editModal.style.display = "block";
        });
    });

    // Prevent delete button from triggering row click
    const deleteButtons = document.querySelectorAll(".delete-btn");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent the row click event
            const lecturerRow = this.closest(".lecturer-row");
            const lecturerId = lecturerRow.dataset.lectId;
            const lecturerName = lecturerRow.dataset.lectName;

            const confirmation = confirm(`Are you sure you want to delete the lecturer "${lecturerName}"?`);
            if (confirmation) {
                fetch("delete_lecturer.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `lecturer_id=${lecturerId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Lecturer "${lecturerName}" has been deleted.`);
                        location.reload(); // Refresh the page
                    } else {
                        alert(`Failed to delete lecturer: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error("Error deleting lecturer:", error);
                    alert("An error occurred while deleting the lecturer.");
                });
            }
        });
    });

    // Lecturer row click for details
    const lecturerRows = document.querySelectorAll(".lecturer-row");
    lecturerRows.forEach(row => {
        row.addEventListener("click", function () {
            const lecturerId = this.dataset.lectId;
            const lecturerList = document.querySelector(".lecturerlist");
            const lecturerDetail = document.getElementById("lecturerDetail");
            const currentActive = document.querySelector(".lecturer-row.active");
            window.currentLecturerId = lecturerId;

            if (currentActive === this && lecturerDetail.classList.contains("active")) {
                this.classList.remove("active");
                lecturerDetail.classList.remove("active");
                lecturerList.classList.remove("active");
                lecturerDetail.innerHTML = "";
            } else {
                if (currentActive) {
                    currentActive.classList.remove("active");
                }
                this.classList.add("active");

                fetch(`table_get_courses_lect.php?lecturer_id=${lecturerId}`)
                    .then(response => response.json())
                    .then(courses => {
                        const coursesHtml = courses.length > 0
                            ? courses.map(course => `
                                <tr class="section-row">
                                    <td class="section-cell">${course.Course_Name}</td>
                                    <td class="section-cell">${course.Course_CH}</td>
                                    <td class="section-cell">${course.Assign_Sche_ID ? "Assigned" : "Unassigned"}</td>
                                </tr>
                            `).join("")
                            : `<tr><td colspan="3">No courses assigned.</td></tr>`;

                        const assignedCoursesCount = courses.filter(course => course.Assign_Sche_ID).length;

                        lecturerDetail.innerHTML = `
                        <h2 class="course-detail-title">Details for ${this.dataset.lectName}</h2>
                        <p class="course-detail-code">Lect ID: ${lecturerId}</p>
                        <p class="course-detail-ch">Credit Hour: ${this.dataset.maxCh}</p>
                        <p class="course-sections-title">Assigned Courses: ${assignedCoursesCount}</p>
                        <table class="lecturercourselist">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Credit Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>${coursesHtml}</tbody>
                        </table>
                        <button class="lect-detail-button" onclick="showDetails(${lecturerId})">Detail</button>
                    `;
                        lecturerDetail.classList.add("active");
                        lecturerList.classList.add("active");
                    })
                    .catch(error => {
                        console.error("Error fetching courses:", error);
                        lecturerDetail.innerHTML = `
                            <h1>Details for ${this.dataset.lectName}</h1>
                            <p>Lect ID: ${lecturerId}</p>
                            <p>Credit Hour: ${this.dataset.maxCh}</p>
                            <p>Assigned Courses: 0</p>
                            <table class="lecturercourselist">
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Credit Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody><tr><td colspan="3">No courses assigned.</td></tr></tbody>
                            </table>
                            <button class="detail-button" onclick="showDetails(${lecturerId})">Detail</button>
                        `;
                        lecturerDetail.classList.add("active");
                        lecturerList.classList.add("active");
                    });
            }
        });
    });
});

function showDetails(lecturerId) {
    const modal = document.getElementById('lecturerModal');
    const modalBody = document.getElementById('modalBody');
    const lecturerRow = document.querySelector(`.lecturer-row[data-lect-id="${lecturerId}"]`);
    const lecturerName = lecturerRow ? lecturerRow.dataset.lectName : 'Unknown Lecturer';
    const totalAssignedCH = lecturerRow ? lecturerRow.dataset.totalCh : '0';  // Total assigned credit hours
    const maxCH = lecturerRow ? lecturerRow.dataset.maxCh : 'N/A';  // Maximum credit hours

    // Fetching lecturer details
    fetch(`table_lecturer_detail.php?lecturer_id=${lecturerId}`)
    .then(response => response.json())
    .then(data => {
        if (!data || data.error) {
            modalBody.innerHTML = `<p>Error: ${data ? data.error : 'Unknown error'}</p>`;
        } else {
            const courses = data.Courses ? data.Courses.split(', ') : [];

            // Grouping courses by Level, Semester, and Section
            const groupedCourses = courses.reduce((acc, courseInfo) => {
                const [courseName, creditHours, assignScheId, levelName, semNumber, sectionNumber] = courseInfo.split('|');
                const key = `${levelName}-${semNumber}-${sectionNumber}`;

                if (!acc[key]) {
                    acc[key] = {
                        level: levelName,
                        semester: semNumber,
                        section: sectionNumber,
                        courses: [] // Initialize empty courses array for this group
                    };
                }

                acc[key].courses.push({
                    name: courseName,
                    creditHours: creditHours,
                    assignScheId: assignScheId,
                    levelName: levelName,
                    semNumber: semNumber,
                    sectionNumber: sectionNumber
                });

                return acc;
            }, {});

            const groupedCoursesArray = Object.values(groupedCourses);

            // Generate HTML for grouped courses (flashcards) by course name
            const coursesHTML = groupedCoursesArray.length > 0 ? groupedCoursesArray.map(group => {
                const { level, semester, section, courses } = group;

                // Group courses by course name
                const groupedByCourse = courses.reduce((acc, course) => {
                    if (!acc[course.name]) {
                        acc[course.name] = { ...course, timeslots: [] }; // Initialize for each course
                    }
                    // Push timeslots for each course
                    if (data.Timeslots) {
                        const timeslotsForCourse = data.Timeslots.filter(ts => ts.Assign_Sche_ID === course.assignScheId);
                        acc[course.name].timeslots.push(...timeslotsForCourse);
                    }
                    return acc;
                }, {});

                // Generate the flashcards for each course
                return Object.values(groupedByCourse).map(course => {
    let timeslotByDayHTML = '';
    const displayDays = {}; // Store days and their respective timeslots

    // Loop through timeslots and group by day
    course.timeslots.forEach(timeslot => {
        if (!displayDays[timeslot.Day]) {
            displayDays[timeslot.Day] = [];
        }
        displayDays[timeslot.Day].push(timeslot.Time_Slot);
    });

    // Build timeslot display for this course, grouped by day
    for (const [day, timeSlots] of Object.entries(displayDays)) {
        timeslotByDayHTML += `<div class="day-list">
                                <strong class="day-title">${day}:</strong>
                                <ul class="time-slot-list">`;
        timeSlots.forEach(slot => {
            timeslotByDayHTML += `<li class="time-slot">${slot}</li>`;
        });
        timeslotByDayHTML += '</ul></div>';
    }

    const displayDaysText = Object.keys(displayDays).slice(0, 2).join(', ');

    return `
        <div class="flashcard">
            <div class="flashcard-content">
                <h3 class="course-title">${course.name} (${course.creditHours} CH)</h3>
                <p><strong>Level:</strong> ${level}</p>
                <p><strong>Semester:</strong> ${semester}</p>
                <p><strong>Section:</strong> ${section}</p>
                <p><strong>Days:</strong> ${displayDaysText}</p>
                <p><strong>Time Slots:</strong></p>
                ${timeslotByDayHTML}
                <p><strong>Status:</strong> ${course.assignScheId ? 'Assigned' : 'Unassigned'}</p>
                <button class="trash-btn" onclick="unassignCourse([${course.assignScheId}], ${lecturerId}, '${course.name}')">Remove All Timeslots</button>
            </div>
        </div>
    `;
}).join('');

            }).join('') : `<div class="flashcard no-courses"><p>No courses assigned.</p></div>`;

            // Create timeslot HTML for unassigned courses
            let timeslotsHTML = '';
            if (courses.length === 0) {
                timeslotsHTML = `<tr><td colspan="7">No available courses for assignment.</td></tr>`;
            } else {
                const unassignedCourses = courses.filter(courseInfo => {
                    const [, , assignScheId] = courseInfo.split('|');
                    return assignScheId === '0';
                });

                timeslotsHTML = unassignedCourses.length > 0 ? unassignedCourses.map(courseInfo => {
                    const [courseName] = courseInfo.split('|');
                    const courseTimeslots = data.Timeslots.filter(slot => slot.Course === courseName);

                    return courseTimeslots.length > 0 ? courseTimeslots.map(slot => `
                        <tr class="section-row">
                            <td class="section-cell">${slot.Course}</td>
                            <td class="section-cell">${slot.Course_Section}</td>
                            <td class="section-cell">${slot.Sem_Number}</td>
                            <td class="section-cell">${slot.Section_Number}</td>
                            <td class="section-cell">${slot.Day}</td>
                            <td class="section-cell">${slot.Time_Slot}</td>
                            <td class="section-cell"><button onclick="assignToLecturer('${slot.Assign_Sche_ID}', ${lecturerId})">Assign</button></td>
                        </tr>`).join('') : `<tr><td colspan="7">No schedule available for ${courseName}</td></tr>`;
                }).join('') : `<tr><td colspan="7">All courses are assigned.</td></tr>`;
            }

            const assignedCoursesCount = courses.filter(course => course.split('|')[2] !== '0').length;

            // Build and display modal content
            modalBody.innerHTML = `
                <h2 class="course-detail-title">${lecturerName}</h2>
                <p class="course-detail-code">
                    <strong>Credit Hour:</strong> ${totalAssignedCH} / ${maxCH}
                </p>
                <p class="course-detail-code"><strong>Assigned Courses:</strong> ${assignedCoursesCount}</p>
                <div>
                    <p class="course-sections-title">Courses:</p>
                    <div class="course-flashcards">
                        ${coursesHTML}
                    </div>
                </div>
                <div class="timeslots">
                    <p class="course-sections-title">Course Schedule and Availability:</p>
                    <select id="levelDropdown" onchange="loadLectSemesters(this.value)">
                        <option value="">Select Level</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?php echo htmlspecialchars($level['Level_ID']); ?>">
                                <?php echo htmlspecialchars($level['Level_Name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="semesterDropdown" onchange="loadLectSections(this.value)" disabled>
                        <option value="">Select Semester</option>
                        <!-- Semesters will be populated dynamically -->
                    </select>
                    <select id="sectionDropdown" disabled onchange="loadCourses(document.getElementById('levelDropdown').value, document.getElementById('semesterDropdown').value, this.value)">
                        <option value="">Select Section</option>
                        <!-- Sections will be populated dynamically -->
                    </select>
                    <table class="lecturercourselistr" style="margin-top: 10px;">
                        <tbody id="coursesTableBody">
                            ${timeslotsHTML}
                        </tbody>
                    </table>
                </div>
            `;

            modal.style.display = "block";
        }
    })
    .catch(error => {
        console.error('Failed to fetch details:', error);
        modalBody.innerHTML = `<p>Error: Unable to fetch details</p>`;
        modal.style.display = "block";
    });
}

function unassignCourse(assignScheIds, lecturerId, courseName) {
    if (!assignScheIds || assignScheIds.length === 0) {
        alert('Assign_Sche_IDs are missing!');
        return;
    }

    if (confirm(`Do you want to unassign all timeslots for the course '${courseName}' for lecturer ID ${lecturerId}?`)) {
        fetch('unassign_course.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `assignScheIds=${encodeURIComponent(JSON.stringify(assignScheIds))}` // Send array of Assign_Sche_IDs
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Success message from the server
                location.reload(); // Reload the page to reflect the changes
            } else {
                console.error('Server Error:', data.message);
                alert(`Failed to unassign timeslots: ${data.message}`); // Error message from the server
            }
        })
        .catch(error => {
            console.error('Network Error:', error);
            alert('An error occurred while unassigning timeslots.');
        });
    }
}

// Function to remove all course timeslots from the lecturer
function removeAllCourseTimeslots(assignScheIds, lecturerId, courseName) {
    // Ensure assignScheIds is an array
    const scheIds = Array.isArray(assignScheIds) ? assignScheIds : [assignScheIds];

    // Modify the confirmation message to show the course name only once
    const confirmationMessage = `Are you sure you want to remove all timeslots for the course "${courseName}"?`;

    if (confirm(confirmationMessage)) {
        fetch('remove_course_timeslots.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                lecturerId: lecturerId,
                assignScheIds: scheIds  // Send array of timeslot IDs
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();  // Reload the page to reflect changes
            } else {
                alert(`Error: ${data.error}`);
            }
        })
        .catch(error => console.error('Fetch error:', error));
    }
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
                            <div class="lecturercourselist-container" style="margin-inline: 20px; width: 100%">
                                <table class="lecturercourselist" style="margin-inline: 20px; width: 100%">
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
                                
                            </div>
                            `;
                            scheduleData.forEach(slot => {
                                scheduleHTML += `
                                    <tr class="section-row">
                                        <td class="section-cell">${slot.Semester}</td>
                                        <td class="section-cell">${slot.Section_Number}</td>
                                        <td class="section-cell">${slot.Day}</td>
                                        <td class="section-cell">${slot.Time_Slot}</td>
                                        <td class="section-cell">${slot.Course_Code}</td>
                                        <td class="section-cell">${slot.Course_Section}</td>
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


    // Event listeners for closing the modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('lecturerModal');
        const closeButton = document.querySelector('.close');

        closeButton.onclick = function() {
            modal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
            // Code to add event listeners
    document.querySelectorAll('.assign-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const courseName = this.dataset.courseName;
            const assignScheId = this.dataset.assignScheId;
            const lecturerId = this.dataset.lecturerId;
            const courseId = this.dataset.courseId;
            assignToTimeslot(assignScheId, lecturerId, courseName, courseId);
        });
    });
    });


document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll('.edit-btn');
    const editModal = document.getElementById('editLecturerModal');
    const closeEditModal = document.querySelector('.close-edit-modal');
    const editLecturerForm = document.getElementById('editLecturerForm');
    let isDirty = false; // Tracks if there are unsaved changes

    // Function to reset the dirty state
    function resetDirtyState() {
        isDirty = false;
    }

    // Function to check if the form is dirty and confirm navigation
    function confirmUnsavedChanges(event) {
        if (isDirty) {
            const confirmLeave = confirm("You have unsaved changes. Do you want to discard them?");
            if (!confirmLeave) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        }
        return true;
    }

    // Open the modal with lecturer details
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const lecturerRow = this.closest('.lecturer-row');
            const lecturerId = lecturerRow.dataset.lectId;
            const lecturerName = lecturerRow.dataset.lectName;
            const lecturerEmail = lecturerRow.dataset.lectEmail;
            const lecturerCH = lecturerRow.dataset.maxCh;

            // Populate the modal form with the lecturer's data
            document.getElementById('editLecturerId').value = lecturerId;
            document.getElementById('editLecturerName').value = lecturerName;
            document.getElementById('editLecturerEmail').value = lecturerEmail;
            document.getElementById('editLecturerPassword').value = ''; // Clear password field
            document.getElementById('editLecturerCH').value = lecturerCH;

            resetDirtyState();
            editModal.style.display = 'block';
        });
    });


    // Track changes in the form to set dirty state
    editLecturerForm.addEventListener('input', function () {
        isDirty = true;
    });

    // Close the modal with confirmation for unsaved changes
    closeEditModal.onclick = function (event) {
        if (confirmUnsavedChanges(event)) {
            editModal.style.display = 'none';
        }
    };

    // Handle click outside the modal
    window.onclick = function (event) {
        if (event.target === editModal) {
            if (confirmUnsavedChanges(event)) {
                editModal.style.display = 'none';
            }
        }
    };

    // Handle form submission
    editLecturerForm.addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(editLecturerForm);

    fetch('edit_lecturer.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json(); // Parse JSON response
    })
    .then(data => {
        if (data.success) {
            alert(data.message); // Show success message
            location.reload();   // Reload the page to reflect changes
        } else {
            alert(`Failed to update lecturer: ${data.message}`); // Show error message from server
        }
    })
    .catch(error => {
        console.error('Error updating lecturer:', error);
        alert('An error occurred while updating the lecturer.'); // Generic error message
    });
});

});



    function loadLectSemesters(levelID) {
    const semesterDropdown = document.getElementById('semesterDropdown');
    const sectionDropdown = document.getElementById('sectionDropdown');
    if (levelID) {
        fetch(`fetch_semesters.php?level_id=${levelID}`)
            .then(response => response.json())
            .then(data => {
                semesterDropdown.innerHTML = '<option value="">Select Semester</option>';
                data.forEach(semester => {
                    let option = document.createElement('option');
                    option.value = semester.Sem_ID;
                    option.text = semester.Sem_Number;
                    semesterDropdown.appendChild(option);
                });
                semesterDropdown.disabled = false;
                sectionDropdown.innerHTML = '<option value="">Select Section</option>';
                sectionDropdown.disabled = true; // Disable until semester is selected
            })
            .catch(error => {
                console.error('Error fetching semesters:', error);
                semesterDropdown.disabled = true;
                sectionDropdown.disabled = true;
            });
    } else {
        semesterDropdown.disabled = true;
        sectionDropdown.disabled = true;
    }
}

function loadLectSections(semesterID) {
    const sectionDropdown = document.getElementById('sectionDropdown');
    if (semesterID) {
        fetch(`fetch_sections.php?sem_id=${semesterID}`)
            .then(response => response.json())
            .then(data => {
                sectionDropdown.innerHTML = '<option value="">Select Section</option>';
                data.forEach(section => {
                    let option = document.createElement('option');
                    option.value = section.Section_ID;
                    option.text = section.Section_Number;
                    sectionDropdown.appendChild(option);
                });
                sectionDropdown.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching sections:', error);
                sectionDropdown.disabled = true;
            });
    } else {
        sectionDropdown.disabled = true;
    }
}


data.forEach(semester => {
  let option = document.createElement('option');
  option.value = semester.Sem_ID; // Make sure this matches the JSON property
  option.text = semester.Sem_Number; // Make sure this matches the JSON property
  semesterDropdown.appendChild(option);
});

function loadCourses(levelID, semesterID, sectionID) {
    fetch(`fetch_courses_for_lecturer.php?level_id=${levelID}&semester_id=${semesterID}&section_id=${sectionID}`)
        .then(response => response.json())
        .then(data => {
            let coursesContainer = document.getElementById('coursesTableBody');
            coursesContainer.innerHTML = ''; // Clear previous entries

            // Group timeslots for the same course on each day
            const groupedCourses = data.reduce((acc, course) => {
                const key = `${course.Course_Name}-${course.Course_Code}`;
                if (!acc[key]) {
                    acc[key] = {
                        ...course,
                        Days: { [course.Day]: [course.Time_Slot] },  // Initialize with day as key and timeslot in array
                        TimeslotIDs: [course.Assign_Sche_ID], // Initialize with the current timeslot ID
                        Lect_ID: course.Lect_ID // Add the Lecturer ID to check if assigned
                    };
                } else {
                    // Add the new day and time slot if not already present
                    if (!acc[key].Days[course.Day]) {
                        acc[key].Days[course.Day] = [];
                    }
                    acc[key].Days[course.Day].push(course.Time_Slot); // Push the timeslot into the array for that day
                    acc[key].TimeslotIDs.push(course.Assign_Sche_ID); // Push the timeslot ID into the array
                    acc[key].Lect_ID = course.Lect_ID; // Update the Lecturer ID
                }
                return acc;
            }, {});

            // Create flashcards for each grouped course
            Object.values(groupedCourses).forEach(course => {
                // Create a list of timeslots for each day
                let timeslotByDayHTML = '';
                for (const [day, timeSlots] of Object.entries(course.Days)) {
                    timeslotByDayHTML += `<div class="day-list">
                                            <strong class="day-title">${day}:</strong>
                                            <ul class="time-slot-list">`;
                    timeSlots.forEach(slot => {
                        timeslotByDayHTML += `<li class="time-slot">${slot}</li>`;
                    });
                    timeslotByDayHTML += '</ul></div>';
                }

                // Limit the days to 2 (if needed)
                const displayDays = Object.keys(course.Days).slice(0, 2).join(', ');

                // Determine if the course is already assigned
                const isAssigned = course.Lect_ID !== null;

                // Create a flashcard for each course
const flashcard = document.createElement('div');
flashcard.classList.add('flashcard');
flashcard.innerHTML = `
    <h3 class="course-title">${course.Course_Name} (${course.Course_Code})</h3>
    <p><strong>Days:</strong> ${displayDays}</p>
    <p><strong>Time Slots</strong></p>
    ${timeslotByDayHTML}
    <p><strong>Status:</strong> ${isAssigned ? `Assigned to ${course.Lect_Name}` : 'Available'}</p> 
    ${!isAssigned ? 
        `<button onclick="assignAllTimeslots(${course.Assign_Sche_ID}, ${window.currentLecturerId}, ${JSON.stringify(course.TimeslotIDs)})">Assign All</button>`
        : ''}
`;
coursesContainer.appendChild(flashcard);

            });
        })
        .catch(error => console.error('Error fetching courses:', error));
}




function assignAllTimeslots(courseID, lecturerID, timeslotIDs) {
    if (!courseID || !lecturerID || !timeslotIDs || timeslotIDs.length === 0) {
        alert('Course ID, Lecturer ID or timeslot data is missing!');
        return;
    }

    if (confirm('Do you want to assign all timeslots for this course to the selected lecturer?')) {
        fetch('assign_all_timeslots.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ assignScheIds: timeslotIDs, lecturerID })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Timeslots assigned successfully.');
                location.reload();  // Reload to see the changes
            } else {
                alert('Failed to assign timeslots: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error assigning timeslots:', error);
            alert('An error occurred while assigning timeslots.');
        });
    }
}








// Call this function when section dropdown changes
document.getElementById('sectionDropdown').addEventListener('change', function() {
    let levelID = document.getElementById('levelDropdown').value;
    let semesterID = document.getElementById('semesterDropdown').value;
    let sectionID = this.value;
    loadCourses(levelID, semesterID, sectionID);
});






</script>

</html>