<?php
include("connect.php");

$sql = "SELECT * FROM level";
$result = mysqli_query($conn, $sql);
$levels = [];

while ($row = mysqli_fetch_assoc($result)) {
    $levels[] = $row;
}

echo json_encode($levels);
?>
