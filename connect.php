<?php

    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "smartsystem";

    // Create the connection using mysqli_connect
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Optional: uncomment to verify connection
    // echo "Connected successfully!";
?>
