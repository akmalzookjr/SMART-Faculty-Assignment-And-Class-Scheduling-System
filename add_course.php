<?php
// Include the database connection
include 'connect.php';

// Retrieve form data
$courseName = $_POST['Course_Name'];
$courseCode = $_POST['Course_Code'];
$courseCH = $_POST['Course_CH'];
$courseSection = $_POST['Course_Section'];

try {
    // Check if the course name or code already exists
    $checkQuery = $conn->prepare("SELECT * FROM course WHERE Course_Name = ? OR Course_Code = ?");
    $checkQuery->bind_param("ss", $courseName, $courseCode);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Course name or code already exists.']);
        exit;
    }

    // Insert course data into the course table
    $stmt = $conn->prepare("INSERT INTO course (Course_Name, Course_CH, Course_Code) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $courseName, $courseCH, $courseCode);
    $stmt->execute();
    
    // Get last inserted Course_ID for section insertions
    $courseId = $conn->insert_id;

    // Insert course sections
    $stmt = $conn->prepare("INSERT INTO course_section (Course_ID, Course_Section) VALUES (?, ?)");
    for ($i = 1; $i <= $courseSection; $i++) {
        $stmt->bind_param("ii", $courseId, $i);
        $stmt->execute();
    }

    // Return success message with the new course ID
    echo json_encode([
        'success' => true,
        'message' => 'Course added successfully',
        'course_id' => $courseId  // Return the new Course_ID
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to add course']);
}
?>
