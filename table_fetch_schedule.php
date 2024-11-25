<?php
include("connect.php");

$section_id = $_GET['section_id'];
$query = 'SELECT Assign_Schedule.Assign_Sche_ID, Assign_Schedule.Sche_ID, Assign_Schedule.Course_Section_ID, 
Schedule.Sche_ID, Schedule.Time_Slot, Schedule.Day, 
Course_Section.Course_Section_ID, Course_Section.Course_ID, Course_Section.Course_Section, 
Course.Course_ID, Course.Course_Name, Course.Course_Code, Course.Course_CH, 
Lecturer_Assignment.Assignment_ID, Lecturer_Assignment.Lect_ID, Lecturer_Assignment.Assign_Sche_ID, 
Section.Section_ID, Section.Section_Number, Section.Sem_ID, 
Semester.Sem_ID, Semester.Sem_Number, Semester.Level_ID, 
Level.Level_ID, Level.Level_Name, 
Lecturer.Lect_Name
FROM Assign_Schedule
INNER JOIN Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
INNER JOIN Course_Section ON Course_Section.Course_Section_ID = Assign_Schedule.Course_Section_ID
INNER JOIN Course ON Course.Course_ID = Course_Section.Course_ID
LEFT JOIN Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
LEFT JOIN Lecturer ON Lecturer.Lect_ID = Lecturer_Assignment.Lect_ID
INNER JOIN Section ON Assign_Schedule.Section_ID = Section.Section_ID
INNER JOIN Semester ON Section.Sem_ID = Semester.Sem_ID 
INNER JOIN Level ON Semester.Level_ID = Level.Level_ID
WHERE Assign_Schedule.Section_ID = ?';

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $section_id);
$stmt->execute();
$result = $stmt->get_result();

$scheduleData = [
    'Monday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Tuesday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Wednesday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Thursday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
    'Friday' => ['8-9' => '', '9-10' => '', '10-11' => '', '11-12' => '', '12-1' => '', '1-2' => '', '2-3' => '', '3-4' => '', '4-5' => '', '5-6' => '', '6-7' => ''],
];

while ($row = $result->fetch_assoc()) {
    $day = $row['Day'];
    $timeSlot = $row['Time_Slot'];
    $courseCode = $row['Course_Code'];
    $courseSection = sprintf('%02d', $row['Course_Section']);
    $lecturerName = $row['Lect_Name'] ?: 'No lecturer'; // Show "No lecturer" if no lecturer is assigned
    
    if (isset($scheduleData[$day][$timeSlot])) {
        $scheduleData[$day][$timeSlot] = htmlspecialchars($courseCode) . '_' . htmlspecialchars($courseSection) . ' - ' . htmlspecialchars($lecturerName);
    }
}

echo json_encode(['schedule' => $scheduleData]);
?>