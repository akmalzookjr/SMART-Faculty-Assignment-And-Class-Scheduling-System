<?php
include("connect.php"); // Your database connection file

// Ensure there is a lecturer_id in the query string
if (isset($_GET['lecturer_id']) && !empty($_GET['lecturer_id'])) {
    $lecturerId = mysqli_real_escape_string($conn, $_GET['lecturer_id']);

    // Query to get the lecturer details
    $query = "SELECT Lecturer.Lect_ID, Lecturer.Lect_Name, Lecturer.Department, Lecturer.Bio,
              Course.Course_Name, Course.Course_CH, Course.Semester
              FROM Lecturer
              LEFT JOIN Lecturer_Course ON Lecturer.Lect_ID = Lecturer_Course.Lect_ID
              LEFT JOIN Course ON Lecturer_Course.Course_ID = Course.Course_ID
              WHERE Lecturer.Lect_ID = '$lecturerId'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
        exit;
    }

    $lecturerDetails = mysqli_fetch_assoc($result);

    // Fetch multiple courses taught by the lecturer if any
    $courses = [];
    if ($lecturerDetails) {
        do {
            $courses[] = [
                'name' => $lecturerDetails['Course_Name'],
                'creditHours' => $lecturerDetails['Course_CH'],
                'semester' => $lecturerDetails['Semester']
            ];
        } while ($lecturerDetails = mysqli_fetch_assoc($result));
    }

    // Remove duplicate entries from courses array (if lecturer teaches multiple courses)
    $courses = array_map("unserialize", array_unique(array_map("serialize", $courses)));

    // Outputting data as JSON
    echo json_encode([
        'name' => $lecturerDetails['Lect_Name'],
        'department' => $lecturerDetails['Department'],
        'bio' => $lecturerDetails['Bio'],
        'courses' => $courses
    ]);
} else {
    echo json_encode(['error' => 'Lecturer ID not provided.']);
}
?>
