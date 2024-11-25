<?php
include 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$sectionId = $data['section_id'];

if (!$sectionId) {
    echo json_encode(['success' => false, 'message' => 'No section ID provided.']);
    exit;
}

// Check if the section is assigned to any lecturer
$checkQuery = "SELECT COUNT(*) as assignment_count FROM Lecturer_Assignment WHERE Course_Section_ID = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $sectionId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['assignment_count'] > 0) {
    // Section is assigned to a lecturer, so prevent deletion
    echo json_encode(['success' => false, 'message' => 'This section cannot be deleted because it is assigned to a lecturer.']);
} else {
    // Proceed with deletion if no assignment exists
    $deleteQuery = "DELETE FROM course_section WHERE Course_Section_ID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $sectionId);
    $success = $stmt->execute();

    echo json_encode(['success' => $success, 'message' => $success ? 'Section deleted successfully.' : 'Failed to delete section.']);
}

$stmt->close();
$conn->close();
?>
