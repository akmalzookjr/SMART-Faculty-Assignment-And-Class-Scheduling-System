<?php
include("connect.php");

$section_id = $_GET['section_id'];
$sql = "SELECT * FROM course WHERE Section_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $section_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = [];

while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
