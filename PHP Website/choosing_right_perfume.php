<?php

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Choosing the Right Perfume | Your Perfume Website</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 36px;
            font-weight: 600;
            color: #333;
        }

        .content {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .content p {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
        }

        .content ol {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
        }

        .tips-list {
            list-style-type: none;
            padding-left: 0;
        }

        .tips-list li {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }

        .tips-list li::before {
            content: '\2022';
            color: #e74c3c;
            font-size: 24px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .call-to-action {
            text-align: center;
            margin-top: 40px;
        }

        .call-to-action h2 {
            font-size: 30px;
            font-family: 'Poppins', sans-serif;
            color: #e74c3c;
        }

        .call-to-action p {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            font-size: 18px;
            padding: 12px 40px;
            border-radius: 50px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .video-container {
            max-width: 800px;
            margin: 0 auto;
            margin-bottom: 40px;
        }

        .video-container iframe {
            width: 100%;
            height: 450px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
   
    <header class="container my-5">
        <div class="section-header">
            <h1>How to Find Your Signature Scent</h1>
        </div>
    </header>

   
    <div class="container">
        <div class="video-container">
        <iframe src="https://www.youtube.com/embed/7pXBBmvCtJU" title="How to Find Your Signature Scent" allowfullscreen></iframe>
        </div>
    </div>

    
    <div class="container content">
        <h2>Understand the Different Types of Perfumes</h2>
        <p>Choosing the right perfume can be a daunting task. Here's a guide to help you find the perfect fragrance that suits your personality and lifestyle:</p>
        <ol>
            <li><strong>Identify Your Scent Preferences:</strong> Think about whether you prefer floral, woody, oriental, or fresh scents.</li>
            <li><strong>Test Before You Buy:</strong> Always try a sample of the perfume before purchasing. Test it on your skin as fragrances can smell different on your body chemistry.</li>
            <li><strong>Consider the Season:</strong> Lighter scents are better for warmer months, while richer, spicier fragrances are more suitable for colder weather.</li>
            <li><strong>Think About the Occasion:</strong> Choose a fragrance that fits the occasion, whether it's an everyday scent, a formal event, or a night out.</li>
        </ol>

        <h3>Perfume Tips</h3>
        <ul class="tips-list">
            <li>When testing perfumes, don't smell more than three at a time to avoid overwhelming your senses.</li>
            <li>Don't rush the decision. Fragrances evolve over time, so give it a few hours to see how it develops on your skin.</li>
            <li>Perfume is an investment. A good fragrance can elevate your confidence and style.</li>
        </ul>

       
        <div class="call-to-action">
            <h2>Ready to Find Your Perfect Fragrance?</h2>
            <p>Explore our collection of perfumes and discover a scent that resonates with you!</p>
            <a href="user_index.php" class="btn-gradient">Shop Now</a>
        </div><br>
    </div>
    <?php include 'footer.php'; ?>
        
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
