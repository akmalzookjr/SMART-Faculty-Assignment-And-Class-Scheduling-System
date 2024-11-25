<?php
include 'connect.php';

$studentId = $_GET['student_id'];
$query = "SELECT course.Course_ID, course.Course_Code, course.Course_Name 
          FROM course_student 
          INNER JOIN course ON course_student.Course_ID = course.Course_ID 
          WHERE course_student.Stud_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['courses' => $courses]);
?>
