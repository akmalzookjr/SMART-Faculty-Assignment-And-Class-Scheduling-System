<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $assignScheIds = $data['assignScheIds'];
    $lecturerId = $data['lecturerID'];

    if (empty($assignScheIds) || !$lecturerId) {
        echo json_encode(['success' => false, 'message' => 'Missing Assign_Sche_IDs or Lecturer ID']);
        exit;
    }

    // Prepare the statement for assigning multiple timeslots
    $assignQuery = "
        INSERT INTO Lecturer_Assignment (Lect_ID, Assign_Sche_ID)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE Lect_ID = VALUES(Lect_ID)";
    $stmtAssign = $conn->prepare($assignQuery);

    $success = true;
    foreach ($assignScheIds as $assignScheId) {
        $stmtAssign->bind_param('ii', $lecturerId, $assignScheId);
        if (!$stmtAssign->execute()) {
            $success = false;
        }
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Timeslots assigned successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to assign timeslots']);
    }

    $stmtAssign->close();
    $conn->close();
}
?>
