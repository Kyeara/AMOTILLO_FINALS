<?php
session_start();
include('dbConfig.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$recipient_role = ($role === 'hr') ? 'applicant' : 'hr';

$stmt = $conn->prepare("SELECT id, username FROM users WHERE role = ? AND id != ?");
$stmt->bind_param("si", $recipient_role, $user_id);
$stmt->execute();
$recipients = $stmt->get_result();
$stmt->close();

$messages_query = "
    SELECT m.id, m.sender_id, m.receiver_id, m.message, m.created_at, u.username AS sender_name
    FROM messages m
    INNER JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = ? OR m.receiver_id = ?)
    AND (m.sender_id = ? OR m.receiver_id = ?)
    ORDER BY m.created_at ASC";
$chat_stmt = $conn->prepare($messages_query);
$chat_stmt->bind_param("iiii", $user_id, $user_id, $_GET['recipient_id'], $_GET['recipient_id']);
$chat_stmt->execute();
$messages = $chat_stmt->get_result();
$chat_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $recipient_id = (int)$_POST['recipient_id'];
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        $send_stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())");
        $send_stmt->bind_param("iis", $user_id, $recipient_id, $message);
        $send_stmt->execute();
        $send_stmt->close();
        header("Location: chat.php?recipient_id=" . $recipient_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .back-link {
            display: block;
            margin-bottom: 15px;
            color: #00796b;
            text-decoration: none;
            font-size: 16px;
            text-align: right;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        form {
            margin-top: 10px;
            text-align: center;
        }

        select {
            padding: 8px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-right: 10px;
        }

        button {
            padding: 8px 20px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #388e3c;
        }

        .message-box {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            height: 400px;
            overflow-y: auto;
            margin-bottom: 15px;
        }

        .message {
            margin-bottom: 15px;
        }

        .message .sender {
            font-weight: bold;
            color: #007bff;
        }

        .message .time {
            font-size: 0.8em;
            color: #888;
            display: block;
            margin-top: 5px;
        }

        .message .content {
            margin-top: 5px;
            font-size: 16px;
            line-height: 1.6;
        }

        textarea {
            width: 100%;
            height: 70px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            resize: none;
        }

        .no-messages {
            font-size: 18px;
            color: #888;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Chat</h2>
    <a class="back-link" href="index.php">Back to Homepage</a>

    <form method="GET" action="chat.php">
        <label for="recipient_id">Select Recipient:</label>
        <select name="recipient_id" id="recipient_id" required>
            <option value="">Choose a recipient</option>
            <?php while ($recipient = $recipients->fetch_assoc()): ?>
                <option value="<?php echo $recipient['id']; ?>" 
                    <?php echo (isset($_GET['recipient_id']) && $_GET['recipient_id'] == $recipient['id']) ? 'selected' : ''; ?> >
                    <?php echo htmlspecialchars($recipient['username']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Start Chat</button>
    </form>

    <?php if (isset($_GET['recipient_id']) && $messages->num_rows > 0): ?>
        <div class="message-box">
            <?php while ($message = $messages->fetch_assoc()): ?>
                <div class="message">
                    <span class="sender"><?php echo htmlspecialchars($message['sender_name']); ?>:</span>
                    <p class="content"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                    <span class="time"><?php echo $message['created_at']; ?></span>
                </div>
            <?php endwhile; ?>
        </div>
    <?php elseif (isset($_GET['recipient_id'])): ?>
        <p class="no-messages">No messages yet. Start the conversation!</p>
    <?php endif; ?>

    <?php if (isset($_GET['recipient_id'])): ?>
        <form method="POST" action="chat.php?recipient_id=<?php echo $_GET['recipient_id']; ?>">
            <input type="hidden" name="recipient_id" value="<?php echo (int)$_GET['recipient_id']; ?>">
            <textarea name="message" placeholder="Type your message here..." required></textarea>
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
