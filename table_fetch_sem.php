<?php
include("connect.php");

$level_id = $_GET['level_id'];
$query = "SELECT Sem_ID, Sem_Number FROM Semester WHERE Level_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $level_id);
$stmt->execute();
$result = $stmt->get_result();

$semesters = [];
while ($row = $result->fetch_assoc()) {
    $semesters[] = $row;
}

echo json_encode(['semesters' => $semesters]);
?>
