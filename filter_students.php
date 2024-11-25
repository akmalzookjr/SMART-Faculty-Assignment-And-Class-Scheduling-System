<?php
include("connect.php");

if (!$conn) {
    echo json_encode(["error" => "Database connection error"]);
    exit;
}

$level_id = $_GET['level_id'] ?? '';
$semester_id = $_GET['semester_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$repeated_course = $_GET['repeated_course'] ?? 0;

$sql = "SELECT Student.Stud_ID, Student.Stud_Name, 
        Section.Section_Number, Semester.Sem_Number, 
        Level.Level_Name, COUNT(Course_Student.Course_ID) AS Total_Course
        FROM Student 
        INNER JOIN Section ON Student.Section_ID = Section.Section_ID 
        INNER JOIN Semester ON Section.Sem_ID = Semester.Sem_ID 
        INNER JOIN Level ON Semester.Level_ID = Level.Level_ID 
        LEFT JOIN Course_Student ON Course_Student.Stud_ID = Student.Stud_ID 
        LEFT JOIN Course ON Course.Course_ID = Course_Student.Course_ID
        WHERE 1=1";

if ($level_id) {
    $sql .= " AND Level.Level_ID = '$level_id'";
}
if ($semester_id) {
    $sql .= " AND Semester.Sem_ID = '$semester_id'";
}
if ($section_id) {
    $sql .= " AND Section.Section_ID = '$section_id'";
}

$sql .= " GROUP BY Student.Stud_ID, Student.Stud_Name, Section.Section_Number, Semester.Sem_Number, Level.Level_Name";

// Add HAVING clause if repeated_course filter is enabled
if ($repeated_course == 1) {
    $sql .= " HAVING Total_Course > 0";
}

$sql .= " ORDER BY Student.Section_ID";

$result = mysqli_query($conn, $sql);

$students = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

echo json_encode($students);
mysqli_close($conn);
?>
