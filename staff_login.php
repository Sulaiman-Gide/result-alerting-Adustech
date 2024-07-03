<?php
session_start();

require 'config.php';

$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "online_result";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Sanitize input to prevent SQL injection (you should improve this for production)
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Check if administrator exists
    $sql = "SELECT * FROM Administrators WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['admin_logged_in'] = true; // Set session variable for authentication
        header("Location: staff_dashboard.php");
        exit(); // Ensure script stops execution after redirection
    } else {
        $_SESSION['login_error'] = "Invalid email or password"; // Set error message for login page
        header("Location: staff_login.php");
        exit(); // Ensure script stops execution after redirection
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aliko Dangote University - Staff Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Bh looped/9z9x8tDLQpjtGkLjtzz6wTNrP+v9bNq7tNdGcjVtIkT/wSBIQvHWLq" crossorigin="anonymous">
    <style>
      body {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100vw;
        height: 100vh;
        overflow-x: hidden;
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
        padding-top: 15px;
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
      <form action="staff_login.php" method="post">
        <label>
            <h2>Staff Login</h2>
        </label>
        
          <label for="email" class="">Email Address</label>
          <input type="email" class="" id="email" name="email" placeholder="example@gmail.com" required>
          <label for="password" class="">Password</label>
          <input type="password" class="" id="password" name="password"placeholder="********" required>  
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