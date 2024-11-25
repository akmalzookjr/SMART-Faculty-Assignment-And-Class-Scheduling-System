<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturerId = $_POST['lecturerId'];
    $lecturerName = $_POST['lecturerName'];
    $lecturerEmail = $_POST['lecturerEmail'];
    $lecturerPassword = $_POST['lecturerPassword'];
    $lecturerCH = $_POST['lecturerCH'];

    // Validate required fields
    if (empty($lecturerId) || empty($lecturerName) || empty($lecturerEmail) || empty($lecturerCH)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Update password if provided
    $passwordUpdate = "";
    if (!empty($lecturerPassword)) {
        $passwordUpdate = ", Lect_Password = '$lecturerPassword'";
    }

    // SQL query to update the lecturer
    $sql = "UPDATE Lecturer 
            SET Lect_Name = '$lecturerName', Lect_Email = '$lecturerEmail', Lect_CH = '$lecturerCH' $passwordUpdate 
            WHERE Lect_ID = '$lecturerId'";

    // Execute the query and handle the response
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Lecturer updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed: ' . mysqli_error($conn)]);
    }
}
?>
