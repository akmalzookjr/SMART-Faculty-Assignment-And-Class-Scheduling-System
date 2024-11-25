<?php
include 'connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$day = $data['day'] ?? null;
$timeSlot = $data['timeSlot'] ?? null;

$response = ['success' => false];

if ($day && $timeSlot) {
    $query = "SELECT Sche_ID FROM schedule WHERE Day = ? AND Time_Slot = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $day, $timeSlot);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $response['success'] = true;
            $response['scheId'] = $row['Sche_ID'];
        } else {
            $response['message'] = 'Schedule ID not found.';
        }
    } else {
        $response['message'] = 'Database error: ' . $stmt->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid input: missing day or timeSlot.';
}

echo json_encode($response);
$conn->close();
?>
