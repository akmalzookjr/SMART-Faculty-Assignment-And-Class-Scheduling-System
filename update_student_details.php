<?php
include 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['Stud_ID'], $data['Stud_Name'], $data['Level_ID'], $data['Sem_ID'], $data['Section_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$studId = $data['Stud_ID'];
$studName = $data['Stud_Name'];
$levelId = $data['Level_ID'];
$semId = $data['Sem_ID'];
$sectionId = $data['Section_ID'];

// Update the student details
$query = "UPDATE student 
          SET Stud_Name = ?, Section_ID = ? 
          WHERE Stud_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $studName, $sectionId, $studId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update student details.']);
}

$stmt->close();
$conn->close();
?>
