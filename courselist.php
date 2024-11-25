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

include 'connect.php';

// Fetch courses
$query = "SELECT * FROM course";
$result = mysqli_query($conn, $query);

if ($result) {
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $courses = []; // Set $courses to an empty array if the query fails
}

// Close the connection (optional)
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
    /* Style for the delete button */
.delete-btnr {
    background-color: #e74c3c; /* Red background */
    color: white;  /* White text */
    border: none;  /* Remove border */
    border-radius: 4px; /* Rounded corners */
    padding: 8px 12px; /* Add padding */
    cursor: pointer; /* Change cursor on hover */
    font-size: 14px; /* Adjust font size */
    transition: background-color 0.3s ease; /* Smooth background color transition */
}

/* Hover effect for the delete button */
.delete-btnr:hover {
    background-color: #c0392b; /* Darker red when hovered */
}

/* Focus effect for the delete button */
.delete-btnr:focus {
    outline: none; /* Remove default focus outline */
    box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.6); /* Add custom focus outline */
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
                    <h1>Course List</h1>
                    <div class="search-container">
                        <input type="text" id="searchBar" placeholder="Search by course name or code...">
                    </div>
                </div>
                <button class="add-btn"><i class="fas fa-plus"></i></button>
            </div>
            <div class="courselist">
                <table class="courselist">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credit Hours</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    <tbody id="courseTableBody">
                        <?php foreach ($courses as $course): ?>
                            <tr class="course-row" 
                                data-course-code="<?php echo $course['Course_Code']; ?>"
                                data-course-id="<?php echo $course['Course_ID']; ?>" 
                                data-course-name="<?php echo htmlspecialchars($course['Course_Name']); ?>" 
                                data-course-ch="<?php echo $course['Course_CH']; ?>" >
                                <td><?php echo htmlspecialchars($course['Course_Code']); ?></td>
                                <td><?php echo htmlspecialchars($course['Course_Name']); ?></td>
                                <td><?php echo htmlspecialchars($course['Course_CH']); ?></td>
                                
                                <td>
                                    <button class="edit-btn"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    <div id="paginationControls" class="pagination-controls"></div>
                </div>
            </div>
        
        <div class="course-detail" id="courseDetail">
            <!-- Course details will be dynamically inserted here -->
        </div>
        <!-- New Edit Course Modal -->
<div id="editCourseDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close-edit-details-modal modal-close-icon">&times;</span>
        <h2>Edit Course Details</h2>
        <form id="editCourseDetailsForm">
            <input type="hidden" id="editDetailsCourseID" name="Course_ID">
            <div class="form-group">
                <label for="editCourseName">Course Name:</label>
                <input type="text" id="editCourseName" name="Course_Name" required>
            </div>
            <div class="form-group">
                <label for="editCourseCode">Course Code:</label>
                <input type="text" id="editCourseCode" name="Course_Code" required>
            </div>
            <div class="form-group">
                <label for="editCourseCH">Credit Hour:</label>
                <input type="number" id="editCourseCH" name="Course_CH" required>
            </div>
            <button type="submit" class="submit-btn">Save Changes</button>
        </form>
    </div>
</div>

        <div id="editCourseModal" class="modal">
            <div class="modal-content">
                <span class="close-edit-modal modal-close-icon">&times;</span>
                <h2>Edit Course Details</h2>
                <form id="editCourseForm">
                    <input type="hidden" id="editCourseID" name="Course_ID">

                    <!-- Section List -->
                    <h3>Sections</h3>
                    <div class="section-list-table-container">
                        <table class="section-list-table" id="sectionListTable">
                            <thead>
                                <tr>
                                    <th>Section Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="sectionListBody">
                                <!-- Sections will be loaded dynamically here -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="addSectionButton" class="addsection-btn">Add Section</button>
                    <button type="submit" class="submit-btn">Save Changes</button>
                </form>
            </div>
        </div>
            <div id="addCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal modal-close-icon">&times;</span>
                    <h2>Add New Course</h2>
                    <form id="addCourseForm">
                        <div class="form-group">
                            <label for="Course_Name">Course Name:</label>
                            <input type="text" id="Course_Name" name="Course_Name" required>
                        </div>
                        <div class="form-group">
                            <label for="Course_Code">Course Code:</label>
                            <input type="text" id="Course_Code" name="Course_Code" required>
                        </div>
                        <div class="form-group">
                            <label for="Course_CH">Course Credit Hour:</label>
                            <input type="number" id="Course_CH" name="Course_CH" required>
                        </div>
                        <div class="form-group">
                            <label for="Course_Section">Section Total:</label>
                            <input type="number" id="Course_Section" name="Course_Section" required>
                        </div>
                        <button type="submit" class="submit-btn">Add Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</html>

