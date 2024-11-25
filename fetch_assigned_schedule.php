<?php
include("connect.php");

$day = $_GET['day'];
$timeSlot = $_GET['timeSlot'];
$sectionId = $_GET['sectionId'];

$sql = "SELECT 
            Assign_Schedule.Course_Section_ID, 
            Course.Course_ID, 
            Lecturer_Assignment.Lect_ID 
        FROM Assign_Schedule
        INNER JOIN Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
        INNER JOIN Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
        INNER JOIN Course ON Course.Course_ID = Course_Section.Course_ID
        LEFT JOIN Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
        WHERE Schedule.Day = ? 
          AND Schedule.Time_Slot = ? 
          AND Assign_Schedule.Section_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $day, $timeSlot, $sectionId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "status" => "success",
        "data" => $row
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No assignment found."
    ]);
}
$stmt->close();
$conn->close();
?>
