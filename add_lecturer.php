<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["lecturerName"]);
    $email = mysqli_real_escape_string($conn, $_POST["lecturerEmail"]);
    $password = mysqli_real_escape_string($conn, $_POST["lecturerPassword"]);
    $creditHour = (int)$_POST["creditHour"];

    // Check if name or email already exists
    $checkQuery = "SELECT * FROM Lecturer WHERE Lect_Name = '$name' OR Lect_Email = '$email'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["success" => false, "message" => "Lecturer name or email already exists."]);
    } else {
        // Insert new lecturer if name and email are unique
        $sql = "INSERT INTO Lecturer (Lect_Name, Lect_Email, Lect_Password, Lect_CH) 
                VALUES ('$name', '$email', '$password', '$creditHour')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Lecturer added successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
        }
    }

    mysqli_close($conn);
}
?>
