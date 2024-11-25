<?php
include 'connect.php';

if (isset($_GET['student_id'])) {
    $studentId = $_GET['student_id'];

    // Fetch student details
    $query = "SELECT 
                Student.Stud_ID, 
                Student.Stud_Name, 
                Section.Section_Number, 
                Section.Section_ID, 
                Semester.Sem_Number, 
                Semester.Sem_ID, 
                Level.Level_Name, 
                Level.Level_ID
              FROM student
              INNER JOIN section ON Student.Section_ID = Section.Section_ID
              INNER JOIN semester ON Section.Sem_ID = Semester.Sem_ID
              INNER JOIN level ON Semester.Level_ID = Level.Level_ID
              WHERE Student.Stud_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    // Fetch enrolled courses for the student
    // Fetch enrolled courses for the student with assignment status
// Fetch enrolled courses for the student with detailed assignment info
$coursesQuery = "SELECT 
                    course.Course_ID, 
                    course.Course_Name, 
                    course.Course_Code,
                    IF(course_student.Assignment_ID IS NOT NULL, 'Assigned', 'Not Assigned') AS Assignment_Status,
                    level.Level_Name,
                    semester.Sem_Number,
                    section.Section_Number,
                    schedule.Day,
                    schedule.Time_Slot
                 FROM course_student
                 INNER JOIN course ON course_student.Course_ID = course.Course_ID
                 LEFT JOIN lecturer_assignment ON course_student.Assignment_ID = lecturer_assignment.Assignment_ID
                 LEFT JOIN assign_schedule ON lecturer_assignment.Assign_Sche_ID = assign_schedule.Assign_Sche_ID
                 LEFT JOIN schedule ON assign_schedule.Sche_ID = schedule.Sche_ID
                 LEFT JOIN section ON assign_schedule.Section_ID = section.Section_ID
                 LEFT JOIN semester ON section.Sem_ID = semester.Sem_ID
                 LEFT JOIN level ON semester.Level_ID = level.Level_ID
                 WHERE course_student.Stud_ID = ?";
$stmt = $conn->prepare($coursesQuery);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$coursesResult = $stmt->get_result();
$courses = $coursesResult->fetch_all(MYSQLI_ASSOC);

// Send response as JSON
echo json_encode(['student' => $student, 'courses' => $courses]);


} else {
    echo json_encode(['error' => 'Invalid student ID']);
}

$conn->close();
?>
