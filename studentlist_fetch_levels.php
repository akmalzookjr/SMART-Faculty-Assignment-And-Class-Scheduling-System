<?php
include 'connect.php';

$query = "SELECT Level_ID, Level_Name FROM level";
$result = mysqli_query($conn, $query);

$levels = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $levels[] = $row;
    }
}

echo json_encode($levels);
?>
