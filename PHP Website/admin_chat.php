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


$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $pdo->prepare("SELECT DISTINCT user_id, MAX(sent_at) AS last_message_time FROM chats WHERE user_id LIKE :search_query GROUP BY user_id ORDER BY last_message_time DESC");
    $stmt->execute([':search_query' => '%' . $search_query . '%']);
} else {
    $stmt = $pdo->prepare("SELECT DISTINCT user_id, MAX(sent_at) AS last_message_time FROM chats GROUP BY user_id ORDER BY last_message_time DESC");
    $stmt->execute();
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

   
    $stmt = $pdo->prepare("DELETE FROM chats WHERE user_id = :user_id");
    $stmt->execute([
        ':user_id' => $user_id
    ]);
 
    header("Location: admin_chat.php?message=Chat deleted successfully");
    exit;
    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat</title>
    <title>Admin Dashboard - Manage Users</title>
 
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<style>
    .navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<body>
<?php include 'admin_navbar.php'; ?>
    <?php include 'offcanvas_sidebar.php'; ?>

    <div class="container mt-5">
        <h2>Customer Messages</h2>
        <form method="GET" action="admin_chat.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by User ID" value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
        <table class="table table-striped">
            <thead class=table-warning>
                <tr>
                    <th>User ID</th>
                    <th>Last Message Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_message_time']); ?></td>
                        <td>
                            <a href="admin_chat_reply.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-primary btn-sm">View Chat</a>
                            <a href="delete_chat.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this chat?')">Delete Chat</a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Bootstrap JS and Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="edit_products.js"></script>
</body>

</html>