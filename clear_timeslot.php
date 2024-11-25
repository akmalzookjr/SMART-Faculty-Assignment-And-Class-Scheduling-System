<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = $_POST['day'];
    $timeSlot = $_POST['timeSlot'];
    $section = $_POST['section'];

    // Validate inputs
    if (!$day || !$timeSlot || !$section) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    try {
        // Start transaction
        mysqli_begin_transaction($conn);

        // Find the Assign_Sche_ID based on the inputs
        $assignScheduleQuery = "SELECT Assign_Sche_ID 
                                FROM Assign_Schedule 
                                WHERE Section_ID = ? 
                                  AND Sche_ID = (SELECT Sche_ID 
                                                 FROM Schedule 
                                                 WHERE Day = ? AND Time_Slot = ?)";
        $stmt = mysqli_prepare($conn, $assignScheduleQuery);
        mysqli_stmt_bind_param($stmt, 'iss', $section, $day, $timeSlot);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $assignScheID);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if (!$assignScheID) {
            echo json_encode(['status' => 'error', 'message' => 'No matching record found']);
            exit;
        }

        // Delete from Lecturer_Assignment if exists
        $deleteLecturerAssignment = "DELETE FROM Lecturer_Assignment WHERE Assign_Sche_ID = ?";
        $stmt = mysqli_prepare($conn, $deleteLecturerAssignment);
        mysqli_stmt_bind_param($stmt, 'i', $assignScheID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from Assign_Schedule
        $deleteAssignSchedule = "DELETE FROM Assign_Schedule WHERE Assign_Sche_ID = ?";
        $stmt = mysqli_prepare($conn, $deleteAssignSchedule);
        mysqli_stmt_bind_param($stmt, 'i', $assignScheID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Commit transaction
        mysqli_commit($conn);

        echo json_encode(['status' => 'success', 'message' => 'Time slot cleared successfully']);
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Failed to clear time slot: ' . $e->getMessage()]);
    }

    mysqli_close($conn);
}
?>
