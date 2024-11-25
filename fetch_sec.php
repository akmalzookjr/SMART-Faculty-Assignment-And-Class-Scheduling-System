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

// Get the semester ID from the request
$semesterId = $_GET['semester_id'];

// Fetch sections associated with the selected semester
$sql = "SELECT * FROM section WHERE Sem_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $semesterId); // Bind the parameter as an integer
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while ($row = $result->fetch_assoc()) {
    $sections[] = $row;
}

// Return the sections as JSON
echo json_encode($sections);

// Close connection
$stmt->close();
$conn->close();
?>
