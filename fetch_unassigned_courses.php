<?php
include("connect.php");

// Fetch unassigned courses based on section ID
if (isset($_GET['section_id'])) {
    $sectionId = $_GET['section_id'];

    $sql = "SELECT c.*, l.Level_Name, s.Sem_Number, sec.Section_Number
            FROM course c
            LEFT JOIN lecturer_assignment la ON c.Course_ID = la.Course_ID
            LEFT JOIN section sec ON la.Section_ID = sec.Section_ID
            LEFT JOIN semester s ON sec.Sem_ID = s.Sem_ID
            LEFT JOIN level l ON s.Level_ID = l.Level_ID
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
