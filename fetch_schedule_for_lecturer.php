<?php
include("connect.php");

if (!$conn) {
    die('Connection error: ' . mysqli_connect_error());
}

$lecturerId = $_GET['lecturer_id'] ?? null;

if ($lecturerId) {
    $sql = "
    SELECT
        Schedule.Day,
        Schedule.Time_Slot,
        Course.Course_Name
    FROM
        Lecturer_Assignment
    INNER JOIN Assign_Schedule ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
    INNER JOIN Schedule ON Assign_Schedule.Sche_ID = Schedule.Sche_ID
    INNER JOIN Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
    INNER JOIN Course ON Course_Section.Course_ID = Course.Course_ID
    WHERE
        Lecturer_Assignment.Lect_ID = ?
    ORDER BY
        Schedule.Day, Schedule.Time_Slot
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedule = [];
    while ($row = $result->fetch_assoc()) {
        $day = $row['Day'];
        $timeSlot = $row['Time_Slot'];
        $courseName = $row['Course_Name'];

        if (!isset($schedule[$day])) {
            $schedule[$day] = [];
        }

        $schedule[$day][$timeSlot] = $courseName;
    }

    echo json_encode(['schedule' => $schedule]);
} else {
    echo json_encode(['error' => 'Lecturer ID is required']);
}

$stmt->close();
$conn->close();
?>
