<?php
include 'connect.php';

header('Content-Type: application/json'); // Ensure JSON output

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['studentId']) || !isset($data['courseIds'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    $studentId = $data['studentId'];
    $newCourseIds = $data['courseIds'];

    // Start database transaction
    $conn->begin_transaction();

    // Fetch currently assigned courses for the student
    $stmt = $conn->prepare("SELECT Course_ID FROM course_student WHERE Stud_ID = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingCourseIds = [];
    while ($row = $result->fetch_assoc()) {
        $existingCourseIds[] = $row['Course_ID'];
    }
    $stmt->close();

    // Determine which courses to add
    $coursesToAdd = array_diff($newCourseIds, $existingCourseIds);
    
    // Determine which courses to remove
    $coursesToRemove = array_diff($existingCourseIds, $newCourseIds);

    // Insert new courses
    if (!empty($coursesToAdd)) {
        $stmt = $conn->prepare("INSERT INTO course_student (Stud_ID, Course_ID) VALUES (?, ?)");
        foreach ($coursesToAdd as $courseId) {
            $stmt->bind_param("ii", $studentId, $courseId);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Remove courses
    if (!empty($coursesToRemove)) {
        $stmt = $conn->prepare("DELETE FROM course_student WHERE Stud_ID = ? AND Course_ID = ?");
        foreach ($coursesToRemove as $courseId) {
            $stmt->bind_param("ii", $studentId, $courseId);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Commit the transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Courses updated successfully.']);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error updating courses: ' . $e->getMessage()]);
}

$conn->close();
?>
