<?php
include 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$courseId = $data['course_id'];
$sections = $data['sections']; // This should be an array of sections

// Start transaction for multiple inserts
$conn->begin_transaction();

try {
    // Get the current maximum section number for the course
    $query = "SELECT MAX(Course_Section) AS max_section FROM course_section WHERE Course_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $nextSection = ($row['max_section'] ?? 0) + 1;

    // Prepare the insert statement for new sections
    $insertQuery = "INSERT INTO course_section (Course_ID, Course_Section) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);

    foreach ($sections as $section) {
        $stmt->bind_param("ii", $courseId, $nextSection);
        $stmt->execute();
        $nextSection++; // Increment for each new section
    }

    // Commit transaction if all inserts were successful
    $conn->commit();
    echo json_encode([
        'success' => true,
        'message' => 'All sections added successfully.'
    ]);

} catch (Exception $e) {
    // Rollback transaction in case of an error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add sections: ' . $e->getMessage()
    ]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
