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

if (isset($_GET['lecturer_id'])) {
    $lecturerId = $_GET['lecturer_id'];

    // Fetch courses taken by selected lecturer
    $sql = "SELECT c.*
            FROM course c
            JOIN lecturer_assignment la ON c.Course_ID = la.Course_ID
            WHERE la.Lect_ID = '$lecturerId'";

    $result = $conn->query($sql);

    // Check if there are results for the lecturer's courses
    if ($result->num_rows > 0) {
        echo "<h3>Courses for Selected Lecturer:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Course ID</th><th>Course Name</th><th>Course Credit Hours</th><th>Course Code</th></tr>";
        while ($courseRow = $result->fetch_assoc()) {
            echo "<tr><td>" . $courseRow["Course_ID"] . "</td><td>" . $courseRow["Course_Name"] . "</td><td>" . $courseRow["Course_CH"] . "</td><td>" . $courseRow["Course_Code"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "The selected lecturer has no courses assigned.";
    }
}

$conn->close();
?>
