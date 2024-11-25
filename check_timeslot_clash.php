<?php
include("connect.php");

// Get lecturer ID and assignment schedule ID
$lecturerId = isset($_GET['lecturer_id']) ? $_GET['lecturer_id'] : '';
$assignScheId = isset($_GET['assign_sche_id']) ? $_GET['assign_sche_id'] : '';

// If the lecturer ID or assignment schedule ID is not provided, return an error
if (empty($lecturerId) || empty($assignScheId)) {
    echo json_encode(['error' => 'Lecturer ID or Assign_Sche_ID is missing']);
    exit;
}

// Check for time slot conflicts
$query = "
    SELECT S.Day, S.Time_Slot
    FROM Schedule S
    INNER JOIN Assign_Schedule AS AS1 ON AS1.Sche_ID = S.Sche_ID
    LEFT JOIN Lecturer_Assignment LA ON LA.Assign_Sche_ID = AS1.Assign_Sche_ID
    WHERE LA.Lect_ID = ? AND AS1.Assign_Sche_ID != ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $lecturerId, $assignScheId);
$stmt->execute();
$result = $stmt->get_result();

// Array to store existing time slots for the lecturer
$existingSlots = [];
while ($row = $result->fetch_assoc()) {
    $existingSlots[] = $row['Day'] . ' ' . $row['Time_Slot'];
}

// Now we need to check if the proposed time slots for the course conflict with existing ones
$proposedSlots = []; // Fetch proposed slots (you can adapt this based on your input structure)

// Assuming you fetch proposed time slots from the database or frontend form
// Example for testing
$proposedSlots[] = 'Monday 9-10';
$proposedSlots[] = 'Monday 10-11';

// Check for clashes
foreach ($proposedSlots as $proposedSlot) {
    if (in_array($proposedSlot, $existingSlots)) {
        // If there's a conflict, return the clash error
        echo json_encode(['clash' => true]);
        exit;
    }
}

// If no clashes, return no clash
echo json_encode(['clash' => false]);

$stmt->close();
$conn->close();
?>
