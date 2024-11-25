<?php
include("connect.php");

if (!$conn) {
    echo json_encode(['error' => 'Connection error: ' . mysqli_connect_error()]);
    exit;
}

$studentId = isset($_GET['student_id']) ? $_GET['student_id'] : '';

if (!$studentId) {
    echo json_encode(['error' => 'No student ID provided']);
    exit;
}

// Step 1: Fetch courses along with their assigned schedules for a specific student
$query = "SELECT Student.Stud_ID, Student.Stud_Name, Student.Section_ID, 
          Section.Section_Number, Semester.Sem_Number, Level.Level_Name,
          GROUP_CONCAT(DISTINCT CONCAT(Course.Course_Name, '|', Course.Course_CH, '|', IFNULL(Course_Student.Assign_Sche_ID, '0')) ORDER BY Course.Course_Name SEPARATOR ', ') AS Courses,
          GROUP_CONCAT(Course.Course_Name) AS RepeatedCourses -- Fetch repeated courses
          FROM Student
          JOIN Section ON Student.Section_ID = Section.Section_ID
          JOIN Semester ON Section.Sem_ID = Semester.Sem_ID
          JOIN Level ON Semester.Level_ID = Level.Level_ID
          JOIN Course_Student ON Course_Student.Stud_ID = Student.Stud_ID
          JOIN Course ON Course_Student.Course_ID = Course.Course_ID
          WHERE Student.Stud_ID = ?
          GROUP BY Student.Stud_ID";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

$repeatedCourses = '';
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $repeatedCourses = explode(',', $data['RepeatedCourses']); // Split repeated courses
}

// Step 2: Modify the schedule query to fetch only the timeslots for repeated courses
$scheduleQuery = "SELECT 
        Assign_Schedule.Assign_Sche_ID, 
        Course.Course_ID,
        Course.Course_Name,
        Course_Section.Course_Section_ID,
        Course_Section.Course_Section,
        Schedule.Day, 
        Schedule.Time_Slot,
        Section.Section_Number,
        Semester.Sem_Number
    FROM Assign_Schedule
    JOIN Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
    JOIN Section ON Assign_Schedule.Section_ID = Section.Section_ID
    JOIN Semester ON Section.Sem_ID = Semester.Sem_ID
    JOIN Course_Section ON Course_Section.Course_Section_ID = Assign_Schedule.Course_Section_ID
    JOIN Course ON Course.Course_ID = Course_Section.Course_ID
    WHERE Course.Course_Name IN ('" . implode("','", $repeatedCourses) . "') -- Filter by repeated courses
    ORDER BY Schedule.Day, Schedule.Time_Slot";

$scheduleStmt = $conn->prepare($scheduleQuery);
$scheduleStmt->execute();
$scheduleResult = $scheduleStmt->get_result();

$timeslots = [];
while ($row = $scheduleResult->fetch_assoc()) {
    $timeslots[] = [
        'Course' => $row['Course_Name'],
        'Course_Section' => $row['Course_Section'],
        'Assign_Sche_ID' => $row['Assign_Sche_ID'],
        'Day' => $row['Day'],
        'Time_Slot' => $row['Time_Slot'],
        'Section_Number' => $row['Section_Number'],
        'Sem_Number' => $row['Sem_Number'],
        'Course_ID' => $row['Course_ID']
    ];
}

if (!empty($data)) {
    $data['Timeslots'] = $timeslots;  // Append timeslots to the output
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No data found for student ID ' . $studentId]);
}

$stmt->close();
$scheduleStmt->close();
$conn->close();
?>
