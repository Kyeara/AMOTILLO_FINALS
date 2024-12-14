<?php
session_start();
include('dbConfig.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO job_posts (title, description, hr_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $description, $_SESSION['user_id']);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f7;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            color: black;
            text-align: center;
            padding: 25px 0;
            font-size: 32px;
            letter-spacing: 1px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 25px;
            color: #1e3a8a;
        }

        .btn {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 12px 0;
            text-align: center;
        }

        .btn-primary {
            background-color: #005bff;
            color: white;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #001bff;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            color: #555;
        }

        input[type="text"], textarea {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }

        textarea {
            height: 180px;
        }

        button {
            background-color: #1e3a8a;
            color: white;
            padding: 14px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2563eb;
        }

        .footer-link {
            text-align: center;
            margin-top: 20px;
        }

        .footer-link a {
            font-size: 16px;
            text-decoration: none;
            color: #1e3a8a;
        }

        .footer-link a:hover {
            color: #4CAF50;
        }

    </style>
</head>
<body>

<header>
    <h1>Create Job Post</h1>
</header>

<div class="container">
    <a href="index.php" class="btn btn-primary">Back to Homepage</a>
    <form method="POST">
        <label for="title">Job Title:</label>
        <input type="text" id="title" name="title" required><br>
        
        <label for="description">Job Description:</label>
        <textarea id="description" name="description" required></textarea><br>
        
        <button type="submit">Post Job</button>
    </form>

    <div class="footer-link">
        <a href="index.php">Back to Homepage</a>
    </div>
</div>

</body>
</html>
