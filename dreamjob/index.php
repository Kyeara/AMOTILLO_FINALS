<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role']; 
$user_id = $_SESSION['user_id'];

include('dbConfig.php');

$chat_users = [];
if ($role == 'hr') {
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'applicant'");
} else {
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'hr'");
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $chat_users[] = $row;
}

$job_posts = [];
if ($role == 'applicant') {
    $stmt = $conn->prepare("SELECT a.status, j.title FROM applications a 
                            LEFT JOIN job_posts j ON a.job_post_id = j.id 
                            WHERE a.applicant_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $application_status = $row['status']; 
        if ($application_status == 'accepted') {
            $accepted_job_title = $row['title'];
        }
    }

    $stmt = $conn->prepare("SELECT id, title, description FROM job_posts");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $job_posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            
            color: black;
            text-align: center;
            font-size: 28px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin: 30px;
        }

        .sidebar {
            width: 220px;
            background-color: #1abc9c;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            margin-bottom: 10px;
            border-radius: 6px;
            text-transform: uppercase;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #16a085;
        }

        .main-content {
            width: 75%;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .role-message {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }

        .job-posts-board, .accepted-jobs, .rejected-jobs, .chat-section {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        .job-posts-board h3, .accepted-jobs h3, .rejected-jobs h3, .chat-section h3 {
            color: #1abc9c;
            font-size: 24px;
        }

        .job-posts-board table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .job-posts-board th, .job-posts-board td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .job-posts-board th {
            background-color: #1abc9c;
            color: white;
        }

        .job-posts-board td a {
            color: #1abc9c;
            text-decoration: none;
            font-weight: bold;
        }

        .job-posts-board td a:hover {
            text-decoration: underline;
        }

        .chat-users {
            list-style: none;
            padding-left: 0;
        }

        .chat-users li {
            margin-bottom: 10px;
        }

        .chat-users a {
            color: #1abc9c;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
        }

        .chat-users a:hover {
            text-decoration: underline;
        }

        .logout {
            text-align: center;
            margin-top: 40px;
        }

        .logout button {
            background-color: #e53935;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .logout button:hover {
            background-color: #c62828;
        }

        .accepted-jobs, .rejected-jobs {
            background-color: #e8f5e9;
        }

        .rejected-jobs {
            background-color: #ffebee;
        }

        .accepted-jobs li, .rejected-jobs li {
            padding: 14px;
            margin-bottom: 14px;
            border-radius: 8px;
            background-color: #ffffff;
            border: 1px solid #ddd;
        }

    </style>
</head>
<body>

<header>
    <h1>Job Portal Dashboard</h1>
</header>

<div class="container">

    
    <div class="sidebar">
        <?php if ($role == 'hr'): ?>
            <a href="jobCreate.php">Create Job Post</a>
            <a href="applicationView.php">View Applications</a>
            <a href="messagesView.php">View Messages</a>
        <?php elseif ($role == 'applicant'): ?>
            <a href="messageHR.php">Message HR</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>

 
    <div class="main-content">
        <div class="role-message">
            <?php if ($role == 'hr'): ?>
                <p>You are logged in as HR. You can post jobs, review applications, and interact with applicants.</p>
            <?php elseif ($role == 'applicant'): ?>
                <p>You are logged in as an Applicant. You can apply for jobs and message HR representatives.</p>
            <?php endif; ?>
        </div>

        <?php if ($role == 'applicant'): ?>
        <div class="job-posts-board">
            <h3>Available Job Posts</h3>
            <?php if (empty($job_posts)): ?>
                <p>No job posts available at the moment.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Description</th>
                            <th>Apply</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($job_posts as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['description']); ?></td>
                                <td><a href="jobApply.php?job_post_id=<?php echo $job['id']; ?>">Apply</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($role == 'applicant'): ?>
        <div class="accepted-jobs">
            <?php
                $stmt = $conn->prepare("SELECT j.title FROM applications a 
                                        LEFT JOIN job_posts j ON a.job_post_id = j.id 
                                        WHERE a.applicant_id = ? AND a.status = 'accepted'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0): ?>
                    <h3>Accepted Job Titles</h3>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($row['title']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No accepted jobs yet.</p>
                <?php endif; ?>
        </div>

        <div class="rejected-jobs">
            <?php
                $stmt = $conn->prepare("SELECT j.title FROM applications a 
                                        LEFT JOIN job_posts j ON a.job_post_id = j.id 
                                        WHERE a.applicant_id = ? AND a.status = 'rejected'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0): ?>
                    <h3>Rejected Job Titles</h3>
                    <ul>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($row['title']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No rejections yet.</p>
                <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="chat-section">
            <h3>Start a Chat</h3>
            <?php if (empty($chat_users)): ?>
                <p>No users available for chat at the moment.</p>
            <?php else: ?>
                <ul class="chat-users">
                    <?php foreach ($chat_users as $user): ?>
                        <li>
                            <a href="chat.php?recipient_id=<?php echo $user['id']; ?>">
                                Chat with <?php echo htmlspecialchars($user['username']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
