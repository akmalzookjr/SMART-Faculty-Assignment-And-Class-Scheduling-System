<?php
session_start();

// Check if the user is logged in and has the role 'dean'
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'dean') {
    // Redirect to login page if not logged in or not a dean
    header('Location: login.php');
    exit();
}

include 'connect.php';  // Include database connection

// Check if course ID and the new course details are sent via POST
if (isset($_POST['Course_ID']) && isset($_POST['Course_Name']) && isset($_POST['Course_Code']) && isset($_POST['Course_CH'])) {
    $courseId = $_POST['Course_ID'];
    $courseName = $_POST['Course_Name'];
    $courseCode = $_POST['Course_Code'];
    $courseCH = $_POST['Course_CH'];

    // Prepare SQL query to update the course details
    $sql = "UPDATE course SET Course_Name = ?, Course_Code = ?, Course_CH = ? WHERE Course_ID = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssii", $courseName, $courseCode, $courseCH, $courseId);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Course updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update course.']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing the query.']);
    }

    mysqli_close($conn);  // Close the database connection
} else {
    echo json_encode(['success' => false, 'message' => 'Required data missing.']);
}
?>
