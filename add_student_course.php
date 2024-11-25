<?php
include 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$studentId = $data['studentId'];
$courseId = $data['courseId'];

$query = "INSERT INTO course_student (Stud_ID, Course_ID) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $studentId, $courseId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
