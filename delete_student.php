<?php
session_start();

// Check if the user is logged in and has the role 'coordinator'
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'coordinator') {
    // Redirect to login page if not logged in or not a coordinator
    header('Location: login.php');
    exit();
}

// Include the database connection
include 'connect.php';

// Check if the student ID is provided in the GET request
if (isset($_GET['student_id'])) {
    $studentId = intval($_GET['student_id']); // Sanitize the input

    // First, check if the student has any repeated courses
    $checkQuery = "SELECT COUNT(*) AS repeated_courses_count
                   FROM course_student 
                   WHERE Stud_ID = ? 
                   AND Course_Stud_ID IN (SELECT Course_Stud_ID FROM course_student WHERE Assignment_ID IS NOT NULL)";
    
    if ($stmt = mysqli_prepare($conn, $checkQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $studentId); // Bind the student ID parameter
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $repeatedCoursesCount);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // If there are repeated courses, do not delete the student and show a custom message
        if ($repeatedCoursesCount > 0) {
            echo json_encode(['success' => false, 'message' => 'The student has repeated course assignments.']);
            exit();
        }
    }

    // Delete the student from the database
    $query = "DELETE FROM student WHERE Stud_ID = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $studentId); // Bind the student ID parameter
        if (mysqli_stmt_execute($stmt)) {
            // Successfully deleted
            echo json_encode(['success' => true]);
        } else {
            // Failed to delete
            echo json_encode(['success' => false, 'message' => 'Failed to delete the student.']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare the SQL query.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Student ID is missing.']);
}

// Close the database connection
mysqli_close($conn);
?>
