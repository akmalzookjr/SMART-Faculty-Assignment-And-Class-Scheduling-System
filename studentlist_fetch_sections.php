<?php
include 'connect.php';

if (isset($_GET['semester_id'])) {
    $semesterId = $_GET['semester_id'];

    $query = "SELECT Section_ID, Section_Number FROM section WHERE Sem_ID = $semesterId";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $sections = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($sections);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

mysqli_close($conn);
?>
