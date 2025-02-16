<?php
session_start(); 

require_once 'db_connection.php';  


$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];


$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if ($product_id) {
    $sql = "SELECT * FROM products WHERE product_id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $product_name = htmlspecialchars($product['product_name']);
        $price = number_format($product['price'], 2);
        $discounted_price = number_format($product['discounted_price'], 2);
        $description = htmlspecialchars($product['description']);
        $stock_quantity = intval($product['stock_quantity']);
        $category = htmlspecialchars($product['category']);
        $subcategory = htmlspecialchars($product['subcategory']);
    } else {
        echo "Product not found.";
    }

    $reviews_sql = "SELECT r.review_text, r.rating, r.created_at, u.user_name FROM reviews r
                    JOIN users u ON r.user_id = u.user_id
                    WHERE r.product_id = :product_id ORDER BY r.created_at DESC";
    $reviews_stmt = $conn->prepare($reviews_sql);
    $reviews_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $reviews_stmt->execute();
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);


    $avg_rating_sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = :product_id";
    $avg_rating_stmt = $conn->prepare($avg_rating_sql);
    $avg_rating_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $avg_rating_stmt->execute();
    $avg_rating = $avg_rating_stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];
    $avg_rating = $avg_rating ? number_format($avg_rating, 1) : 0;
} else {
    echo "Invalid product ID.";
}


$in_wishlist = false;
if ($is_logged_in) {
    $query = "SELECT 1 FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $in_wishlist = $stmt->rowCount() > 0;
}

if (isset($_POST['add_to_wishlist'])) {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $user_id = $_SESSION['user_id'];


        $query = "INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            echo "Error adding item to wishlist.";
        }
    }
}


