<?php
// Database connection
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "smartsystem";

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the level ID from the request
$levelId = $_GET['level_id'];

// Fetch semesters associated with the selected level
$sql = "SELECT * FROM semester WHERE Level_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $levelId); // Bind the parameter as an integer
$stmt->execute();
$result = $stmt->get_result();

$semesters = [];
while ($row = $result->fetch_assoc()) {
    $semesters[] = $row;
}

// Return the semesters as JSON
echo json_encode($semesters);

// Close connection
$stmt->close();
$conn->close();
?>
