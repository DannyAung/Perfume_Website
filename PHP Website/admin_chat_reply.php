<?php
session_start();


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}


$host = 'localhost';
$username_db = 'root';
$password_db = '';
$db_name = 'ecom_website';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4;port=$port", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get the user ID from the URL
if (!isset($_GET['user_id'])) {
    header('Location: admin_chat.php');
    exit;
}

$user_id = $_GET['user_id'];

// Fetch user name 
if (!isset($_GET['user_name'])) {
    $userStmt = $pdo->prepare("SELECT user_name FROM users WHERE user_id = :user_id LIMIT 1");
    $userStmt->execute([':user_id' => $user_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_name = $user['user_name'];
    } else {
        $user_name = "Unknown User"; 
    }
} else {
    $user_name = $_GET['user_name'];
}

$stmt = $pdo->prepare("SELECT * FROM chats WHERE user_id = :user_id ORDER BY sent_at ASC");
$stmt->execute([':user_id' => $user_id]);
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with User #<?php echo $user_id; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .chat-container {
            max-width: 600px;
            margin: 20px auto;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chat-bubble {
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 75%;
            word-wrap: break-word;
        }

        .user-bubble {
            background-color: #007bff;
            color: white;
            text-align: left;
            margin-left: auto;
        }

        .admin-bubble {
            background-color: #e9ecef;
            text-align: left;
            margin-right: auto;
        }

        .chat-container {
            max-width: 600px;
            margin: 20px auto;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
        }

        .chat-box {
            padding: 10px;
        }

        .chat-bubble {
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 75%;
            word-wrap: break-word;
            clear: both;
        }

      
        .admin-bubble {
            background-color: #007bff;
            color: white;
            margin-left: 140px;
            text-align: right;
        }

     
        .user-bubble {
            margin-right: 140px;
            color: black;
            background-color: #e9ecef;
            text-align: left;
        }
    </style>
</head>

<body>
<?php include 'admin_navbar.php'; ?>
<?php include 'offcanvas_sidebar.php'; ?>


    <div class="container">
        <div class="chat-container">
           
            <div class="text-center mb-1">
                <img src="images/chat.gif" alt="Chat GIF" class="img-fluid" style="max-width: 180px;">
            </div>
            <h4 class="text-center">Chat with User <?php echo $user_name; ?></h4>
            <div class="chat-box" style="height: 400px; overflow-y: scroll;">
                <?php foreach ($chats as $chat): ?>
                    <div class="chat-bubble <?php echo $chat['sender'] === 'user' ? 'user-bubble' : 'admin-bubble'; ?>">
                        <small class="text-muted">
                            <?php echo $chat['sender'] === 'user' ? 'User' : 'Admin'; ?>
                        </small><br>
                        <p><?php echo htmlspecialchars($chat['message']); ?></p>
                        <small class="text-muted"><?php echo $chat['sent_at']; ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="admin_send_message.php" method="POST" class="mt-3">
                <div class="input-group">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="text" name="message" class="form-control" placeholder="Type your reply..." required>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <div class="row mt-4 border-top pt-3">
            <div class="col-md-6">
                <p class="text-muted">&copy; 2025 Fragrance Haven. All rights reserved.</p>
            </div>
    </footer>
</body>

</html>