<?php
include("connect.php");

$assignScheIds = json_decode($_POST['assignScheIds']);
$lectId = $_POST['lectId'];

// Prepare a SQL statement to update the Lecturer_Assignment table
$stmt = $conn->prepare("UPDATE Lecturer_Assignment SET Lect_ID = ? WHERE Assign_Sche_ID IN (?)");
$stmt->bind_param("ii", $lectId, $placeholderForInClause);

// You need to prepare the SQL IN clause dynamically based on the number of IDs
$placeholderForInClause = implode(',', array_fill(0, count($assignScheIds), '?'));

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating assignments"]);
}

mysqli_close($conn);
?>
