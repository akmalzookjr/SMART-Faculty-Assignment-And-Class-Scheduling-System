<?php
include 'connect.php';

$courseId = $_GET['course_id'];
$query = "SELECT Course_Section_ID as Section_ID, Course_Section FROM course_section WHERE Course_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$result = $stmt->get_result();
$sections = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['sections' => $sections]);
$stmt->close();
$conn->close();
?>
