<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = $_POST['Course_ID'];
    $courseName = $_POST['Course_Name'];
    $courseCode = $_POST['Course_Code'];
    $courseCH = $_POST['Course_CH'];

    $query = "UPDATE course SET Course_Name=?, Course_Code=?, Course_CH=? WHERE Course_ID=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $courseName, $courseCode, $courseCH, $courseId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update course.']);
    }

    $stmt->close();
    $conn->close();
}
?>
