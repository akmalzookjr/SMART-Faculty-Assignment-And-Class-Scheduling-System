<?php
include("connect.php");

$data = json_decode(file_get_contents("php://input"), true);
$assignScheIds = $data['assignScheIds'];
$lecturerId = $data['lecturerId'];

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

// Example of an SQL query to assign multiple time slots
$query = "INSERT INTO Assignments (Assign_Sche_ID, Lect_ID) VALUES ";
$values = [];

foreach ($assignScheIds as $id) {
    $values[] = "('$id', '$lecturerId')";
}

$query .= implode(", ", $values) . ";";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Courses assigned successfully']);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
