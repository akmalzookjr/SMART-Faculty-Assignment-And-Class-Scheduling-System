<?php
include 'connect.php';

$studentId = $_GET['student_id']; // Get the student ID from the request

$response = ['success' => false, 'timeslots' => []];

if ($studentId) {
    // Query to fetch courses, timeslots, and schedule details for the student's repeated courses
    $query = "
        SELECT 
            c.Course_ID, 
            c.Course_Name, 
            c.Course_Code, 
            s.Time_Slot, 
            s.Day
        FROM course_student cs
        JOIN course c ON cs.Course_ID = c.Course_ID
        JOIN lecturer_assignment la ON cs.Assignment_ID = la.Assignment_ID
        JOIN assign_schedule asch ON la.Assign_Sche_ID = asch.Assign_Sche_ID
        JOIN schedule s ON asch.Sche_ID = s.Sche_ID
        WHERE cs.Stud_ID = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $timeslots = [];
    while ($row = $result->fetch_assoc()) {
        $courseId = $row['Course_ID'];
        if (!isset($timeslots[$courseId])) {
            $timeslots[$courseId] = [
                'Course_Name' => $row['Course_Name'],
                'Course_Code' => $row['Course_Code'],
                'Timeslots' => []
            ];
        }
        $timeslots[$courseId]['Timeslots'][] = [
            'Time_Slot' => $row['Time_Slot'],
            'Day' => $row['Day']
        ];
    }

    $response['success'] = true;
    $response['timeslots'] = $timeslots;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
