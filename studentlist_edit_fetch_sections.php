<?php
include 'connect.php';

if (isset($_GET['semester_id'])) {
    $semester_id = intval($_GET['semester_id']);
    $query = "SELECT Section_ID, Section_Number FROM section WHERE Sem_ID = $semester_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $sections = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($sections);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(["error" => "Missing semester_id"]);
}
?>
