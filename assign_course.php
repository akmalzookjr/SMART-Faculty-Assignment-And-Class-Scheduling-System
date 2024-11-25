<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignScheId = $_POST['assignScheId'];
    $lectId = $_POST['lectId'];

    if (!$assignScheId || !$lectId) {
        echo json_encode(['success' => false, 'message' => 'Missing Assign_Sche_ID or Lecturer ID']);
        exit;
    }

    // Fetch the day and course section for the given Assign_Sche_ID
    $query = "
        SELECT Schedule.Day, Assign_Schedule.Course_Section_ID
        FROM Assign_Schedule
        INNER JOIN Schedule ON Assign_Schedule.Sche_ID = Schedule.Sche_ID
        WHERE Assign_Schedule.Assign_Sche_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $assignScheId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Assign_Sche_ID']);
        exit;
    }

    $row = $result->fetch_assoc();
    $day = $row['Day'];
    $courseSectionId = $row['Course_Section_ID'];

    // Fetch up to 2 timeslots on the same day for the course
    $queryTimeslots = "
        SELECT Assign_Schedule.Assign_Sche_ID
        FROM Assign_Schedule
        INNER JOIN Schedule ON Assign_Schedule.Sche_ID = Schedule.Sche_ID
        WHERE Assign_Schedule.Course_Section_ID = ? AND Schedule.Day = ?
        ORDER BY Schedule.Time_Slot ASC
        LIMIT 2";
    $stmtTimeslots = $conn->prepare($queryTimeslots);
    $stmtTimeslots->bind_param('is', $courseSectionId, $day);
    $stmtTimeslots->execute();
    $resultTimeslots = $stmtTimeslots->get_result();

    $timeslotIds = [];
    while ($row = $resultTimeslots->fetch_assoc()) {
        $timeslotIds[] = $row['Assign_Sche_ID'];
    }

    if (empty($timeslotIds)) {
        echo json_encode(['success' => false, 'message' => 'No timeslots found for this course on the same day']);
        exit;
    }

    // Assign the timeslots to the lecturer
    $assignQuery = "
        INSERT INTO Lecturer_Assignment (Lect_ID, Assign_Sche_ID)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE Lect_ID = VALUES(Lect_ID)";
    $stmtAssign = $conn->prepare($assignQuery);

    $success = true;
    foreach ($timeslotIds as $id) {
        $stmtAssign->bind_param('ii', $lectId, $id);
        if (!$stmtAssign->execute()) {
            $success = false;
        }
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Timeslots assigned successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to assign timeslots']);
    }

    $stmt->close();
    $stmtTimeslots->close();
    $stmtAssign->close();
    $conn->close();
}
?>
