<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    // Redirect to login page if session is not set
    header("Location: login.php");
    exit();
}

// Retrieve session data
$userName = $_SESSION["name"] ?? "User";
$userType = $_SESSION["type"] ?? "User Role";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="css/stylehome.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .settings-section {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 40%;
            margin: 50px auto;
        }

        .settings-section h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .settings-section p {
            font-size: 14px;
            color: #777;
            text-align: center;
            margin-bottom: 20px;
        }

        .settings-section form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .settings-section label {
            font-size: 14px;
            color: #555;
            font-weight: bold;
        }

        .settings-section input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .settings-section input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .btn-save {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            align-self: center;
        }

        .btn-save:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 8px rgba(0, 91, 179, 0.2);
        }
        .user-img {
  width: 50px;  /* Set your desired width */
  height: 50px; /* Set the same height to make it a square */
  background-color: white;
  padding: 2px;
  object-fit: contain;  /* Ensures the image maintains its aspect ratio while fitting inside the defined width/height */
}
.topfaq{
    align-items: center;
    display: flex;
    flex-direction: column;

}
.faq {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 80%;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
    margin: 20px auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.faq h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

.faq-item {
    margin-bottom: 15px;
    width: 50%;
}

.faq-question {
    font-size: 18px;
    font-weight: bold;
    color: #0056b3;
    cursor: pointer;
    padding: 10px;
    background: #e6f3ff;
    border: 1px solid #b3d8ff;
    border-radius: 5px;
    margin: 0;
}

.faq-answer {
    display: none;
    padding: 10px;
    background: #fff;
    border: 1px solid #ccc;
    border-top: none;
    border-radius: 0 0 5px 5px;
    color: #333;
}

    </style>
</head>
<body>
<div class="sidebar">
    <div class="top">
        <div class="logo">
            <span>SMART System</span>
        </div>
        <i class="bx bx-menu" id="btn"></i>
    </div>
    <div class="user">
        <a href="home.php"><img src="css/logo uptm.png" alt="me" class="user-img"></a>
        <div>
            <p class="bold"><?php echo htmlspecialchars($userName); ?></p>
            <p><?php echo htmlspecialchars($userType); ?></p>
        </div>
    </div>
    <ul>
        <li>
            <a href="home.php">
                <i class="bx bxs-grid-alt"></i>
                <span class="nav-item">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>
        <?php if ($userType == "dean") : ?>
            <li>
                <a href="lecturerlist.php">
                    <i class='bx bxs-user-pin'></i>
                    <span class="nav-item">Lecturer</span>
                </a>
                <span class="tooltip">Lecturer</span>
            </li>
            <li>
                <a href="schedulelist.php">
                    <i class='bx bx-table'></i>
                    <span class="nav-item">Schedule</span>
                </a>
                <span class="tooltip">Schedule</span>
            </li>
            <li>
                <a href="courselist.php">
                    <i class="bx bx-book"></i>
                    <span class="nav-item">Courses</span>
                </a>
                <span class="tooltip">Courses</span>
            </li>
        <?php endif; ?>
        <?php if ($userType == "coordinator") : ?>
            <li>
                <a href="studentlist.php">
                    <i class="bx bxs-user-detail"></i>
                    <span class="nav-item">Students</span>
                </a>
                <span class="tooltip">Students</span>
            </li>
        <?php endif; ?>
        <li>
            <a href="settings.php">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Settings</span>
            </a>
            <span class="tooltip">Settings</span>
        </li>
        <li>
            <a href="logout.php">
                <i class="bx bx-log-out"></i>
                <span class="nav-item">Logout</span>
            </a>
            <span class="tooltip">Logout</span>
        </li>
    </ul>
</div>
<div class="main-content">
    <header>
        <div class="logo">
            <img src="icon uptm.png" alt="uptm" class="logo-small">
        </div>
    </header>
    <div class="topfaq">
        <div class="settings-section">
            <h1>Settings</h1>
            <p>Manage your account settings and preferences here.</p>

            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])): ?>
                <p style="color: green; text-align: center;">Settings updated successfully!</p>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red; text-align: center;">Failed to update settings. Please try again.</p>
            <?php endif; ?>

            <form action="update_settings.php" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">
                
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>
        <div class="faq">
            <h2>FAQ</h2>
            <div class="faq-item">
                <h3 class="faq-question">What is the SMART Faculty Assignment and Class Scheduling System?</h3>
                <p class="faq-answer">The SMART Faculty Assignment and Class Scheduling System is a software solution designed to automate the assignment of lecturers to courses and the scheduling of classes. It ensures optimal workload distribution, prevents scheduling conflicts, and provides efficient timetables for students.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">Who can use this system?</h3>
                <p class="faq-answer">The system is designed for the Deputy Dean, Academic Coordinators, and Lecturers within the Faculty of Computing and Multimedia (FCOM). Students also benefit from improved scheduling indirectly.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">How does the system prevent scheduling conflicts?</h3>
                <p class="faq-answer">The system uses algorithms to detect and resolve overlapping schedules for lecturers and students, ensuring that no two classes or lecturers are scheduled at the same time in the same location.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">Can the system handle lecturer credit hour limits?</h3>
                <p class="faq-answer">Yes, the system automatically tracks and manages lecturer credit hours, ensuring that assignments stay within the allowable limits to avoid overloading lecturers.</p>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };

    document.querySelectorAll('.faq-question').forEach(item => {
    item.addEventListener('click', () => {
        const answer = item.nextElementSibling;
        const isActive = answer.style.display === 'block';

        // Hide all answers
        document.querySelectorAll('.faq-answer').forEach(ans => {
            ans.style.display = 'none';
        });

        // Toggle the clicked answer
        answer.style.display = isActive ? 'none' : 'block';
    });
});

</script>
</html>
