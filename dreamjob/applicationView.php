<?php
session_start();
include('dbConfig.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT a.applicant_id, a.resume, a.description, u.username, a.id AS application_id, a.status FROM applications a INNER JOIN users u ON a.applicant_id = u.id");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applicants</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fb;
            margin: 0;
            padding: 0;
        }

        header {
            color: black;
            text-align: center;
            font-size: 50px;
            font-weight: 600;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 50px;
            margin-bottom: 20px;
            color: #333;
        }

        .message {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn {
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
            margin: 5px;
            font-weight: 500;
        }
        
        .btn:hover {
            background-color: #004bff;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f1f1f1;
            color: #333;
        }

        td a {
            color: #007bff;
            font-weight: 500;
        }

        td a:hover {
            text-decoration: underline;
        }

        td em {
            font-style: italic;
            color: #888;
        }

        .actions form {
            display: inline-block;
        }

        .actions button {
            margin: 0 10px;
        }

        .actions button:hover {
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            table, th, td {
                font-size: 14px;
            }

            .btn {
                font-size: 14px;
                padding: 10px 15px;
            }
        }

    </style>
</head>
<body>

<header>
    <h1>Current Applications</h1>
</header>

<div class="container">
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            echo "<div class='message success'>Application processed successfully!</div>";
        } else {
            echo "<div class='message error'>An error occurred while processing the application. Please try again.</div>";
        }
    }
    ?>

    <a href="index.php" class="btn btn-primary">Back to Homepage</a>

    <table>
        <thead>
            <tr>
                <th>Applicant Name</th>
                <th>Contact Info</th>
                <th>Resume</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a href="uploads/<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">Download Resume</a>
                    </td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td class="actions">
                        <?php if ($row['status'] == 'pending') { ?>
                            <form action="applicationProcess.php" method="POST" style="display:inline;">
                                <button type="submit" name="action" value="accept" class="btn btn-success" onclick="return confirm('Are you sure you want to accept this applicant?')">Accept</button>
                                <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>" />
                            </form>
                            <form action="applicationProcess.php" method="POST" style="display:inline;">
                                <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this applicant?')">Reject</button>
                                <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>" />
                            </form>
                        <?php } else { ?>
                            <em>Already <?php echo htmlspecialchars($row['status']); ?></em>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
