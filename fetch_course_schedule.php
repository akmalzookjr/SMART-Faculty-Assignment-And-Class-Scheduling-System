<?php
include("connect.php");

if (!$conn) {
    echo json_encode(['error' => 'Connection error: ' . mysqli_connect_error()]);
    exit;
}

$assignScheId = isset($_GET['assign_sche_id']) ? $_GET['assign_sche_id'] : '';
$courseName = isset($_GET['course_name']) ? $_GET['course_name'] : '';

if (!$assignScheId || !$courseName) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// Query to fetch the course schedule by Assign_Sche_ID and Course Name
$query = "SELECT Schedule.Day, Schedule.Time_Slot, Section.Section_Number, Semester.Sem_ID, Semester.Sem_Number, Course_Section.Course_Section_ID, Course_Section.Course_Section, Course.Course_Code
          FROM Assign_Schedule
          JOIN Schedule ON Assign_Schedule.Sche_ID = Schedule.Sche_ID
          JOIN Section ON Assign_Schedule.Section_ID = Section.Section_ID
          JOIN Semester ON Section.Sem_ID = Semester.Sem_ID
          JOIN Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
          JOIN Course ON Course.Course_ID = Course_Section.Course_ID
          WHERE Assign_Schedule.Assign_Sche_ID = ? AND Course.Course_Name = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $assignScheId, $courseName);
$stmt->execute();
$result = $stmt->get_result();

$scheduleData = [];
while ($row = $result->fetch_assoc()) {
    $scheduleData[] = [
        'Semester' => $row['Sem_Number'],
        'Section_Number' => $row['Section_Number'],
        'Day' => $row['Day'],
        'Time_Slot' => $row['Time_Slot'],
        'Course_Code' => $row['Course_Code'],
        'Course_Section' => $row['Course_Section']
    ];
}

echo json_encode($scheduleData);

$stmt->close();
$conn->close();
?>
