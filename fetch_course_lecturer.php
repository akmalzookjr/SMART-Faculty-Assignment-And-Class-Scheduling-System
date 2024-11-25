<?php
include("connect.php");

$day = $_GET['day'];
$timeSlot = $_GET['timeSlot'];

// Query to fetch course and lecturer based on the day and time slot
$sql = "SELECT Course.Course_ID AS courseId, Lecturer.Lect_ID AS lecturerId
        FROM Assign_Schedule
        INNER JOIN Schedule ON Assign_Schedule.Sche_ID = Schedule.Sche_ID
        INNER JOIN Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
        INNER JOIN Course ON Course_Section.Course_ID = Course.Course_ID
        LEFT JOIN Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
        LEFT JOIN Lecturer ON Lecturer.Lect_ID = Lecturer_Assignment.Lect_ID
        WHERE Schedule.Day = ? AND Schedule.Time_Slot = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $day, $timeSlot);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(["courseId" => "", "lecturerId" => ""]);
}

$stmt->close();
$conn->close();
?>
