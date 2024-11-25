<?php
include("connect.php");

$data = json_decode(file_get_contents("php://input"), true);
$assignScheId = $data['assignScheId'] ?? null;
$lecturerId = $data['lecturerId'] ?? null;

// Log received data for debugging
error_log("Received data in assign_lecturer.php: " . json_encode($data));

if (!$assignScheId || !$lecturerId) {
    echo json_encode(["success" => false, "error" => "Missing required fields", "received" => $data]);
    exit;
}

// Update the lecturer assignment in the database
$sql = "UPDATE Lecturer_Assignment SET Lect_ID = ? WHERE Assign_Sche_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $lecturerId, $assignScheId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
