<?php
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'dean') {
    // Redirect to login if not logged in or not a dean
    header('Location: login.php');
    exit();
}

include 'connect.php';  // Include database connection

// Check if course ID is passed via POST
if (isset($_POST['course_id'])) {
    $courseId = $_POST['course_id'];

    // Check if the course has any sections assigned to it
    $checkSectionsSql = "SELECT COUNT(*) AS section_count FROM Course_Section WHERE Course_ID = ?";
    $stmt = mysqli_prepare($conn, $checkSectionsSql);
    mysqli_stmt_bind_param($stmt, "i", $courseId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $sectionCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($sectionCount > 0) {
        // If there are sections, inform the user they can't delete the course
        echo json_encode([
            'success' => false,
            'message' => 'This course has assigned sections. You must delete the sections first before deleting the course.'
        ]);
    } else {
        // If no sections, proceed with deleting the course
        $deleteCourseSql = "DELETE FROM course WHERE Course_ID = ?";
        if ($deleteStmt = mysqli_prepare($conn, $deleteCourseSql)) {
            mysqli_stmt_bind_param($deleteStmt, "i", $courseId);
            if (mysqli_stmt_execute($deleteStmt)) {
                echo json_encode(['success' => true]);  // Return success response
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete course.']);
            }
            mysqli_stmt_close($deleteStmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing the query.']);
        }
    }

    mysqli_close($conn);  // Close the database connection
} else {
    echo json_encode(['success' => false, 'message' => 'No course ID provided.']);
}
?>