if (isset($_POST['remove_from_wishlist'])) {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $user_id = $_SESSION['user_id'];
        $query = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            echo "Error removing item from wishlist.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</head>

<style>
    .nav-tabs .nav-link {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        padding: 9px 19px;
        border-radius: 6px;
        text-align: center;
    }

    .review-text {
        text-align: left;
    }

    .description-text {
        text-align: left;
    }

    .nav-tabs .nav-link.active {
        background-color: rgb(253, 253, 255);
        color: white;
        border-color: rgb(138, 138, 139);
    }

    .nav-tabs .nav-link {
        background-color: transparent;
        color: rgb(146, 147, 148);

    }

    .card-body {
        padding: 20px;
    }

    .review,
    .description-text,
    .no-reviews {
        font-family: 'Roboto', sans-serif;
    }

    .review {
        padding: 16px;
        margin-bottom: 16px;
        background-color: transparent;
        /* Remove background shade */
    }

    .card {
        box-shadow: none;
    }

    .related-product-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
</style>



<body>
    <?php include 'navbar.php'; ?>
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </div>

        <!-- Product Details Section -->
        <div class="container my-5">
            <?php if ($product): ?>
                <div class="row">
                    <!-- Thumbnail Gallery and Main Image -->
                    <div class="col-md-6 d-flex">
                        <div class="thumbnail-gallery me-3" style="margin-left: 50px;">
                            <?php
                            // Images array
                            $images = [];

                            if (!empty($product['image'])) {
                                $images[] = 'products/' . htmlspecialchars($product['image']);
                            }
                            if (!empty($product['extra_image_1'])) {
                                $images[] = 'products/' . htmlspecialchars($product['extra_image_1']);
                            }
                            if (!empty($product['extra_image_2'])) {
                                $images[] = 'products/' . htmlspecialchars($product['extra_image_2']);
                            }

                            if (empty($images)) {
                                $images[] = 'images/default-image.jpg';
                            }

                            // Thumbnails for all images
                            foreach ($images as $index => $image_path) {
                                echo '<img src="' . $image_path . '" class="thumbnail-img mb-2" data-index="' . $index . '" 
                        style="width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 1px solid #ddd; border-radius: 5px;">';
                            }
                            ?>
                        </div>
                        <div class="main-image-container" style="margin-left: 10px;">
                            <img id="main-image" src="<?php echo $images[0]; ?>"
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                class="img-fluid rounded shadow"
                                style="height: 450px; width: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-6">
                        <h1 class="fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 36px; color: #222;">
                            <?php echo htmlspecialchars($product['product_name']); ?>
                        </h1>
                        <p class="text-secondary" style="font-family: 'Roboto', sans-serif; font-size: 14px; margin-bottom: 20px;">
                            <strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?>
                        </p>
                        <p class="text-secondary" style="font-family: 'Roboto', sans-serif; font-size: 14px; margin-bottom: 20px;">
                            <strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?>
                        </p>

                        <!-- Price Section -->
                        <?php if ($product['subcategory'] === 'discount' && isset($product['discounted_price']) && $product['discounted_price'] < $product['price']): ?>
                            <p class="text-muted text-decoration-line-through" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
                                $<?php echo number_format($product['price'], 2); ?>
                            </p>
                            <p class="text-danger fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 24px;">
                                $<?php echo number_format($product['discounted_price'], 2); ?>
                            </p>
                        <?php else: ?>
                            <p class="text-dark fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 24px;">
                                $<?php echo number_format($product['price'], 2); ?>
                            </p>
                        <?php endif; ?>


                        <p class="text-secondary" style="font-family: 'Roboto', sans-serif; font-size: 14px;">
                            <strong>Stock:</strong> <?php echo intval($product['stock_quantity']); ?> available
                        </p>


                        <p class="text-secondary" style="font-family: 'Roboto', sans-serif; font-size: 14px;">
                            <strong>Rating:</strong>
                            <span class="text-warning">
                                <?php for ($i = 0; $i < floor($avg_rating); $i++): ?>★<?php endfor; ?>
                                <?php for ($i = floor($avg_rating); $i < 5; $i++): ?>☆<?php endfor; ?>
                            </span>
                            (<?php echo $avg_rating; ?>)
                        </p>


                        <div class="d-flex align-items-center mt-4">
                            <form method="POST" action="add_to_cart.php" class="d-flex align-items-center me-3">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="number" name="quantity" value="1" min="1" class="form-control me-3" style="width: 100px;">
                                <button type="submit" name="add_to_cart" class="btn btn-primary px-4">
                                    Add to Cart
                                </button>
                            </form>


                            <form method="POST" action="product_details.php?product_id=<?php echo $product_id; ?>">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                                <?php if ($in_wishlist): ?>
                                    <button type="submit" name="remove_from_wishlist" class="btn btn-outline-danger" title="Remove from Wishlist">
                                        <i class="bi bi-heart-fill text-danger"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add_to_wishlist" class="btn btn-outline-primary" title="Add to Wishlist">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <div class="mt-4">
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active btn-sm" id="tabDescription" data-bs-toggle="tab" href="#collapseDescription" role="tab" aria-controls="collapseDescription" aria-selected="true">Description</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-sm" id="tabReviews" data-bs-toggle="tab" href="#collapseReviews" role="tab" aria-controls="collapseReviews" aria-selected="false">Reviews</a>
                            </li>
                        </ul>


                        <div class="tab-content mt-3">
                            <div class="tab-pane show active" id="collapseDescription" role="tabpanel" aria-labelledby="tabDescription">
                                <div class="card card-body">
                                    <p class="description-text">
                                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Reviews Tab -->
                            <div class="tab-pane fade" id="collapseReviews" role="tabpanel" aria-labelledby="tabReviews">
                                <div class="card card-body">
                                    <?php if ($reviews): ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <div class="review p-3 mb-4">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold review-author">
                                                        <?php echo htmlspecialchars($review['user_name']); ?>
                                                    </span>
                                                    <span class="text-muted review-date">
                                                        <?php echo date('F d, Y', strtotime($review['created_at'])); ?>
                                                    </span>
                                                </div>
                                                <div class="d-flex mt-2">
                                                    <span class="text-warning review-rating">
                                                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>★<?php endfor; ?>
                                                        <?php for ($i = $review['rating']; $i < 5; $i++): ?>☆<?php endfor; ?>
                                                    </span>
                                                </div>
                                                <p class="review-text">
                                                    <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="no-reviews">
                                            No reviews yet. Be the first to leave a review!
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.getElementById('btnDescription').addEventListener('click', function() {
                            var description = document.getElementById('collapseDescription');
                            var reviews = document.getElementById('collapseReviews');
                            if (description.classList.contains('show')) {
                                description.classList.remove('show');
                            } else {
                                description.classList.add('show');
                                reviews.classList.remove('show');
                            }
                        });

                        document.getElementById('btnReviews').addEventListener('click', function() {
                            var reviews = document.getElementById('collapseReviews');
                            var description = document.getElementById('collapseDescription');
                            if (reviews.classList.contains('show')) {
                                reviews.classList.remove('show');
                            } else {
                                reviews.classList.add('show');
                                description.classList.remove('show');
                            }
                        });
                    </script>
                <?php endif; ?>

                <div class="row mt-5">
                    <div class="col-12">
                        <h6 style="font-family: 'Poppins', sans-serif; font-size: 18px; color: #333;"><strong>You may also like</strong></h6>
                        <div class="row">
                            <?php
                            $related_sql = "
                            SELECT * 
                            FROM products 
                            WHERE category = :category 
                            AND product_id != :product_id 
                            AND stock_quantity > 0 
                            ORDER BY (subcategory = 'discount') DESC, discounted_price ASC 
                            LIMIT 4;
                            ";
                            $related_stmt = $conn->prepare($related_sql);
                            $related_stmt->bindParam(':category', $product['category'], PDO::PARAM_STR);
                            $related_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                            $related_stmt->execute();
                            $related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php if ($related_products): ?>
                                <?php foreach ($related_products as $related_product): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card related-product-card">
                                            <img src="products/<?php echo htmlspecialchars($related_product['image']); ?>" class="card-img-top"
                                                alt="<?php echo htmlspecialchars($related_product['product_name']); ?>"
                                                style="height: 170px; object-fit: cover;">
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title"><?php echo htmlspecialchars($related_product['product_name']); ?></h5>
                                                <?php if ($related_product['subcategory'] === 'discount' && isset($related_product['discounted_price']) && $related_product['discounted_price'] < $related_product['price']): ?>
                                                    <p class="text-muted"><del>$<?php echo number_format($related_product['price'], 2); ?></del></p>
                                                    <p class="card-text text-danger"><strong>$<?php echo number_format($related_product['discounted_price'], 2); ?></strong></p>
                                                <?php else: ?>
                                                    <p class="card-text">$<?php echo number_format($related_product['price'], 2); ?></p>
                                                <?php endif; ?>
                                                <a href="product_details.php?product_id=<?php echo $related_product['product_id']; ?>" class="btn btn-primary mt-auto">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No related products found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const thumbnails = document.querySelectorAll('.thumbnail-img');
                const mainImage = document.getElementById('main-image');

                thumbnails.forEach((thumbnail, index) => {
                    thumbnail.addEventListener('click', () => {
                        mainImage.src = thumbnail.src;

                        thumbnails.forEach(thumb => thumb.style.border = '1px solid #ddd');
                        thumbnail.style.border = '2px solid #007bff';
                    });
                });
            });
        </script>
        <?php include 'footer.php'; ?>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>