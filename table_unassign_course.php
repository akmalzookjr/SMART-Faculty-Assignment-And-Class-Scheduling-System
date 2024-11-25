<?php
include("connect.php");

$data = json_decode(file_get_contents("php://input"), true);
$assignScheId = $data['assignScheId'];
$studentId = $data['studentId'];

if (!$assignScheId || !$studentId) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$query = "UPDATE course_student SET Assign_Sche_ID = NULL WHERE Stud_ID = ? AND Assign_Sche_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $studentId, $assignScheId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
