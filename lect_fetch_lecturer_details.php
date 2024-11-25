<?php
include("connect.php"); // Ensure your DB connection settings are correct

$lectId = $_GET['lect_id'] ?? ''; // Sanitize this to prevent SQL injection

// Query to fetch lecturer details
$sql = "SELECT lecturer.Lect_ID, lecturer.Lect_Name, lecturer.Lect_CH, 
        IFNULL(COUNT(DISTINCT assign_schedule.Assign_Sche_ID), 0) AS total_taught_CH
        FROM lecturer
        LEFT JOIN lecturer_assignment ON lecturer.Lect_ID = lecturer_assignment.Lect_ID
        LEFT JOIN assign_schedule ON lecturer_assignment.Assign_Sche_ID = assign_schedule.Assign_Sche_ID
        WHERE lecturer.Lect_ID = $lectId
        GROUP BY lecturer.Lect_ID";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

// Optionally, fetch courses taught by the lecturer
$courses_sql = "SELECT course.Course_Name, course.Course_CH FROM course
                JOIN course_section ON course.Course_ID = course_section.Course_ID
                JOIN assign_schedule ON course_section.Course_Section_ID = assign_schedule.Course_Section_ID
                JOIN lecturer_assignment ON assign_schedule.Assign_Sche_ID = lecturer_assignment.Assign_Sche_ID
                WHERE lecturer_assignment.Lect_ID = $lectId";
$courses_result = mysqli_query($conn, $courses_sql);
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

$data['courses'] = $courses; // Append courses to the lecturer's details

echo json_encode($data); // Return data as JSON

mysqli_free_result($result);
mysqli_free_result($courses_result);
mysqli_close($conn);
?>
