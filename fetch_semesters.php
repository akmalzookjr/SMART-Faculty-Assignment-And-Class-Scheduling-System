<?php
include("connect.php");

$level_id = $_GET['level_id'];
$sql = "SELECT * FROM semester WHERE Level_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $level_id);
$stmt->execute();
$result = $stmt->get_result();
$semesters = [];

while ($row = mysqli_fetch_assoc($result)) {
    $semesters[] = $row;
}

echo json_encode($semesters);
?>
