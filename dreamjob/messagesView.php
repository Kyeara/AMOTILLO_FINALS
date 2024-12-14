<?php
session_start();
include('dbConfig.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT messages.id, messages.sender_id, messages.message, messages.created_at, users.username AS sender_name FROM messages INNER JOIN users ON messages.sender_id = users.id WHERE messages.receiver_id = ? ORDER BY messages.created_at DESC");
$stmt->bind_param("i", $hr_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR - View Messages</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #fafafa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            color: black;
            text-align: center;
            padding: 25px 0;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 50px;
            text-align: center;
            color: black;
            margin-bottom: 25px;
        }

        .btn {
            padding: 12px 25px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: inline-block;
            margin: 10px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #004bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #00796b;
        }

        td {
            color: #555;
            word-wrap: break-word;
            max-width: 300px;
        }

        td a {
            color: #007bff;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        td em {
            font-style: italic;
            color: #888;
        }

        .no-messages {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>Messages from Applicants</h1>
</header>

<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Message</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['sender_name']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-messages">No messages from applicants yet.</p>
    <?php endif; ?>

    <br>
    <a href="index.php" class="btn">Back to Homepage</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
