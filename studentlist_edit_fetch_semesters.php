<?php
include 'connect.php';

if (isset($_GET['level_id'])) {
    $level_id = intval($_GET['level_id']);
    $query = "SELECT Sem_ID, Sem_Number FROM semester WHERE Level_ID = $level_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $semesters = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($semesters);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(["error" => "Missing level_id"]);
}
?>
