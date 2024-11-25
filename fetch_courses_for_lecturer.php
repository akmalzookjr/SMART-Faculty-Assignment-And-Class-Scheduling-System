<?php
include("connect.php");

if (!$conn) {
    die('Connection error: ' . mysqli_connect_error());
}

$levelID = $_GET['level_id'] ?? '';
$semesterID = $_GET['semester_id'] ?? '';
$sectionID = $_GET['section_id'] ?? '';

if ($levelID && $semesterID && $sectionID) {
    $stmt = $conn->prepare("
SELECT 
    Course.Course_Name,
    Course.Course_Code,
    Schedule.Day,
    Schedule.Time_Slot,
    Assign_Schedule.Assign_Sche_ID,
    Section.Section_Number,
    Semester.Sem_Number,
    Level.Level_Name,
    Lecturer.Lect_Name,  -- Lecturer's name
    Lecturer_Assignment.Lect_ID as Lect_ID
FROM 
    Schedule
INNER JOIN 
    Assign_Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
INNER JOIN 
    Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
INNER JOIN 
    Course ON Course.Course_ID = Course_Section.Course_ID
LEFT JOIN 
    Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
LEFT JOIN 
    Lecturer ON Lecturer.Lect_ID = Lecturer_Assignment.Lect_ID
LEFT JOIN 
    Section ON Assign_Schedule.Section_ID = Section.Section_ID
LEFT JOIN 
    Semester ON Section.Sem_ID = Semester.Sem_ID
LEFT JOIN 
    Level ON Semester.Level_ID = Level.Level_ID
WHERE 
    Level.Level_ID = ? AND
    Semester.Sem_ID = ? AND
    Section.Section_ID = ?
ORDER BY 
    FIELD(Schedule.Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'), Schedule.Time_Slot;
");
    $stmt->bind_param("iii", $levelID, $semesterID, $sectionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($courses);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>
