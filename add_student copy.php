<?php
include("connect.php");

// Initialize the response with default values
$response = ["success" => false, "message" => ""];

// Check the request method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve POST data
    $studentName = isset($_POST['studentName']) ? $_POST['studentName'] : null;
    $sectionID = isset($_POST['sectionSelect']) ? $_POST['sectionSelect'] : null;

    // Debugging: Output received data
    $response["debug"] = [
        "studentName" => $studentName,
        "sectionID" => $sectionID
    ];

    // Check if student name and section ID are provided
    if (empty($studentName) || empty($sectionID)) {
        $response["message"] = "Student name and section must be provided.";
    } else {
        // Insert student directly with the provided Section_ID
        $sql_insert = "INSERT INTO student (Stud_Name, Section_ID) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);

        // Check if statement preparation was successful
        if (!$stmt_insert) {
            $response["message"] = "Error in preparing SQL statement: " . $conn->error;
        } else {
            // Bind parameters and execute
            $stmt_insert->bind_param("si", $studentName, $sectionID);
            if ($stmt_insert->execute()) {
                $response["success"] = true;
                $response["message"] = "Student added successfully.";
            } else {
                $response["message"] = "Failed to add student: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
    }
} else {
    $response["message"] = "Invalid request method.";
}

// Close the database connection
$conn->close();

// Output the response as JSON
echo json_encode($response);
