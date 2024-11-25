<?php
include 'connect.php';

$courseId = $_GET['course_id'];

// Fetch sections for this course
$sectionsQuery = $conn->prepare("SELECT Course_Section FROM course_section WHERE Course_ID = ?");
$sectionsQuery->bind_param("i", $courseId);
$sectionsQuery->execute();
$sectionsResult = $sectionsQuery->get_result();
$sections = $sectionsResult->fetch_all(MYSQLI_ASSOC);

// Return JSON response
echo json_encode([
    'sections' => $sections,
]);

// Close connections
$sectionsQuery->close();
$conn->close();
?>
