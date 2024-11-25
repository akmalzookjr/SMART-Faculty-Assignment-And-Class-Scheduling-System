<?php
// get_courses.php
include("connect.php");
$studentId = $_GET['student_id'];

// table_get_courses.php
$query = "SELECT c.Course_Name, c.Course_CH, cs.Assign_Sche_ID, s.Time_Slot, cs2.Course_Section
          FROM Course_Student cs
          JOIN Course c ON cs.Course_ID = c.Course_ID
          LEFT JOIN Assign_Schedule a ON cs.Assign_Sche_ID = a.Assign_Sche_ID
          LEFT JOIN Schedule s ON a.Sche_ID = s.Sche_ID
          LEFT JOIN Course_Section cs2 ON a.Course_Section_ID = cs2.Course_Section_ID
          WHERE cs.Stud_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($courses);

?>