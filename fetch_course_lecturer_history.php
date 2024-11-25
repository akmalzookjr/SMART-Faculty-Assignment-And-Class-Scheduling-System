<?php
include("connect.php");

header('Content-Type: application/json');

$day = $_GET['day'] ?? null;
$timeSlotIndex = $_GET['timeSlotIndex'] ?? null;
$section = $_GET['section'] ?? null;

if (!$day || $timeSlotIndex === null || !$section) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters.']);
    exit;
}

// Map the time slot index to the actual time slot
$timeSlotMapping = ['8-9', '9-10', '10-11', '11-12', '12-1', '1-2', '2-3', '3-4', '4-5', '5-6', '6-7'];
$timeSlot = $timeSlotMapping[$timeSlotIndex];

// Fetch schedule and course details
$query = "
    SELECT 
        Assign_Schedule.Course_Section_ID, 
        Course_Section.Course_ID, 
        Lecturer_Assignment.Lect_ID 
    FROM Assign_Schedule
    INNER JOIN Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
    LEFT JOIN Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
    INNER JOIN Course_Section ON Course_Section.Course_Section_ID = Assign_Schedule.Course_Section_ID
    WHERE Schedule.Day = ? AND Schedule.Time_Slot = ? AND Assign_Schedule.Section_ID = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $day, $timeSlot, $section);
$stmt->execute();
$stmt->bind_result($courseSectionId, $courseId, $lecturerId);
$stmt->fetch();

if ($courseId) {
    echo json_encode([
        'status' => 'success',
        'courseId' => $courseId,
        'courseSectionId' => $courseSectionId,
        'lecturerId' => $lecturerId,
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found for the selected slot.']);
}

$stmt->close();
$conn->close();
