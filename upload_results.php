<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: staff_login.php");
    exit;
}

// Database connection details
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

// Initialize response message
$response = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // File handling
    $target_dir = "uploads/"; // Directory where files will be uploaded
    $target_file = $target_dir . basename($_FILES["result_file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size (adjust as necessary)
    if ($_FILES["result_file"]["size"] > 5000000) { // 5 MB limit
        $response = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only PDF files
    if ($fileType != "pdf") {
        $response = "Sorry, only PDF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $response = "Your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["result_file"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now insert into database
            $level = $_POST['level'];
            $semester = $_POST['semester'];
            $file_name = htmlspecialchars(basename($_FILES["result_file"]["name"]));

            // Insert into database (adjust table name and columns as per your schema)
            $sql = "INSERT INTO result_files (level, semester, file_name, file_path) 
                    VALUES ('$level', '$semester', '$file_name', '$target_file')";

            if ($conn->query($sql) === TRUE) {
                $response = "The file $file_name has been uploaded and saved to database.";
            } else {
                $response = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $response = "Sorry, there was an error uploading your file.";
        }
    }

    // Required if your environment does not handle autoloading
    require __DIR__ . '\twilio-php-main\src\Twilio\autoload.php';

    // Your Account SID and Auth Token from console.twilio.com
    $sid = "ACe9cf77a32b3baefb3a8d79e5c88a5134";
    $token = "0d95d10c4eaceeb79ead2713a677fcfa";
    $client = new Twilio\Rest\Client($sid, $token);

    try {
        // Use the Client to make requests to the Twilio REST API
        $client->messages->create(
            // The number you'd like to send the message to
            '+2348101892045', // Ensure this number is verified on Twilio
            [

                'from' => '+14155787485',
                // The body of the text message you'd like to send
                'body' => "Hello, this is to inform you that our result has been uploaded. Please log in to your dashboard to view and download it"
            ]
        );
        echo "Message sent successfully!";
    } catch (Twilio\Exceptions\RestException $e) {
        echo "Error: " . $e->getMessage();
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
            padding: 20px 20px 30px 10px;
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
            grid-template-columns: 1fr;
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

        /* Added styles for response messages */
        .response {
            margin-top: 1rem;
            margin-bottom: 1.3rem;
            padding: 0.8rem;
            border-radius: 0.375rem;
            width: 100%;
            text-align: center;
        }

        .response.error {
            background-color: #fca5a5;
            color: #9b2c2c;
        }

        .response.success {
            background-color: #b8f2e6;
            color: #064e3b;
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
            <a href="upload_results.php" class="active">
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

        <div class="main-content">
            <h2>Upload Results</h2>
            
            <!-- Response message area -->
            <?php if (!empty($response)): ?>
                <div class="response <?php echo strpos($response, 'Error') !== false ? 'error' : 'success'; ?>">
                    <?php echo $response; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="form-container">
                    <div class="form-group">
                        <label for="level">Level:</label>
                        <input type="text" id="level" name="level" required>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester:</label>
                        <select id="semester" name="semester" required>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="result_file">Select PDF File:</label>
                        <input type="file" id="result_file" name="result_file" accept=".pdf" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