<script>
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };

    // Store and retrieve last course ID
    function setLastEditedCourseId(courseId) {
        localStorage.setItem('lastEditedCourseId', courseId);
    }

    function getLastEditedCourseId() {
        return localStorage.getItem('lastEditedCourseId');
    }

    function clearLastEditedCourseId() {
        localStorage.removeItem('lastEditedCourseId');
    }
    document.addEventListener("DOMContentLoaded", function() {
    const courseCHInput = document.getElementById("Course_CH");
    const courseSectionInput = document.getElementById("Course_Section");

    // Ensure credit hour and section total cannot be negative
    courseCHInput.addEventListener("input", function() {
        if (courseCHInput.value < 1) {
            courseCHInput.value = 1; // Set the value to 0 if the user tries to input a negative value
        }
    });

    courseSectionInput.addEventListener("input", function() {
        if (courseSectionInput.value < 1) {
            courseSectionInput.value = 1; // Set the value to 0 if the user tries to input a negative value
        }
    });
});

    // COURSE SEARCH
    document.addEventListener("DOMContentLoaded", function() {
        const searchBar = document.getElementById('searchBar');
        const courseRows = document.querySelectorAll('.course-row');

        searchBar.addEventListener('input', function() {
            const searchText = searchBar.value.toLowerCase();
            
            courseRows.forEach(row => {
                const courseName = row.dataset.courseName.toLowerCase();
                const courseCode = row.dataset.courseCode.toLowerCase();
                
                if (courseName.includes(searchText) || courseCode.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // ADD COURSE MODAL
    document.addEventListener("DOMContentLoaded", function() {
    const addButton = document.querySelector(".add-btn");
    const modal = document.getElementById("addCourseModal");
    const closeModal = document.querySelector(".close-modal");
    const addCourseForm = document.getElementById("addCourseForm");
    let hasUnsavedChanges = false;

    // Track changes in the form fields
    document.getElementById("Course_Name").addEventListener("input", () => {
        hasUnsavedChanges = true;
    });
    document.getElementById("Course_Code").addEventListener("input", () => {
        hasUnsavedChanges = true;
    });
    document.getElementById("Course_CH").addEventListener("input", () => {
        hasUnsavedChanges = true;
    });
    document.getElementById("Course_Section").addEventListener("input", () => {
        hasUnsavedChanges = true;
    });

    // Show modal and reset unsaved changes flag
    addButton.onclick = function() {
        modal.style.display = "block";
        hasUnsavedChanges = false; // Reset flag on open
    };

    // Confirm before closing the modal if there are unsaved changes
    function confirmDiscardChanges() {
        if (hasUnsavedChanges) {
            if (confirm("You have unsaved changes. Do you want to discard them?")) {
                closeAddCourseModal();
            }
        } else {
            closeAddCourseModal();
        }
    }

    // Close modal and reset form
    function closeAddCourseModal() {
        modal.style.display = "none";
        addCourseForm.reset(); // Reset form fields
        hasUnsavedChanges = false; // Reset flag
    }

    closeModal.onclick = confirmDiscardChanges;

    // Close the modal when clicking outside of the modal content, with confirmation if there are unsaved changes
    modal.addEventListener("click", function(event) {
        if (event.target === modal) {
            confirmDiscardChanges();
        }
    });

    // AJAX form submission
    addCourseForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(addCourseForm);

        fetch("add_course.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                addCourseForm.reset();
                modal.style.display = "none";
                
                // Save the new course ID to localStorage and reload the page
                setLastEditedCourseId(data.course_id); // Assuming data.course_id contains the new course ID
                location.reload();
            } else {
                alert("Failed to add course: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while adding the course.");
        });
    });
});

    // COURSE DETAILS
    // Reload last edited course detail after page refresh
    document.addEventListener("DOMContentLoaded", function() {
        const lastEditedCourseId = getLastEditedCourseId();
        if (lastEditedCourseId) {
            displayCourseDetails(lastEditedCourseId);
            clearLastEditedCourseId();
        }
    });

    // COURSE DETAILS with displayCourseDetails function incorporated
    document.querySelectorAll('.course-row').forEach(row => {
        row.addEventListener('click', function() {
            const courseId = this.dataset.courseId;
            displayCourseDetails(courseId);
        });
    });

    // Display course details function
    function displayCourseDetails(courseId) {
        const courseRow = document.querySelector(`.course-row[data-course-id="${courseId}"]`);
        const courseList = document.querySelector('.courselist');
        const courseDetail = document.getElementById('courseDetail');

        // Toggle logic for the clicked row
        const isActive = courseRow.classList.contains('active');

        // Remove active state from any currently active row
        document.querySelectorAll('.course-row.active').forEach(activeRow => {
            activeRow.classList.remove('active');
        });

        // If the clicked row or last edited course is already active, collapse and exit
        if (isActive) {
            courseDetail.classList.remove('active'); // Smoothly hide details
            courseDetail.style.maxHeight = "0"; // Reset max-height to trigger smooth collapse
            courseList.classList.remove('active');
            courseDetail.innerHTML = ''; // Clear the details after the transition
            return;
        }

        // Mark the clicked row as active
        courseRow.classList.add('active');

        // Fetch and display course details
        fetch(`table_get_sections_course.php?course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                const sectionsHtml = data.sections.length > 0
                    ? data.sections.map(section => `
                        <tr class="section-row">
                            <td class="section-cell">Section ${section.Course_Section}</td>
                        </tr>
                    `).join('')
                    : `<tr class="no-section-row"><td class="no-section-cell" colspan="1">No sections available.</td></tr>`;

                courseDetail.innerHTML = `
                    <h2 class="course-detail-title">Details for ${courseRow.dataset.courseName}</h2>
                    <p class="course-detail-code"><strong>Course Code:</strong> ${courseRow.dataset.courseCode}</p>
                    <p class="course-detail-ch"><strong>Credit Hours:</strong> ${courseRow.dataset.courseCh}</p>
                    <p class="course-sections-title">Sections:</p>
                    <table class="sections-table">
                        <thead>
                            <tr>
                                <th>Section</th>
                            </tr>
                        </thead>
                        <tbody>${sectionsHtml}</tbody>
                    </table>
                    <button class="course-edit-detail-button" data-course-id="${courseId}" 
                        data-course-name="${courseRow.dataset.courseName}" data-course-code="${courseRow.dataset.courseCode}" 
                        data-course-ch="${courseRow.dataset.courseCh}">Edit Detail</button>
                `;
                courseDetail.classList.add('active');
                courseList.classList.add('active');
                courseDetail.style.maxHeight = "500px"; // Set max-height to trigger smooth expand
                clearLastEditedCourseId(); // Clear only after successfully displaying details
            })
            .catch(error => {
                console.error('Error fetching sections:', error);
                courseDetail.innerHTML = `
                    <h2>Details for ${courseRow.dataset.courseName}</h2>
                    <p>Course Code: ${courseRow.dataset.courseCode}</p>
                    <p>Credit Hours: ${courseRow.dataset.courseCh}</p>
                    <p>Sections:</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Section</th>
                            </tr>
                        </thead>
                        <tbody><tr><td colspan="1">Error loading sections.</td></tr></tbody>
                    </table>
                `;
                courseDetail.classList.add('active');
                courseList.classList.add('active');
                courseDetail.style.maxHeight = "500px";
            });
    }
    document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById("editCourseDetailsModal");
    const closeEditModal = document.querySelector(".close-edit-details-modal");
    const saveChangesButton = document.querySelector(".submit-btn"); // Button to save changes
    let hasUnsavedChanges = false;

    // Track changes in the form fields
    const courseNameInput = document.getElementById("editCourseName");
    const courseCodeInput = document.getElementById("editCourseCode");
    const courseCHInput = document.getElementById("editCourseCH");

    courseNameInput.addEventListener("input", () => {
        hasUnsavedChanges = true;
    });
    courseCodeInput.addEventListener("input", () => {
        hasUnsavedChanges = true;
    });
    courseCHInput.addEventListener("input", () => {
        hasUnsavedChanges = true;
    });

    // Confirm before closing modal if there are unsaved changes
    function confirmDiscardChanges() {
        if (hasUnsavedChanges) {
            if (confirm("You have unsaved changes. Do you want to discard them?")) {
                closeEditModalAction();
            }
        } else {
            closeEditModalAction();
        }
    }

    // Close the modal and reset changes
    function closeEditModalAction() {
        editModal.style.display = "none";
        hasUnsavedChanges = false;
    }

    // Close the modal when clicking on the close button
    closeEditModal.addEventListener("click", function () {
        confirmDiscardChanges();
    });

    // Close modal when clicking outside the modal content
    window.addEventListener("click", function (event) {
        if (event.target === editModal) {
            confirmDiscardChanges();
        }
    });

    // Open Edit Modal
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const courseRow = this.closest('.course-row');  // Get the closest course row
            const courseId = courseRow.dataset.courseId;  // Get the course ID from the data attribute
            
            // Fill the modal form with the course data
            document.getElementById('editDetailsCourseID').value = courseRow.dataset.courseId;
            document.getElementById('editCourseName').value = courseRow.dataset.courseName;
            document.getElementById('editCourseCode').value = courseRow.dataset.courseCode;
            document.getElementById('editCourseCH').value = courseRow.dataset.courseCh;
            
            // Show the edit modal
            editModal.style.display = 'block';
            hasUnsavedChanges = false;  // Reset the unsaved changes flag when opening the modal
        });
    });

    // Handle save changes
    saveChangesButton.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent the form from submitting normally

        // Check if there are any unsaved changes
        if (!hasUnsavedChanges) {
            alert("No new changes to save.");
            closeEditModalAction(); // Close the modal if no changes
        } else {
            // Logic for saving the changes
            const courseId = document.getElementById("editDetailsCourseID").value;
            const courseName = document.getElementById("editCourseName").value;
            const courseCode = document.getElementById("editCourseCode").value;
            const courseCH = document.getElementById("editCourseCH").value;

            // Validate the course data before saving
            if (courseName && courseCode && courseCH) {
                // Send the updated data to the server (example logic for saving)
                fetch("save_course_details.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        course_id: courseId,
                        course_name: courseName,
                        course_code: courseCode,
                        course_ch: courseCH
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Changes saved successfully!");
                        closeEditModalAction(); // Close the modal after saving
                    } else {
                        alert("Failed to save changes.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while saving changes.");
                });
            } else {
                alert("Please fill in all fields.");
            }
        }
    });
});


    // COURSE EDIT DETAIL MODAL
    document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById("editCourseModal");
    const closeEditModal = document.querySelector(".close-edit-modal");
    const sectionListBody = document.getElementById("sectionListBody");
    const addSectionButton = document.getElementById("addSectionButton");
    let unsavedSections = [];
    let originalSections = [];
    let sectionsToDelete = [];
    let hasUnsavedChanges = false;

    // Function to load sections from the database
    function loadSections(courseId) {
        fetch(`fetch_course_sections.php?course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                originalSections = [...data.sections];
                renderSectionList();
            })
            .catch(error => console.error('Error fetching sections:', error));
    }

    // Render the section list (combines original and unsaved changes)
    function renderSectionList() {
    const renderedOriginalSections = originalSections
        .filter(section => !sectionsToDelete.includes(section.Section_ID))
        .map(section => `
            <tr>
                <td>Section ${section.Course_Section}</td>
                <td>
                    <button type="button" class="delete-btnr" onclick="markSectionForDeletion(${section.Section_ID})">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </td>
            </tr>
        `).join('');

    const renderedUnsavedSections = unsavedSections.map(section => `
        <tr>
            <td>New Section ${section.Course_Section}</td>
            <td>
                <button type="button" class="delete-btnr" onclick="removeUnsavedSection(${section.Course_Section})">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');

    sectionListBody.innerHTML = renderedOriginalSections + renderedUnsavedSections;
}


    // Add a new unsaved section
    addSectionButton.onclick = function () {
    // Create a new section
    const newSectionNumber = (Math.max(0, ...unsavedSections.map(s => s.Course_Section), ...originalSections.map(s => s.Course_Section)) || 0) + 1;

    // Add the new section to the unsaved sections array
    unsavedSections.push({ Course_Section: newSectionNumber });

    // Render the updated section list
    renderSectionList();

    // Scroll the newly added section into view
    const lastSection = sectionListBody.lastElementChild;  // Get the last section (newly added)
    if (lastSection) {
        lastSection.scrollIntoView({
            behavior: 'smooth',   // Smooth scroll
            block: 'end'          // Scroll to the end (bottom)
        });
    }

    // Set the flag for unsaved changes
    hasUnsavedChanges = true;
};


    // Mark an existing section for deletion
    window.markSectionForDeletion = function (sectionId) {
        sectionsToDelete.push(sectionId);
        renderSectionList();
        hasUnsavedChanges = true;
    };

    // Remove an unsaved section
    window.removeUnsavedSection = function (sectionNumber) {
        unsavedSections = unsavedSections.filter(section => section.Course_Section !== sectionNumber);
        renderSectionList();
        hasUnsavedChanges = true;
    };

    // Save changes, including new sections and deletions
    document.getElementById("editCourseForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const courseId = document.getElementById("editCourseID").value;
        const courseName = document.getElementById("editCourseName").value;
        const courseCode = document.getElementById("editCourseCode").value;
        const courseCH = document.getElementById("editCourseCH").value;

        // Validate credit hour
        if (courseCH < 1) {
            alert("Credit hours must be at least 1.");
            document.getElementById("editCourseCH").focus(); // Focus on the credit hour input
            return;
        }

        // Check for changes
        const isCourseNameChanged = courseName !== originalCourseData.name;
        const isCourseCodeChanged = courseCode !== originalCourseData.code;
        const isCourseCHChanged = courseCH !== originalCourseData.ch;
        const hasUnsavedSectionChanges = unsavedSections.length > 0 || sectionsToDelete.length > 0;

        if (!isCourseNameChanged && !isCourseCodeChanged && !isCourseCHChanged && !hasUnsavedSectionChanges) {
            // No changes detected
            alert("No new changes to save.");
            editModal.style.display = "none"; // Close the modal
            return;
        }

        // Send data to the server
        fetch("save_course_sections.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                course_id: courseId,
                course_name: courseName,
                course_code: courseCode,
                course_ch: courseCH,
                new_sections: unsavedSections,
                deleted_sections: sectionsToDelete,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Changes saved successfully!");
                    // Reset unsaved changes tracking
                    unsavedSections = [];
                    sectionsToDelete = [];
                    hasUnsavedChanges = false;
                    location.reload(); // Reload to reflect changes
                } else {
                    alert("Error saving changes: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error saving changes:", error);
                alert("An error occurred while saving changes.");
            });
    });

    // Confirm before closing modal with unsaved changes
    closeEditModal.onclick = function () {
        if (hasUnsavedChanges && !confirm("You have unsaved changes. Do you really want to close without saving?")) {
            return;
        }
        editModal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target === editModal && hasUnsavedChanges) {
            if (confirm("You have unsaved changes. Do you really want to close without saving?")) {
                editModal.style.display = "none";
            }
        }
    };

    // Open Edit Modal
    let originalCourseData = {};

    document.body.addEventListener("click", function (event) {
        if (event.target.classList.contains("course-edit-detail-button")) {
            const courseId = event.target.dataset.courseId;
            const courseName = event.target.dataset.courseName;
            const courseCode = event.target.dataset.courseCode;
            const courseCH = event.target.dataset.courseCh;

            originalCourseData = {
                name: courseName,
                code: courseCode,
                ch: courseCH,
            };

            document.getElementById("editCourseID").value = courseId;
            document.getElementById("editCourseName").value = courseName;
            document.getElementById("editCourseCode").value = courseCode;
            document.getElementById("editCourseCH").value = courseCH;

            unsavedSections = [];
            sectionsToDelete = [];
            hasUnsavedChanges = false;
            loadSections(courseId);
            editModal.style.display = "block";
        }
    });

    // Close modal if clicked outside the modal area
    window.addEventListener("click", function (event) {
        if (event.target === editModal) {
            if (hasUnsavedChanges) {
                if (confirm("You have unsaved changes. Do you want to discard them?")) {
                    editModal.style.display = "none";
                }
            } else {
                editModal.style.display = "none";
            }
        }
    });
});





    document.addEventListener("DOMContentLoaded", function () {
        const courseRows = Array.from(document.querySelectorAll('.course-row'));
        const searchBar = document.getElementById('searchBar');
        const itemsPerPage = 12;
        let currentPage = 1;
        let filteredCourses = courseRows;

        // Render the current page of courses
        function renderPage(page) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            // Hide all courses, then display only the ones for the current page
            filteredCourses.forEach((course, index) => {
                course.style.display = (index >= start && index < end) ? '' : 'none';
            });

            renderPaginationControls();
        }

        // Render pagination controls based on the filtered courses
        function renderPaginationControls() {
            const totalPages = Math.ceil(filteredCourses.length / itemsPerPage);
            const paginationControls = document.getElementById('paginationControls');
            paginationControls.innerHTML = '';

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


        // Update the filteredCourses array based on the search term and reset pagination
        function filterCourses() {
            const searchText = searchBar.value.toLowerCase();

            filteredCourses = courseRows.filter(row => {
                const courseName = row.dataset.courseName.toLowerCase();
                const courseCode = row.dataset.courseCode.toLowerCase();
                return courseName.includes(searchText) || courseCode.includes(searchText);
            });

            currentPage = 1; // Reset to first page after search
            renderPage(currentPage);
        }

        // Search event listener
        searchBar.addEventListener('input', filterCourses);

        // Initial render of pagination with all courses
        renderPage(currentPage);
    });

    document.addEventListener("DOMContentLoaded", function() {
    // Select all delete buttons (trash icons)
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();  // Prevent row click event
            const courseRow = this.closest('.course-row');  // Get the closest course row
            const courseId = courseRow.dataset.courseId;  // Get the course ID from data attribute
            
            // Ask for confirmation before deleting
            if (confirm('Are you sure you want to delete this course?')) {
                // Send a delete request to the server via AJAX
                fetch('delete_course.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `course_id=${courseId}`  // Send the course ID to the server
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // If deletion is successful, remove the row from the DOM
                        courseRow.remove();
                        alert('Course deleted successfully!');
                    } else {
                        alert(data.message);  // Show message from the server
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the course.');
                });
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Open the modal when the "edit" button is clicked
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const courseRow = this.closest('.course-row');  // Get the closest course row
            const courseId = courseRow.dataset.courseId;  // Get the course ID from the data attribute
            
            // Fill the modal form with the course data
            document.getElementById('editDetailsCourseID').value = courseRow.dataset.courseId;
            document.getElementById('editCourseName').value = courseRow.dataset.courseName;
            document.getElementById('editCourseCode').value = courseRow.dataset.courseCode;
            document.getElementById('editCourseCH').value = courseRow.dataset.courseCh;
            
            // Show the edit modal
            document.getElementById('editCourseDetailsModal').style.display = 'block';
        });
    });

    // Close the modal when clicking on close button
    document.querySelector('.close-edit-details-modal').addEventListener('click', function() {
        document.getElementById('editCourseDetailsModal').style.display = 'none';
    });

    // Close modal when clicking outside the modal content
    window.onclick = function(event) {
        if (event.target === document.getElementById('editCourseDetailsModal')) {
            document.getElementById('editCourseDetailsModal').style.display = 'none';
        }
    };

    // Handle form submission to update course details
    document.getElementById("editCourseDetailsForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch("update_course_details.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();  // Reload the page to see the changes
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while updating the course.");
        });
    });
});



</script>
