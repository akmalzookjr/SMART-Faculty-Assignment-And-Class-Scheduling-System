<?php
include("connect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$assignScheIds = isset($_POST['assignScheIds']) ? json_decode($_POST['assignScheIds'], true) : [];

if (empty($assignScheIds)) {
    echo json_encode(['success' => false, 'message' => 'Missing Assign_Sche_IDs']);
    exit;
}

try {
    // Get the Course_ID for all Assign_Sche_IDs
    $query = "
        SELECT DISTINCT 
            Course_Section.Course_ID
        FROM 
            Assign_Schedule
        INNER JOIN 
            Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
        WHERE 
            Assign_Schedule.Assign_Sche_ID IN (" . implode(',', $assignScheIds) . ")
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No related course found']);
        exit;
    }

    // Fetch the Course_ID from the result
    $courseId = $result->fetch_assoc()['Course_ID'];

    // Fetch all Assign_Sche_IDs for the same Course_ID (across all days)
    $queryTimeslots = "
        SELECT 
            Assign_Schedule.Assign_Sche_ID
        FROM 
            Assign_Schedule
        INNER JOIN 
            Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
        WHERE 
            Course_Section.Course_ID = ?
    ";

    $stmtTimeslots = $conn->prepare($queryTimeslots);
    $stmtTimeslots->bind_param('i', $courseId);
    $stmtTimeslots->execute();
    $resultTimeslots = $stmtTimeslots->get_result();

    $allAssignScheIds = [];
    while ($row = $resultTimeslots->fetch_assoc()) {
        $allAssignScheIds[] = $row['Assign_Sche_ID'];
    }

    if (empty($allAssignScheIds)) {
        echo json_encode(['success' => false, 'message' => 'No related timeslots found']);
        exit;
    }

    // Now, delete the related records from lecturer_assignment for all Assign_Sche_IDs
    $deleteLecturerAssignment = "DELETE FROM lecturer_assignment WHERE Assign_Sche_ID IN (" . implode(',', $allAssignScheIds) . ")";
    $stmtDeleteLecturerAssignment = $conn->prepare($deleteLecturerAssignment);
    $stmtDeleteLecturerAssignment->execute();

    echo json_encode(['success' => true, 'message' => 'Course and all related timeslots unassigned successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>
