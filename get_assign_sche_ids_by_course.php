<?php
include("connect.php");

$data = json_decode(file_get_contents('php://input'), true);
$lecturerId = $data['lecturerId'];
$courseName = $data['courseName'];

// Check for missing data
if (empty($lecturerId) || empty($courseName)) {
    echo json_encode(['error' => 'Invalid data provided']);
    exit;
}

// SQL to find all Assign_Sche_IDs for this lecturer and course
$sql = "
    SELECT Assign_Sche_ID
    FROM lecturer_assignment
    INNER JOIN Assign_Schedule ON Assign_Schedule.Assign_Sche_ID = lecturer_assignment.Assign_Sche_ID
    INNER JOIN Course_Section ON Course_Section.Course_Section_ID = Assign_Schedule.Course_Section_ID
    INNER JOIN Course ON Course.Course_ID = Course_Section.Course_ID
    WHERE lecturer_assignment.Lect_ID = $lecturerId AND Course.Course_Name = '$courseName'
";

$result = mysqli_query($conn, $sql);

if ($result) {
    $assignScheIds = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $assignScheIds[] = $row['Assign_Sche_ID'];
    }
    echo json_encode(['success' => true, 'assignScheIds' => $assignScheIds]);
} else {
    error_log("MySQL error: " . mysqli_error($conn));
    echo json_encode(['error' => 'Failed to fetch Assign_Sche_IDs']);
}

mysqli_close($conn);
?>
