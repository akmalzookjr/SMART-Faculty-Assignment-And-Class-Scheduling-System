<?php
include("connect.php"); // Your database connection file

// Query to get lecturer info along with total credit hours they are teaching
$sql = "SELECT lecturer.Lect_ID, lecturer.Lect_Name, lecturer.Lect_CH, SUM(course.Course_CH) AS total_taught_CH 
        FROM lecturer
        JOIN lecturer_course ON lecturer.Lect_ID = lecturer_course.Lect_ID
        JOIN course ON lecturer_course.Course_ID = course.Course_ID
        GROUP BY lecturer.Lect_ID";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer List</title>
    <link rel="stylesheet" href="css/style3.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

    <div class="main-content">
        <div class="header">
            <input type="text" placeholder="Search here...">
            <div class="profile-icons">
                <button class="icon bg-icon"><i class="fas fa-moon"></i></button>
                <button class="icon bg-icon"><i class="fas fa-bell"></i></button>
                <button class="icon bg-icon"><i class="fas fa-cog"></i></button>
                <button class="icon bg-icon"><i class="fas fa-calendar"></i></button>
                <div class="profile-pic bg-icon-profile">
                    <img src="profile-pic-placeholder.png" alt="Profile">
                </div>
            </div>
        </div>

        <div class="lecturer-list">
            <h2>Lecturer List</h2>
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox"></th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Credit Hour</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Calculate credit hour ratio and determine status
                            $total_taught_CH = $row['total_taught_CH'];
                            $lecturer_CH = $row['Lect_CH'];
                            $id = $row['Lect_ID'];
                            
                            // Determine status
                            if ($total_taught_CH >= $lecturer_CH) {
                                $status = "Full";
                                $status_class = "status-full"; // Green
                            } else {
                                $status = "Free";
                                $status_class = "status-free"; // Red
                            }

                            // Display row with status and credit hour
                            ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?php echo $row['Lect_Name']; ?></td>
                                <td class="<?php echo $status_class; ?>"><?php echo $status; ?></td>
                                <td><?php echo $total_taught_CH . '/' . $lecturer_CH; ?></td>
                                <td>
                                    <button class="edit-btn"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn"><a href="delete.php?deleteid='<?php echo $row['Lect_ID']; ?>'"><i class="fas fa-trash-alt"></i></a></button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>No lecturers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- <div class="popup"> -->
            <!-- Popup modal will be here -->
        </div>
    </div>
    
</body>
</html>

<?php
mysqli_close($conn);
?>
