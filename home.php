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
    <title>Home</title>
    <link rel="stylesheet" href="css/stylehome.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<style>
    .user-img {
  width: 50px;  /* Set your desired width */
  height: 50px; /* Set the same height to make it a square */
  background-color: white;
  padding: 2px;
  object-fit: contain;  /* Ensures the image maintains its aspect ratio while fitting inside the defined width/height */
}
</style>
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
    <div>
        <div class="top-middle">
            <div class="logos-container">
                <img src="css/logo uptm.png" alt="UPTM Logo" class="logo-image">
                <img src="css/fcom_logo_clear.png" alt="FCOM Logo" class="logo-image">
            </div>
            <div class="user-info-overlay">
                <h1 class="typing-text" data-text="<?php echo htmlspecialchars($userName); ?>"></h1>
                <h3 class="fade-in-text"><?php echo htmlspecialchars($userType); ?></h3>
                <h4 class="fade-in-text">Faculty of Computing and Multimedia</h4>
            </div>
        </div>
        <div class="dashboard">
            <h1>Welcome to the Dashboard, <?php echo htmlspecialchars($userName); ?></h1>
            <div class="dashboard-cards">
                <?php if ($userType == "dean") : ?>
                    <a href="lecturerlist.php" class="dashboard-card">
                        <i class="bx bxs-user-pin"></i>
                        <h3>Lecturer List</h3>
                    </a>
                    <a href="schedulelist.php" class="dashboard-card">
                        <i class="bx bx-table"></i>
                        <h3>Schedule List</h3>
                    </a>
                    <a href="courselist.php" class="dashboard-card">
                        <i class="bx bx-book"></i>
                        <h3>Course List</h3>
                    </a>
                <?php endif; ?>
                <?php if ($userType == "coordinator") : ?>
                    <a href="studentlist.php" class="dashboard-card">
                        <i class="bx bxs-user-detail"></i>
                        <h3>Student List</h3>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    let btn = document.querySelector('#btn')
    let sidebar = document.querySelector('.sidebar')

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };

    // TYPING TEXT TOP MIDDLE
    document.addEventListener("DOMContentLoaded", function () {
        const nameElement = document.querySelector('.typing-text');
        const additionalElements = document.querySelectorAll('.fade-in-text');

        function typeWithMistakeCorrection(element, text, callback) {
            let charIndex = 0;
            let isMistake = false;
            let mistakeLength = 0;
            const typingSpeed = 50;
            const mistakeChance = 0.05;

            const typingInterval = setInterval(() => {
                if (isMistake) {
                    if (mistakeLength > 0) {
                        element.textContent = element.textContent.slice(0, -1);
                        mistakeLength--;
                    } else {
                        isMistake = false;
                    }
                } else {
                    if (charIndex < text.length) {
                        element.textContent += text.charAt(charIndex);
                        charIndex++;
                        if (Math.random() < mistakeChance && charIndex > 1) {
                            isMistake = true;
                            mistakeLength = Math.floor(Math.random() * 3) + 1;
                            charIndex -= mistakeLength;
                        }
                    } else {
                        clearInterval(typingInterval);
                        if (callback) callback();
                    }
                }
            }, typingSpeed);
        }

        if (nameElement) {
            const text = nameElement.getAttribute('data-text');
            typeWithMistakeCorrection(nameElement, text, () => {
                additionalElements.forEach((el, index) => {
                    setTimeout(() => {
                        el.style.animation = 'fadeIn 3s ease-in forwards';
                    }, index * 3000);
                });
            });
        }
    });
</script>
</html>
