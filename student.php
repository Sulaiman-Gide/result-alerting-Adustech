<?php
session_start();

// Database connection
require 'config.php';

// Function to check if result is available for the student's level
function isResultAvailable($pdo, $level) {
    $sql = "SELECT COUNT(*) AS count FROM result_files WHERE level = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$level]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($result['count'] > 0);
}

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch student details
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT * FROM Students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Function to determine student level
function getStudentLevel($student_id) {
    // Extract the prefix from the student_id
    $prefix = substr($student_id, 0, 4);

    // Determine the level based on the prefix
    switch ($prefix) {
        case 'UG18':
            $level = 400;
            break;
        case 'UG20':
            $level = 300;
            break;
        case 'UG21':
            $level = 200;
            break;
        case 'UG22':
        case 'UG23':
            $level = 100;
            break;
        default:
            $level = "Unknown";
            break;
    }

    return $level;
}

$level = getStudentLevel($student['student_id']);

// Check if result is available for the student's level
$result_available = isResultAvailable($pdo, $level);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch the result file for the student's level
    $sql = "SELECT file_name, file_path FROM result_files WHERE level = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$level]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $file_name = $row['file_name'];
        $file_path = $row['file_path'];

        // Download the file
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        $response = "No result found for your level.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aliko Dangote University - Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,600,700,700i&display=swap" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/fontawesome-all.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <style>
        .text {
            padding-top: 20px;
        }
        .avatar {
            width: 50px;
            height: 50px;
            margin-left: 5px;
            border-radius: 50%;
            margin-bottom: .5rem;
        }
        .container {
            margin: 40px;
            padding: 0 20px;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 24px;
        }
        .card {
            overflow: hidden;
        }
        .card-content {
            padding: 24px;
        }
        .student-info {
            display: flex;
            align-items: start;
            margin-bottom: 16px;
        }
        .avatar {
            border-radius: 50%;
            margin-right: 16px;
            width: 100px;
            height: 100px;
        }
        .student-name {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .student-detail {
            color: #718096;
        }
        .download-button {
            background-color: #4299e1;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }
        .no-result {
            color: #e53e3e;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <a class="navbar-brand" href="index.html">
            <img src='images/logo.png' alt='logo'/>
            <h1 class="text">Aliko Dangote University Of <br> Science & Technology, Wudil</h1>
        </a>

        <!-- Mobile Menu Toggle Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-awesome fas fa-bars"></span>
            <span class="navbar-toggler-awesome fas fa-times"></span>
        </button>
        <!-- end of mobile menu toggle button -->

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link h6" href="#">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link h6" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- end of navigation -->

    <div class="container">
        <h1>Welcome, <?= htmlspecialchars($student['name']) ?></h1>
        <div class="card">
            <div class="card-content">
                <div class="student-info">
                    <img src="images/avater.png" alt="Avatar" class="avatar">
                    <div>
                        <p class="student-name"><?= htmlspecialchars($student['name']) ?></p>
                        <p class="student-detail"><?= htmlspecialchars($student['email']) ?></p>
                        <p class="student-detail"><?= htmlspecialchars($student['phone_number']) ?></p>
                        <p class="student-detail">Department ID: Computer Science</p>
                        <p class="student-detail">Level: <?= htmlspecialchars($level) ?></p>
                    </div>
                </div>
                <?php if ($result_available): ?>
                    <form method="POST" action="student.php">
                        <button type="submit">Download Result</button>
                    </form>
                <?php else: ?>
                    <p class="no-result">Result not available for your level yet.</p>
                <?php endif; ?>
                <?php if (isset($response)): ?>
                    <p class="no-result"><?= htmlspecialchars($response) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
