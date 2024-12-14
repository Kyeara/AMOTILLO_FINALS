<?php
session_start();
include('dbConfig.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        echo "<p style='color: red;'>Passwords do not match!</p>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<p style='color: red;'>Username already taken!</p>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);
    $stmt->execute();

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
       
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f3f5;
    margin: 0;
    padding: 0;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}


.container {
    max-width: 420px;
    background-color: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    width: 100%;
}


h2 {
    text-align: center;
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 25px;
}


label {
    font-size: 16px;
    color: #34495e;
    margin-bottom: 8px;
    display: block;
}


input, select {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}


input:focus, select:focus {
    border-color: #3498db;
    outline: none;
}


button {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 14px 20px;
    font-size: 16px;
    border-radius: 8px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease;
}


button:hover {
    background-color: #2980b9;
}


p {
    text-align: center;
    font-size: 14px;
    margin-top: 20px;
}

a {
    color: #3498db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}


.error {
    color: #e74c3c;
    text-align: center;
    font-size: 14px;
    margin-bottom: 15px;
    font-weight: bold;
}


@media (max-width: 500px) {
    .container {
        width: 90%;
        padding: 25px;
    }
    
    h2 {
        font-size: 26px;
    }
}

    </style>
</head>
<body>

<div class="container">
    <h2>Register</h2>
    <form method="POST">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <label for="role">Role</label>
        <select name="role" id="role" required>
            <option value="applicant">Applicant</option>
            <option value="hr">HR</option>
        </select>

        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
