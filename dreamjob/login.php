<?php
session_start();
include('dbConfig.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php");
    } else {
        $error_message = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f1f1f1;
    margin: 0;
    padding: 0;
    color: #333;
}


header {
    color: black;
    text-align: center;
    padding: 30px 0;
}

header h1 {
    font-size: 50px;
    margin: 0;
    font-weight: normal;
}


.container {
    max-width: 450px;
    margin: 70px auto;
    background-color: #ffffff;
    padding: 35px;
    border-radius: 10px;
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
}


h2 {
    text-align: center;
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 25px;
}


label {
    font-size: 16px;
    color: #2c3e50;
    margin-bottom: 8px;
    display: block;
}


input[type="text"], input[type="password"] {
    width: 100%;
    padding: 12px 18px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

input[type="text"]:focus, input[type="password"]:focus {
    border-color: #3498db;
    outline: none;
}


button {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 12px 24px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #2980b9;
}

p {
    text-align: center;
    font-size: 14px;
    color: #333;
}

a {
    color: #3498db;
    text-decoration: none;
}

a:hover {
    color: #2980b9;
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
        padding: 20px;
    }
    
    header h1 {
        font-size: 30px;
    }
}

    </style>
</head>
<body>

<header>
    <h1>Login</h1>
</header>

<div class="container">
    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

</body>
</html>
