<?php
include("connect.php");

$sem_id = $_GET['sem_id'];
$query = "SELECT Section_ID, Section_Number FROM Section WHERE Sem_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $sem_id);
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while ($row = $result->fetch_assoc()) {
    $sections[] = $row;
}

echo json_encode(['sections' => $sections]);
?>
