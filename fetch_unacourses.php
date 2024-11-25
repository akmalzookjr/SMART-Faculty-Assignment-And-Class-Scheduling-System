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

// Fetch unassigned courses based on section ID
if (isset($_GET['section_id'])) {
    $sectionId = $_GET['section_id'];

    $sql = "SELECT c.*
            FROM course c
            LEFT JOIN lecturer_assignment la ON c.Course_ID = la.Course_ID
            WHERE la.Lect_ID IS NULL AND la.Section_ID = '$sectionId'";

    $result = $conn->query($sql);

    $courses = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }

    // Return the courses as JSON
    echo json_encode($courses);
}
?>
