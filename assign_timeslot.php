<?php
include 'connect.php'; // Ensure this connects to your database
header('Content-Type: application/json');

// Start output buffering to prevent any accidental output before JSON response
ob_start();

// Decode the JSON payload sent from the frontend
$data = json_decode(file_get_contents('php://input'), true);

$courseStudId = isset($data['courseStudId']) ? intval($data['courseStudId']) : 0;
$assignmentId = isset($data['assignmentId']) ? intval($data['assignmentId']) : 0;
$action = isset($data['action']) ? $data['action'] : ''; // Action can be 'assign' or 'unassign'

if ($courseStudId > 0 && $assignmentId > 0 && in_array($action, ['assign', 'unassign'])) {
    // Get the section of the student
    $query = "SELECT s.Section_ID 
              FROM course_student cs
              JOIN student s ON cs.Stud_ID = s.Stud_ID
              WHERE cs.Course_Stud_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $courseStudId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found.']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->bind_result($studentSectionId);
    $stmt->fetch();
    $stmt->close();

    if ($action === 'assign') {
        // Get the schedule for the assignment from lecturer_assignment
        $query = "SELECT la.Assign_Sche_ID, asg.Sche_ID 
                  FROM lecturer_assignment la
                  JOIN assign_schedule asg ON la.Assign_Sche_ID = asg.Assign_Sche_ID
                  WHERE la.Assignment_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $assignmentId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Assignment not found.']);
            $stmt->close();
            $conn->close();
            exit();
        }

        $stmt->bind_result($assignScheId, $newCourseScheId);
        $stmt->fetch();
        $stmt->close();

        // Check if the same schedule ID is already assigned to another course in the same section
        $query = "SELECT c.Course_Name, s.Time_Slot, s.Day
                  FROM assign_schedule asg
                  JOIN course_student cs ON asg.Course_Section_ID = cs.Course_ID
                  JOIN schedule s ON asg.Sche_ID = s.Sche_ID
                  JOIN course c ON cs.Course_ID = c.Course_ID
                  WHERE asg.Section_ID = ? AND asg.Sche_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $studentSectionId, $newCourseScheId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Fetch the conflicting course details
            $stmt->bind_result($courseName, $timeSlot, $day);
            $stmt->fetch();

            // If there's a clash, return the course name and timeslot
            echo json_encode([
                'success' => false,
                'message' => 'There is already a course in that timeslot.',
                'course_name' => $courseName,
                'timeslot' => $timeSlot,
                'day' => $day
            ]);
        } else {
            // No clash, proceed to assign the timeslot
            $updateQuery = "UPDATE course_student SET Assignment_ID = ? WHERE Course_Stud_ID = ?";
            $updateStmt = $conn->prepare($updateQuery);

            if ($updateStmt) {
                $updateStmt->bind_param('ii', $assignmentId, $courseStudId);
                if ($updateStmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Assignment_ID assigned successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to execute query.']);
                }
                $updateStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to prepare update query.']);
            }
        }
    } elseif ($action === 'unassign') {
        // Unassign the timeslot by setting Assignment_ID to NULL
        $updateQuery = "UPDATE course_student SET Assignment_ID = NULL WHERE Course_Stud_ID = ?";
        $updateStmt = $conn->prepare($updateQuery);

        if ($updateStmt) {
            $updateStmt->bind_param('i', $courseStudId);
            if ($updateStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Timeslot unassigned successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to unassign the timeslot.']);
            }
            
            $updateStmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare unassign query.']);
        }
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Course_Stud_ID, Assignment_ID, or action.']);
}

$conn->close();

// End the output buffer and send the response
ob_end_flush();
?>
