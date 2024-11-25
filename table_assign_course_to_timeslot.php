<?php
include("connect.php");

$data = json_decode(file_get_contents("php://input"), true);
$assignScheId = $data['assignScheId'];
$studentId = $data['studentId'];
$courseId = $data['courseId'];  // This should be passed from JavaScript

if (!$assignScheId || !$studentId || !$courseId) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

// Update the specific course for the student
$query = "UPDATE course_student SET Assign_Sche_ID = ? WHERE Stud_ID = ? AND Course_ID = ? AND Assign_Sche_ID IS NULL";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $assignScheId, $studentId, $courseId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();

?>
