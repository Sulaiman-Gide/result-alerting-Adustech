<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: staff_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_result";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($name) && !empty($email) && !empty($password)) {
        $stmt = $conn->prepare("INSERT INTO Administrators (name, email, password) VALUES (?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("sss", $name, $email, $password);
            if ($stmt->execute()) {
                $success = "Administrator account created successfully!";
            } else {
                $error = "Error executing statement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error preparing statement: " . $conn->error;
        }
    } else {
        $error = "All fields are required";
    }
}
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
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            padding: 50px 20px 30px 10px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            overflow-y: auto;
            overflow-x: hidden;
        
        }

        .main-content p {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            text-align: start;
        }
        form {
            width: 100%;
        }
        .form-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 1rem;
            width: 100%;
        }

        .form-group {
            width: 100%;
            margin-bottom: 1rem;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 0.75rem;
            margin-top: .8rem;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            box-sizing: border-box;
        }
        form .btn-div {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-top: .5rem;
        }
        form .btn {
            background-color: teal;
            color: white;
            font-weight: 500;
            font-size: .9rem;
            padding: 0.75rem 1.2rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form .btn:hover {
            background-color: rgb(0, 44, 44);
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
            <a href="add_staff.php" class="active">
                <h1>Add Lecturer</h1>
            </a>
            <span></span>
            <a href="upload_results.php">
                <h1>Upload Results</h1>
            </a>
            <a href="manage_students.php">
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
        <main class="main-content">
            <p>Welcome, use the form below to add an administrator.</p>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
            <form method="POST">
                <div class="form-container">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="example@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                </div>
                <div class="btn-div">
                    <button class="btn" type="submit">Register Administrator</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
