<?php
include 'connect.php';
header('Content-Type: application/json');

if (isset($_GET['course_code'])) {
    $courseCode = $_GET['course_code'];

    $query = "SELECT Course_ID FROM course WHERE Course_Code = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param('s', $courseCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();

        if ($course) {
            echo json_encode(['success' => true, 'courseId' => $course['Course_ID']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Course not found.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Course Code is required.']);
}

$conn->close();
?>
