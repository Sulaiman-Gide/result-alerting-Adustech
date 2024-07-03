<?php
    session_start();

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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $student_id = $_POST['student_id'];
        $password = $_POST['password'];

        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT * FROM students WHERE student_id = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $student_id, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Login successful, set session variables
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_id'] = $student_id;
            header("Location: student.php");
            exit;
        } else {
            // Login failed, display error message
            $error = "Invalid student ID or password.";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,600,700,700i&display=swap" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100vw;
            height: 100vh;
            background-color: white;
        }

        .form-group {
            padding: 20px 15px; /* Base style for small screens */
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            width: 90%; /* Base width for small screens */
        }

        @media (min-width: 480px) {
            .form-group {
                padding: 25px 40px; /* Styles for screens 480px and larger */
                width: 75%;
            }
        }

        @media (min-width: 768px) {
            .form-group {
                width: 45%; /* Styles for screens 768px and larger */
            }
        }
        form {
            display: flex;
            justify-content: center;
            flex-direction: column;
            width: 100%;
        }
        label {
            display: block;
            margin-top: 1.5rem;
        }
        label h2 {
            color: #333;
            font: 700 2.20rem/3.875rem "Montserrat", sans-serif;
        }
        input {
            outline: none;
            border: none;
            display: block;
            width: 100%;
            padding-bottom: 10px;
            padding-left: 5px;
            margin-bottom: 20px;
            border-bottom: 2px solid rgb(178, 177, 177);
        }
        .form-footer {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0px 5px;
        }
        .form-check {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            flex-direction: row;
            padding: 0px;
            width: 50%;
        }
        .check-input {
            width: fit-content;
            height: 20px;
            margin-top: 3px;
            margin-right: 8px;
        }
        .check-label {
            display: inline;
            margin-top: 0px;
            margin-bottom: 0;
        }
        .go-back {
            font-size: 14px;
            color: #666;
            text-decoration: none;
        }
        .go-back:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
        }
        .btn {
            font: 700 1.12rem/2.5rem "Montserrat", sans-serif;
            color: aliceblue;
            background-color: #008080;
            width: 80%;
            margin: 3rem auto;
            border-radius: 30px;
        }
        .btn:hover {
            color: rgb(237, 237, 237);
            background-color: #006767;
        }
    </style>
</head>
<body>
    <div class="form-group">
        <form action="login.php" method="post">
            <label>
                <h2>Student Sign In</h2>
            </label>
            <?php
                if (isset($error)) {
                    echo '<div class="error">'.$error.'</div>';
                }
            ?>
            <label for="exampleInputstudent_id1">Registration Number</label>
            <input type="text" class="" id="student_id" placeholder="UG18/COMS/0000" name="student_id" oninput="this.value = this.value.toUpperCase();" required>
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="" id="exampleInputPassword1" name="password" placeholder="********">
            <div class="form-footer">
                <div class="form-check">
                    <input type="checkbox" class="check-input" id="rememberMe" name="rememberMe">
                    <label class="check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="index.html" class="go-back">Go back?</a>
            </div>
            <button type="submit" class="btn" name="login-submit">Login</button>
        </form>
    </div>
</body>
</html>
