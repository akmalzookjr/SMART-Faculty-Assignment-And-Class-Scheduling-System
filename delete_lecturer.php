<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturerId = $_POST['lecturer_id'] ?? null;

    if ($lecturerId) {
        // Check if the lecturer has any assignments
        $checkAssignmentsSql = "SELECT COUNT(*) as assignment_count FROM Lecturer_Assignment WHERE Lect_ID = ?";
        $stmt = $conn->prepare($checkAssignmentsSql);
        $stmt->bind_param('i', $lecturerId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['assignment_count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Lecturer has assigned courses and cannot be deleted.']);
            exit;
        }

        // Delete lecturer from the database
        $deleteSql = "DELETE FROM Lecturer WHERE Lect_ID = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param('i', $lecturerId);
        $success = $stmt->execute();

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete lecturer.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid lecturer ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
