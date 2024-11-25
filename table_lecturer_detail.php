<?php
include("connect.php");

if (!$conn) {
    echo json_encode(['error' => 'Connection error: ' . mysqli_connect_error()]);
    exit;
}

$lecturerId = isset($_GET['lecturer_id']) ? $_GET['lecturer_id'] : '';

if (!$lecturerId) {
    echo json_encode(['error' => 'No lecturer ID provided']);
    exit;
}

// Fetch courses along with their assigned schedules for a specific lecturer, including Level, Semester, and Section
$query = "SELECT Lecturer.Lect_ID, Lecturer.Lect_Name,
          GROUP_CONCAT(DISTINCT CONCAT(Course.Course_Name, '|', Course.Course_CH, '|', IFNULL(Lecturer_Assignment.Assign_Sche_ID, '0'), '|', Level.Level_Name, '|', Semester.Sem_Number, '|', Section.Section_Number) ORDER BY Course.Course_Name SEPARATOR ', ') AS Courses
          FROM Lecturer
          LEFT JOIN Lecturer_Assignment ON Lecturer.Lect_ID = Lecturer_Assignment.Lect_ID
          LEFT JOIN Assign_Schedule ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
          LEFT JOIN Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
          LEFT JOIN Course ON Course_Section.Course_ID = Course.Course_ID
          LEFT JOIN Section ON Assign_Schedule.Section_ID = Section.Section_ID
          LEFT JOIN Semester ON Section.Sem_ID = Semester.Sem_ID
          LEFT JOIN Level ON Semester.Level_ID = Level.Level_ID
          WHERE Lecturer.Lect_ID = ?
          GROUP BY Lecturer.Lect_ID";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lecturerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    echo json_encode(['error' => 'No data found for lecturer ID ' . $lecturerId]);
    $stmt->close();
    $conn->close();
    exit;
}

// Fetch the timeslots for courses taught by the lecturer
$scheduleQuery = "SELECT 
        Assign_Schedule.Assign_Sche_ID, 
        Course.Course_ID,
        Course.Course_Name,
        Course_Section.Course_Section,
        Section.Section_Number,
        Semester.Sem_Number,
        Level.Level_Name,
        Schedule.Day, 
        Schedule.Time_Slot
    FROM Assign_Schedule
    JOIN Schedule ON Schedule.Sche_ID = Assign_Schedule.Sche_ID
    JOIN Course_Section ON Course_Section.Course_Section_ID = Assign_Schedule.Course_Section_ID
    JOIN Course ON Course.Course_ID = Course_Section.Course_ID
    JOIN Lecturer_Assignment ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
    JOIN Section ON Section.Section_ID = Assign_Schedule.Section_ID
    JOIN Semester ON Semester.Sem_ID = Section.Sem_ID
    JOIN Level ON Level.Level_ID = Semester.Level_ID
    WHERE Lecturer_Assignment.Lect_ID = ?
    ORDER BY Schedule.Day, Schedule.Time_Slot";

$scheduleStmt = $conn->prepare($scheduleQuery);
$scheduleStmt->bind_param("i", $lecturerId);
$scheduleStmt->execute();
$scheduleResult = $scheduleStmt->get_result();

$timeslots = [];
while ($row = $scheduleResult->fetch_assoc()) {
    $timeslots[] = [
        'Course' => $row['Course_Name'],
        'Course_Section' => $row['Course_Section'],
        'Section_Number' => $row['Section_Number'],
        'Semester' => $row['Sem_Number'],
        'Level' => $row['Level_Name'],
        'Day' => $row['Day'],
        'Time_Slot' => $row['Time_Slot'],
        'Assign_Sche_ID' => $row['Assign_Sche_ID']
    ];
}

$data['Timeslots'] = $timeslots;  // Append timeslots to the output
echo json_encode($data);

$stmt->close();
$scheduleStmt->close();
$conn->close();
?>
