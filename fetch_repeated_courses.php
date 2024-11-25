<?php
include 'connect.php';

header('Content-Type: application/json');

$studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

$response = ['success' => false, 'courses' => []];

if ($studentId > 0) {
    // Query to fetch repeated courses for the student
    $query = "
    SELECT 
        cs.Course_Stud_ID, 
        cs.Course_ID,
        c.Course_Name, 
        c.Course_Code, 
        cs.Assignment_ID,
        CASE 
            WHEN cs.Assignment_ID IS NULL THEN 'Unassigned'
            ELSE 'Assigned'
        END AS Assignment_Status
    FROM course_student cs
    JOIN course c ON cs.Course_ID = c.Course_ID
    WHERE cs.Stud_ID = ?
    ORDER BY Assignment_Status ASC, c.Course_Name;
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $courses = $result->fetch_all(MYSQLI_ASSOC);

        // Fetch available timeslots for each unassigned course
        foreach ($courses as &$course) {
            if ($course['Assignment_ID'] === null) {
                $assignmentQuery = "
                SELECT 
                    la.Assignment_ID,
                    CONCAT(s.Day, ' ', s.Time_Slot) AS AvailableTimeslot,
                    lv.Level_Name,
                    sm.Sem_Number AS Semester,
                    sc.Section_Number AS Section
                FROM lecturer_assignment la
                JOIN assign_schedule asch ON la.Assign_Sche_ID = asch.Assign_Sche_ID
                JOIN schedule s ON asch.Sche_ID = s.Sche_ID
                JOIN section sc ON asch.Section_ID = sc.Section_ID
                JOIN semester sm ON sc.Sem_ID = sm.Sem_ID
                JOIN level lv ON sm.Level_ID = lv.Level_ID
                WHERE asch.Course_Section_ID IN (
                    SELECT cs_sec.Course_Section_ID
                    FROM course_section cs_sec
                    WHERE cs_sec.Course_ID = ?
                )
                ";
                $assignmentStmt = $conn->prepare($assignmentQuery);
                $assignmentStmt->bind_param('i', $course['Course_ID']);
                $assignmentStmt->execute();
                $assignmentResult = $assignmentStmt->get_result();
                $course['AvailableAssignments'] = $assignmentResult->fetch_all(MYSQLI_ASSOC);
            } else {
                $course['AvailableAssignments'] = [];
            }
        }

        $response['success'] = true;
        $response['courses'] = $courses;
    } else {
        $response['message'] = 'Error fetching courses.';
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid student ID.';
}

echo json_encode($response);
$conn->close();
