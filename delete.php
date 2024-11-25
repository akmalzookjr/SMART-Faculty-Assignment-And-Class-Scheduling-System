<?php
include 'connect.php'; // Make sure this file contains the correct database connection

if(isset($_GET['deleteid'])){
    $id = $_GET['deleteid'];

    // Make sure to use the correct variable name for the database connection
    $sql = "DELETE FROM lecturer WHERE Lect_ID = $id"; 
    $result = mysqli_query($conn, $sql); // Use $conn here

    if($result){
        echo "Deleted successfully";
        // You can also add a redirect here if necessary
        header('Location: lecturerlist.php');
    } else {
        die(mysqli_error($conn)); // Use $conn here
    }
}
?>
