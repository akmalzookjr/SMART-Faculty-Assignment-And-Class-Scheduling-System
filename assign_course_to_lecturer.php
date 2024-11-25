<?php
include("connect.php");

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Debugging: Log the received data to ensure it's correct
    error_log("Received Data: " . print_r($data, true));
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit();
    }

    $assignScheId = $data['assignScheId'];
    $lecturerId = $data['lecturerId'];
    $courseId = $data['courseId'];

    if (!$conn) {
        echo json_encode(['success' => false, 'error' => 'Database connection error']);
        exit();
    }

    // Insert into Lecturer_Assignment table to assign the course to the lecturer
    $insertQuery = "INSERT INTO Lecturer_Assignment (Assign_Sche_ID, Lect_ID) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'SQL preparation error: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("ii", $assignScheId, $lecturerId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log('SQL Execution Error: ' . $stmt->error);
        echo json_encode(['success' => false, 'error' => 'Failed to assign course to lecturer: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
