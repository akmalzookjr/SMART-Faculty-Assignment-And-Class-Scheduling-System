<?php
// Database connection
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "smartsystem";

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch levels
$levels = $conn->query("SELECT * FROM level");
?>

<!-- Dropdown Form -->
<form method="post" action="">
    <label for="level">Level:</label>
    <select name="level" id="level" onchange="fetchSemesters(this.value)">
        <option value="">Select Level</option>
        <?php while ($level = $levels->fetch_assoc()): ?>
            <option value="<?php echo $level['Level_ID']; ?>"><?php echo $level['Level_Name']; ?></option>
        <?php endwhile; ?>
    </select>

    <label for="semester">Semester:</label>
    <select name="semester" id="semester" onchange="fetchSections(this.value)">
        <option value="">Select Semester</option>
    </select>

    <label for="section">Section:</label>
    <select name="section" id="section" onchange="fetchUnassignedCourses(this.value)">
        <option value="">Select Section</option>
    </select>
</form>

<!-- Placeholder for showing unassigned courses -->
<div id="unassignedCoursesContainer"></div>

<script>
// Fetch semesters based on selected level
function fetchSemesters(levelId) {
    var semesterSelect = document.getElementById('semester');
    semesterSelect.innerHTML = '<option value="">Select Semester</option>'; // Reset semester options

    if (levelId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_sem.php?level_id=' + levelId, true);
        xhr.onload = function() {
            if (this.status == 200) {
                var semesters = JSON.parse(this.responseText);
                semesters.forEach(function(semester) {
                    var option = document.createElement('option');
                    option.value = semester.Sem_ID;
                    option.textContent = semester.Sem_Number;
                    semesterSelect.appendChild(option);
                });
            }
        };
        xhr.send();
    }
}

// Fetch sections based on selected semester
function fetchSections(semesterId) {
    var sectionSelect = document.getElementById('section');
    sectionSelect.innerHTML = '<option value="">Select Section</option>'; // Reset section options

    if (semesterId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_sec.php?semester_id=' + semesterId, true);
        xhr.onload = function() {
            if (this.status == 200) {
                var sections = JSON.parse(this.responseText);
                sections.forEach(function(section) {
                    var option = document.createElement('option');
                    option.value = section.Section_ID;
                    option.textContent = section.Section_Number;
                    sectionSelect.appendChild(option);
                });
            }
        };
        xhr.send();
    }
}

// Fetch unassigned courses based on selected section
function fetchUnassignedCourses(sectionId) {
    var unassignedCoursesContainer = document.getElementById('unassignedCoursesContainer');
    unassignedCoursesContainer.innerHTML = ''; // Clear existing content

    if (sectionId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_unacourses.php?section_id=' + sectionId, true);
        xhr.onload = function() {
            if (this.status == 200) {
                var courses = JSON.parse(this.responseText);

                if (courses.length > 0) {
                    var table = '<table border="1"><tr><th>Course ID</th><th>Course Name</th><th>Course Credit Hours</th><th>Course Code</th></tr>';
                    courses.forEach(function(course) {
                        table += '<tr><td>' + course.Course_ID + '</td><td>' + course.Course_Name + '</td><td>' + course.Course_CH + '</td><td>' + course.Course_Code + '</td></tr>';
                    });
                    table += '</table>';
                    unassignedCoursesContainer.innerHTML = table;
                } else {
                    unassignedCoursesContainer.innerHTML = 'No unassigned courses found for this section.';
                }
            }
        };
        xhr.send();
    }
}
</script>
