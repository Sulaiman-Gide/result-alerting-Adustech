<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header("Location: staff_login.php");
        exit;
    }

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_result";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to retrieve students with their details
    $sql_students = "SELECT * FROM Students";
    $result_students = $conn->query($sql_students);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aliko Dangote University - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,600,700,700i&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <style>
        body {
            height: 100vh;
            overflow: hidden;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            display: flex;
            overflow-y: hidden;
            background-color: #f9fafb;
            user-select: none;
            height: 100%;
        }

        .sidebar {
            display: none;
            flex-direction: column;
            width: 13rem;
            height: 100%;
            margin-left: 0.3rem;
            margin-top: 0.3rem;
            margin-bottom: 0.3rem;
            border-radius: 0.375rem;
            background-color: white;
            overflow-y: scroll;
            padding: 0.5rem 0.7rem;
            border: 1px solid #e5e7eb;
        }

        .sidebar::-webkit-scrollbar {
            display: none;
        }
        .sidebar h2 {
            font-weight: 800;
            font-size: 1.52rem;
            text-align: center;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .sidebar h1 {
            font-weight: 800;
            font-size: 1rem;
            text-align: center;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .sidebar span {
            display: block;
            border: 1px solid #d1d5db;
            width: 100%;
            margin: 1rem 0;
        }

        .sidebar a,
        .sidebar div {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            
            cursor: pointer;
            padding: 0.4rem 0.5rem;
            border-radius: 0.375rem;
            margin-top: 1rem;
            border: 1px solid #e5e7eb;
            color: #4b5563;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar div:hover {
            background-color: #1f2937;
            color: white;
        }

        .sidebar a.active {
            background-color: #1f2937;
            color: white;
        }

        .sidebar a.logout {
            background-color: red;
            color: white;
        }

        .sidebar a.logout:hover {
            background-color: #8B0000;
        }

        .sidebar a:hover .icon,
        .sidebar div:hover .icon {
            color: white;
        }

        .sidebar a .icon,
        .sidebar div .icon {
            font-size: 1.1rem;
            margin-right: 0.75rem;
        }

        .sidebar a h1,
        .sidebar div h1 {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .main-content {
            background: white;
            padding: 2rem 4rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            padding: 1rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            overflow-y: auto;
            overflow-x: hidden;
        
        }
        table {
            width: 100%;
            border-collapse: collapse;

        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        @media (min-width: 640px) {
            .sidebar {
                display: flex;
            }
        }

        @media (min-width: 768px) {
            .sidebar {
                width: 20%;
            }
            .main-content {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Staff Panel</h2>
            <span></span>
            <a href="staff_dashboard.php">
                <h1>Dashboard</h1>
            </a>
            <a href="manage_staff.php">
                <h1>Manage Lecturers</h1>
            </a>
            <a href="add_staff.php">
                <h1>Add Lecturer</h1>
            </a>
            <span></span>
            <a href="upload_results.php">
                <h1>Upload Results</h1>
            </a>
            <a href="manage_students.php" class="active">
                <h1>Manage Students</h1>
            </a>
            <span></span>
            <a href="index.html">
                <i class="fa-solid fa-house icon"></i>
                <h1>Home</h1>
            </a>
            <a href="logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket icon"></i>
                <h1>Logout</h1>
            </a>

        </div>

        <div class="main-content">
            <h2>Manage Students</h2>
            <table>
                <thead>
                    <tr>
                        <th>Registration Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_students->num_rows > 0) {
                        while ($row = $result_students->fetch_assoc()) {
                            $registration_number = $row['student_id'];
                            $name = $row['name'];
                            $email = $row['email'];

                            // Determine level based on registration number prefix
                            $level = "Unknown";
                            $prefix = substr($registration_number, 0, 4); // Extract first four characters (e.g., UG18)

                            switch ($prefix) {
                                case 'UG18':
                                    $level = 4;
                                    break;
                                case 'UG20':
                                    $level = 3;
                                    break;
                                case 'UG21':
                                    $level = 2;
                                    break;
                                case 'UG22':
                                    $level = 1;
                                    break;
                                case 'UG23':
                                    $level = 1;
                                    break;
                                default:
                                    $level = "Unknown";
                                    break;
                            }

                            echo "<tr>
                                    <td>$registration_number</td>
                                    <td>$name</td>
                                    <td>$email</td>
                                    <td>$level</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No students found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
