<?php
include 'connect.php';

$studentName = $_POST['studentName'];
$sectionId = $_POST['sectionNumber'];
$courses = $_POST['courses'] ?? [];

// Insert student into the database
mysqli_query($conn, "INSERT INTO student (Stud_Name, Section_ID) VALUES ('$studentName', $sectionId)");
$studentId = mysqli_insert_id($conn); // Get the newly inserted student ID

// Insert repeated courses for the student
foreach ($courses as $courseId) {
    mysqli_query($conn, "INSERT INTO course_student (Course_ID, Stud_ID) VALUES ($courseId, $studentId)");
}

echo json_encode(['success' => true]);
?>
