<?php
session_start();
include('dbConfig.php');

if ($_SESSION['role'] != 'applicant') {
    header("Location: index.php");
    exit();
}

$hr_id = 2;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $message = htmlspecialchars($message);

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $_SESSION['user_id'], $hr_id, $message);

    if ($stmt->execute()) {
        $success_message = "Message sent successfully.";
    } else {
        $error_message = "Error sending message: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message HR</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        header {
            color: black;
            text-align: center;
            padding: 30px 0;
            font-size: 32px;
            letter-spacing: 1px;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 50px;
            color: black;
            margin-bottom: 25px;
        }

        label {
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
            color: #00796b;
        }

        textarea {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 25px;
            resize: vertical;
            background-color: #e8f5e9;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-transform: uppercase;
            font-weight: 500;
        }

        button:hover {
            background-color: #388e3c;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 30px;
            font-size: 18px;
            text-decoration: none;
            color: #00796b;
            font-weight: 500;
        }

        a:hover {
            color: #004d40;
        }

        .message-status {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            padding: 12px;
            border-radius: 8px;
        }

        .message-status.success {
            color: #388e3c;
            background-color: #e8f5e9;
            font-weight: bold;
        }

        .message-status.error {
            color: #e53935;
            background-color: #ffebee;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>Send Message to HR</h1>
</header>

<div class="container">
    <?php if (isset($success_message)): ?>
        <div class="message-status success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message-status error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="6" required></textarea>

        <button type="submit">Send Message</button>
    </form>

    <a href="index.php">Back to Homepage</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
