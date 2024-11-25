<?php
include("connect.php");

$sem_id = $_GET['sem_id'];
$sql = "SELECT * FROM section WHERE Sem_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sem_id);
$stmt->execute();
$result = $stmt->get_result();
$sections = [];

while ($row = mysqli_fetch_assoc($result)) {
    $sections[] = $row;
}

echo json_encode($sections);
?>
