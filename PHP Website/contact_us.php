<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
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
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch chat messages for the user
$stmt = $pdo->prepare("SELECT * FROM chats WHERE user_id = :user_id ORDER BY sent_at ASC");
$stmt->execute([':user_id' => $user_id]);
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</head>
<style>
    .chat-container {
        max-width: 600px;
        margin: 20px auto;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
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

    #contactSection,
    #chatSection {
        display: none;
    }
</style>

<?php include 'navbar.php'; ?>


<header class="bg-light py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Get in Touch</h1>
        <p class="lead text-muted">We’re here to assist you. Reach out to us anytime!</p>
    </div>
</header>


<div class="container my-3 ">
    <button class="btn btn-primary me-3" id="contactButton">Contact</button>
    <button class="btn btn-secondary" id="liveChatButton">Live Chat</button>
</div>

<div id="contactSection" class="container py-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <h2 class="mb-4">Contact Form</h2>
            <form action="contact_process.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message here" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        <div class="col-md-6">
            <h2 class="mb-4">Contact Details</h2>
            <p class="text-muted"><i class="bi bi-geo-alt-fill"></i> Pyi Yeik Thar Street, Kamayut, Yangon, Myanmar</p>
            <p class="text-muted"><i class="bi bi-phone-fill"></i> +959450197415</p>
            <p class="text-muted"><i class="bi bi-envelope-fill"></i> support@fragrancehaven.com</p>
            <h5 class="mt-4">Business Hours</h5>
            <p class="text-muted">Monday - Friday: 9:00 AM - 6:00 PM</p>
            <p class="text-muted">Saturday: 10:00 AM - 4:00 PM</p>
            <p class="text-muted">Sunday: Closed</p>
        </div>
    </div>
</div>


<div id="chatSection" class="container py-5" style="display: none;">
    <div class="chat-container p-9 rounded-3 shadow-sm" style="background: #f8f9fa;">
        <div class="text-center mb-1">
            <img src="images/chat.gif" alt="Chat GIF" class="img-fluid" style="max-width: 180px;">
        </div>
        <h3 class="text-center mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 400; color: #333;">Let's Talk!!</h3>
        <div class="chat-box mb-2" style="height: 250px; overflow-y: scroll; padding-right: 15px;">
            <?php foreach ($chats as $chat): ?>
                <div class="chat-bubble <?php echo $chat['sender'] === 'user' ? 'user-bubble' : 'admin-bubble'; ?> mb-3">
                    <small class="fw-bold"><?php echo $chat['sender'] === 'user' ? 'You' : 'Admin'; ?></small><br>
                    <p class="m-0"><?php echo htmlspecialchars($chat['message']); ?></p>
                    <small class="text-muted"><?php echo $chat['sent_at']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <form action="user_send_message.php" method="POST">
            <div class="input-group">
                <input type="text" name="message" class="form-control rounded-pill border-0 shadow-sm" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Send</button>
            </div>
        </form>
    </div>
</div>

<section id="mapSection" class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-3">Our Location</h2>
        <div class="map-container rounded shadow-sm overflow-hidden">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3832.4929049234636!2d96.14201991480174!3d16.83087312393282!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c19491d5fc4915%3A0xbc8c93b252e3f2f5!2sKamayut%20Township%2C%20Yangon%2C%20Myanmar%20(Burma)!5e0!3m2!1sen!2smm!4v1689567890123!5m2!1sen!2smm" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<script>
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('page') === 'chatSection') {
            document.getElementById('contactSection').style.display = 'none';
            document.getElementById('mapSection').style.display = 'none';
            document.getElementById('chatSection').style.display = 'block';
        
        } else {
            document.getElementById('contactSection').style.display = 'block';
            document.getElementById('chatSection').style.display = 'none';
        }
    };

    const liveChatButton = document.getElementById('liveChatButton');
    const contactButton = document.getElementById('contactButton');
    const chatSection = document.getElementById('chatSection');
    const contactSection = document.getElementById('contactSection');

    liveChatButton.addEventListener('click', () => {
        chatSection.style.display = 'block';
        mapSection.style.display = 'none';
        contactSection.style.display = 'none';
    });

    contactButton.addEventListener('click', () => {
        contactSection.style.display = 'block';
        chatSection.style.display = 'none';
        mapSection.style.display = 'block';
     
    });

    
</script>
</div>

<?php include 'footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>