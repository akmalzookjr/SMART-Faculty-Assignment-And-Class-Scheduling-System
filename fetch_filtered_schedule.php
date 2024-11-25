<?php
include 'connect.php';  // Make sure your database connection is correctly set up

$levelId = $_GET['level_id'] ?? '';  // Safely retrieve the level ID

if (!empty($levelId)) {
    $query = "SELECT * FROM Schedule WHERE Level_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $levelId);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedules = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($schedules);
} else {
    echo json_encode([]);
}
?>
