<?php
session_start();
include('dbConfig.php');

$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SESSION['role'] != 'applicant') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resume = $_FILES['resume']['name'];
    $description = $_POST['description'];

    if ($_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $resume_path = $upload_dir . basename($resume);

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
            $stmt = $conn->prepare("INSERT INTO applications (job_post_id, applicant_id, resume, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $_GET['job_post_id'], $_SESSION['user_id'], $resume, $description);
            $stmt->execute();

            header("Location: index.php");
            exit();
        } else {
            echo "Failed to upload the resume. Please try again.";
        }
    } else {
        echo "Error uploading the resume.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
        }

        header {
            color: black;
            text-align: center;
            padding: 20px 0;
            font-size: 35px;
            font-weight: 600;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
            display: block;
        }

        textarea, input[type="file"], button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 8px;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        input[type="file"] {
            background-color: #f8f9fa;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
            display: block;
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

      
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }

    </style>
</head>
<body>

<header>
    <h1>Job Application</h1>
</header>

<div class="container">
    <h2>Apply for a Job</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="description">Phone Number or Email:</label>
            <textarea name="description" id="description" placeholder="Enter your contact info..." required></textarea>
        </div>

        <div class="form-group">
            <label for="resume">Resume (PDF format):</label>
            <input type="file" name="resume" id="resume" accept=".pdf" required>
        </div>

        <button type="submit">Submit Application</button>
    </form>

    <a href="index.php" class="back-link">Back to Homepage</a>
</div>

</body>
</html>
