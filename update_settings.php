<?php
session_start();
include("connect.php");

// Create the connection using mysqli_connect
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $currentEmail = $_SESSION["email"];

    // Build the query
    $query = "UPDATE deputydean SET Dean_Name = ?, Dean_Email = ?";
    $types = "ss";
    $params = [$name, $email];

    // If a new password is provided, include it in the query
    if (!empty($password)) {
        $query .= ", Dean_Password = ?";
        $types .= "s";
        $params[] = $password; // Store the plain text password
    }

    // Complete the query
    $query .= " WHERE Dean_Email = ?";
    $types .= "s";
    $params[] = $currentEmail;

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Update session data
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;

        // Redirect back to settings with a success message
        header("Location: settings.php?success=1");
    } else {
        // Redirect back to settings with an error message
        header("Location: settings.php?error=1");
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Close the connection
mysqli_close($conn);
?>
