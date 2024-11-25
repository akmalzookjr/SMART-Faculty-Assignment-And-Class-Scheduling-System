<?php
ob_start();  // Start output buffering to prevent output before sending the JSON response.
error_reporting(0);  // Disable PHP error reporting for a clean response.
header('Content-Type: application/json');  // Ensure the response is sent as JSON.

include("connect.php");  // Assuming your database connection is set here.

$data = json_decode(file_get_contents("php://input"));

$lecturerId = $data->lecturerId;
$timeslot = $data->timeslot;
$day = $data->day;

// Prepare the SQL query to check for conflicts
$sql_check_conflict = "
    SELECT COUNT(*) AS conflict_count
    FROM lecturer_assignment
    JOIN assign_schedule ON lecturer_assignment.Assign_Sche_ID = assign_schedule.Assign_Sche_ID
    JOIN schedule ON assign_schedule.Sche_ID = schedule.Sche_ID
    WHERE lecturer_assignment.Lect_ID = ? 
    AND schedule.Day = ? 
    AND schedule.Time_Slot = ? 
    AND assign_schedule.Assign_Sche_ID != ?";  // Exclude the current assignment

// Prepare the statement to avoid SQL injection
$stmt = $conn->prepare($sql_check_conflict);
$stmt->bind_param("ssss", $lecturerId, $day, $timeslot, $timeslot); // Bind parameters
$stmt->execute();
$stmt->bind_result($conflict_count);
$stmt->fetch();
$stmt->close();

// Return the result as a JSON response
echo json_encode(['conflict' => $conflict_count > 0]);

// End of the script
exit;
?>
