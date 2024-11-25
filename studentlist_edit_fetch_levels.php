<?php
include 'connect.php';

$query = "SELECT Level_ID, Level_Name FROM level";
$result = mysqli_query($conn, $query);

if ($result) {
    $levels = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($levels);
} else {
    echo json_encode([]);
}
?>
