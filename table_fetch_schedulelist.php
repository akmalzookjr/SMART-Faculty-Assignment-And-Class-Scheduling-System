<?php
include("connect.php");

$sectionId = 13; // Update this as needed

$sql = "SELECT Schedule.Day, Schedule.Time_Slot, Course.Course_Code, Course.Course_Name, Lecturer.Lect_Name 
        FROM Assign_Schedule
        INNER JOIN Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
        INNER JOIN Course ON Course_Section.Course_ID = Course.Course_ID
        LEFT JOIN Lecturer_Assignment ON Assign_Schedule.Assign_Sche_ID = Lecturer_Assignment.Assign_Sche_ID
        LEFT JOIN Lecturer ON Lecturer_Assignment.Lect_ID = Lecturer.Lect_ID
        INNER JOIN Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
        WHERE Assign_Schedule.Section_ID = '$sectionId'";

$result = mysqli_query($conn, $sql);
$schedule = [];

while ($row = mysqli_fetch_assoc($result)) {
    $day = $row['Day'];
    $timeSlot = $row['Time_Slot'];
    $courseName = $row['Course_Code'] . ' - ' . $row['Course_Name'];
    $lecturerName = $row['Lect_Name'];
    
    $schedule[$day][$timeSlot] = $courseName . ' - ' . $lecturerName;
}

echo json_encode(['schedule' => $schedule]);

mysqli_close($conn);
?>
