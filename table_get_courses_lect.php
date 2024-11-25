<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lecturerId = $_GET['lecturer_id'];

    if (!$lecturerId) {
        echo json_encode(['status' => 'error', 'message' => 'Lecturer ID is required']);
        exit;
    }

    // Query to get assigned courses for the lecturer
    $query = "
        SELECT 
            Course.Course_Name, 
            Course.Course_CH, 
            Assign_Schedule.Assign_Sche_ID,
            Schedule.Day,
            Schedule.Time_Slot,
            Section.Section_Number,
            Semester.Sem_Number,
            Level.Level_Name
        FROM 
            Lecturer_Assignment
        INNER JOIN 
            Assign_Schedule ON Lecturer_Assignment.Assign_Sche_ID = Assign_Schedule.Assign_Sche_ID
        INNER JOIN 
            Course_Section ON Assign_Schedule.Course_Section_ID = Course_Section.Course_Section_ID
        INNER JOIN 
            Course ON Course_Section.Course_ID = Course.Course_ID
        INNER JOIN 
            Schedule ON Assign_Schedule.Sche_ID = Schedule.Sche_ID
        INNER JOIN 
            Section ON Assign_Schedule.Section_ID = Section.Section_ID
        INNER JOIN 
            Semester ON Section.Sem_ID = Semester.Sem_ID
        INNER JOIN 
            Level ON Semester.Level_ID = Level.Level_ID
        WHERE 
            Lecturer_Assignment.Lect_ID = ?
        ORDER BY 
            Schedule.Day, Schedule.Time_Slot";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $lecturerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }

    echo json_encode($courses);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
