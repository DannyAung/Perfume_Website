-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2025 at 10:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecom_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(5, 'admin', 'admin@gmail.com', '$2y$10$rCHk.ft.vUMyTLY.E0Ia0ufU52ccRla8pqgHuvLp1JL8AKv8gDAtm', '2025-01-15 03:07:44', '2025-01-15 03:38:15');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ordered_status` enum('ordered','not_ordered') DEFAULT 'not_ordered',
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `user_id`, `product_id`, `quantity`, `added_at`, `ordered_status`, `order_id`) VALUES
(284, 22, 55, 6, '2025-01-23 04:14:09', 'ordered', NULL),
(286, 22, 53, 1, '2025-01-23 04:21:14', 'ordered', NULL),
(287, 22, 56, 1, '2025-01-23 04:22:09', 'ordered', NULL),
(304, 22, 71, 2, '2025-01-24 17:56:56', 'ordered', NULL),
(305, 22, 55, 1, '2025-01-27 11:28:43', 'ordered', NULL),
(306, 22, 71, 1, '2025-01-27 11:34:39', 'ordered', NULL),
(307, 22, 68, 2, '2025-01-27 11:47:50', 'ordered', NULL),
(308, 22, 54, 1, '2025-01-27 11:50:54', 'ordered', NULL),
(309, 22, 62, 1, '2025-01-27 11:54:46', 'ordered', NULL),
(310, 22, 55, 1, '2025-01-27 12:15:45', 'ordered', NULL),
(311, 22, 54, 1, '2025-01-27 12:30:34', 'ordered', NULL),
(312, 22, 54, 1, '2025-01-27 12:31:24', 'ordered', NULL),
(313, 13, 55, 1, '2025-01-27 15:14:33', 'ordered', NULL),
(314, 13, 62, 1, '2025-01-27 15:14:37', 'ordered', NULL),
(315, 13, 69, 1, '2025-01-27 15:14:46', 'ordered', NULL),
(316, 13, 66, 1, '2025-01-27 15:14:51', 'ordered', NULL),
(317, 22, 62, 1, '2025-01-27 15:15:46', 'ordered', NULL),
(318, 22, 74, 1, '2025-01-27 15:15:48', 'ordered', NULL),
(319, 22, 82, 1, '2025-01-27 15:15:50', 'ordered', NULL),
(320, 22, 72, 1, '2025-01-27 15:15:52', 'ordered', NULL),
(321, 24, 78, 1, '2025-01-27 15:16:46', 'ordered', NULL),
(322, 24, 75, 1, '2025-01-27 15:16:47', 'ordered', NULL),
(323, 24, 90, 1, '2025-01-27 15:16:50', 'ordered', NULL),
(324, 30, 68, 1, '2025-01-27 15:17:44', 'ordered', NULL),
(325, 30, 69, 1, '2025-01-27 15:19:42', 'ordered', NULL),
(331, 33, 71, 2, '2025-01-29 20:49:29', 'ordered', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unread` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`chat_id`, `user_id`, `admin_id`, `message`, `sender`, `sent_at`, `unread`) VALUES
(109, 22, NULL, 'Hey', 'user', '2025-01-24 04:30:08', 1),
(110, 22, NULL, 'Hi How can i help you?', 'admin', '2025-01-25 16:21:29', 1),
(111, 33, NULL, 'Hello', 'user', '2025-01-29 20:21:10', 1),
(112, 33, NULL, 'Hello! how can i help you', 'admin', '2025-01-29 20:22:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `reply_status` enum('Pending','Replied') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`, `admin_reply`, `replied_at`, `reply_status`) VALUES
(4, 'Chaw Nadi Aung', 'chaw@gmail.com', 'Hey! I have problem in creating new account.', '2025-01-24 04:25:21', NULL, NULL, 'Pending'),
(5, 'Chaw Nadi Aung', 'chaw@gmail.com', 'Hello. Can i ask something', '2025-01-29 20:20:01', NULL, NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(11) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `coupon_image` varchar(255) DEFAULT NULL,
  `discount_percentage` decimal(10,2) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `minimum_purchase_amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `coupon_code`, `coupon_image`, `discount_percentage`, `valid_from`, `valid_to`, `minimum_purchase_amount`) VALUES
(56, 'BOGO', 'uploads/coupons/BOGO_coupon.png', 50.00, '2025-01-26', '2025-01-30', 1000.00),
(57, 'SAVE20', 'uploads/coupons/SAVE20_coupon.png', 20.00, '2025-01-25', '2025-05-25', 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','shipped','cancelled','delivered') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `status`, `created_at`, `updated_at`, `admin_id`, `payment_method`, `shipping_method`, `shipping_fee`, `address`, `phone`, `name`, `email`, `discount_percentage`, `coupon_code`, `coupon_id`) VALUES
(1013, 13, 391.20, 'pending', '2024-11-16 15:15:32', '2024-11-16 15:15:32', NULL, 'Cash On Delivery', 'Express', 40.00, 'Yangon', '09885844357', 'Thiri', 'chaw@gmail.com', 20.00, 'SAVE20', 57),
(1014, 22, 677.00, 'pending', '2024-12-16 15:15:32', '2024-12-16 15:15:32', NULL, 'Cash On Delivery', 'Standard', 20.00, 'Yangon', '09885844356', 'Chaw Nadi Aung', 'chawnadi@gmail.com', 0.00, NULL, NULL),
(1015, 24, 617.60, 'pending', '2025-01-27 15:17:25', '2025-01-27 15:17:25', NULL, 'Cash On Delivery', 'Standard', 20.00, 'Yangon', '09885844356', 'Chaw Nadi Aung', 'lynn@gmail.com', 20.00, 'SAVE20', 57),
(1016, 30, 420.00, 'pending', '2025-01-26 15:18:06', '2025-01-26 15:18:06', NULL, 'Cash On Delivery', 'Express', 40.00, 'Yangon', '09885844356', 'Nyi Say', 'nyisay@gmail.com', 0.00, NULL, NULL),
(1017, 30, 330.00, 'pending', '2025-01-27 15:20:06', '2025-01-27 15:20:06', NULL, 'KPay', 'Express', 40.00, 'Yangon', '09969348251', 'Nyi Say', 'nyisay@gmail.com', 0.00, NULL, NULL),
(1018, 33, 500.00, 'completed', '2025-01-29 20:55:31', '2025-01-29 21:01:17', NULL, 'Cash On Delivery', 'Standard', 20.00, 'Yangon', '09885844356', 'Chaw ', 'chaw@gmail.com', 20.00, 'SAVE20', 57);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`, `product_name`, `size`) VALUES
(176, 1013, 55, 1, 38.00, 'Calvin Klein', '100ml'),
(177, 1013, 62, 1, 54.00, 'My Way', '90ml'),
(178, 1013, 69, 1, 250.00, 'Boss The Scent By Hugo Boss', '200ml'),
(179, 1013, 66, 1, 97.00, 'Nautica Voyage', '100ml'),
(180, 1014, 54, 1, 57.00, 'Zara Deep Garden', '100ml'),
(181, 1014, 62, 1, 54.00, 'My Way', '90ml'),
(182, 1014, 74, 1, 190.00, 'Versace Yellow Diamond', '100ml'),
(183, 1014, 82, 1, 180.00, 'YSL Moon Paris', '100ml'),
(184, 1014, 72, 1, 156.00, 'Ladies Delina La Rosee by Perfume De Merly', '100ml'),
(185, 1015, 78, 1, 213.00, 'Explore by Montblanc', '100ml'),
(186, 1015, 75, 1, 200.00, 'Acqua Di Gio', '100ml'),
(187, 1015, 90, 1, 334.00, 'Dolce and Gabbana', '100ml'),
(188, 1016, 68, 1, 340.00, 'Jean Paul Elixir', '100ml'),
(189, 1017, 69, 1, 250.00, 'Boss The Scent By Hugo Boss', '200ml'),
(190, 1018, 71, 2, 300.00, 'Nishane', '100ml');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `payment_method`, `created_at`, `updated_at`) VALUES
(2, 'Cash On Delivery', '2025-01-27 11:28:34', '2025-01-27 11:28:34'),
(3, 'KPay', '2025-01-27 11:33:17', '2025-01-27 11:33:17'),
(4, 'Credit Card', '2025-01-27 11:33:39', '2025-01-27 15:30:20');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` enum('Men','Women','Unisex') NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `extra_image_1` varchar(255) DEFAULT NULL,
  `extra_image_2` varchar(255) DEFAULT NULL,
  `discount_available` enum('Yes','No') DEFAULT 'No',
  `discount_percentage` int(11) DEFAULT 0,
  `discounted_price` decimal(10,2) DEFAULT 0.00,
  `subcategory` enum('discount','popular','latest','featured') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `image`, `stock_quantity`, `created_at`, `updated_at`, `category`, `admin_id`, `size`, `rating`, `extra_image_1`, `extra_image_2`, `discount_available`, `discount_percentage`, `discounted_price`, `subcategory`) VALUES
(53, 'Versace Eros ', 'Versace Eros \r\nA powerful and seductive fragrance, Versace Eros EDP blends fresh citrus with warm, oriental and woody notes. With its vibrant mint, green apple, and lemon top notes, followed by tonka bean, geranium, and ambroxan, it leaves a lasting impression of vanilla, cedarwood, and oakmoss. A fragrance of strength, passion, and irresistible allure.\r\nperfect for leaving a lasting impression.\r\n\r\nConcentration: Eau de Parfum\r\n\r\nTop Notes: Mint, Green Apple, Lemon\r\nHeart Notes: Tonka Bean, Ambroxan, Geranium\r\nBase Notes: Vanilla, Cedarwood, Oakmoss', 120.00, 'versace_eros_main.png', 5, '2025-01-22 03:36:20', '2025-01-23 11:18:07', 'Men', NULL, '100', 0, 'versace_eros_extra1.png', 'versace_eros_extra2.png', 'No', 0, 0.00, 'latest'),
(54, 'Zara Deep Garden', 'Zara Deep Garden\r\nZara Deep Garden is an embodiment of nature’s most fragrant blossoms and verdant greens, delicately blended to create an unforgettable scent. With its refreshing yet earthy composition, this perfume offers a unique way to experience the serenity of a blooming garden. Wear it for an invigorating, sophisticated touch to your everyday style. Its timeless fragrance will leave a lasting impression wherever you go.\r\n\r\nConcentration: Eau de Parfum\r\n\r\nTop Notes: Mint, Green Apple, Lemon\r\nHeart Notes: Tonka Bean, Ambroxan, Geranium\r\nBase Notes: Vanilla, Cedarwood, Oakmoss', 60.00, 'zara_deep.jpg', 2, '2025-01-22 03:52:21', '2025-01-27 15:16:18', 'Women', NULL, '100ml', 0, 'zara_deep_extra1.jpg', 'zara_deep_extra2.jpg', 'Yes', 5, 57.00, 'discount'),
(55, 'Calvin Klein', 'Calvin Klein\r\nCalvin Klein EDT is a timeless, versatile fragrance that combines freshness and elegance, perfect for any occasion. Its light concentration of 5-15% ensures a subtle yet lasting impression throughout the day.\r\n\r\nConcetration: Eau De Toilette\r\n\r\nTop Notes: Fresh Bergamot, Lemon, Pineapple\r\nMid Notes: Aromatic Lavender, Jasmine, Nutmeg\r\nBase Notes: Warm Musk, Cedarwood, Amber', 40.00, 'ck.jpg', 2, '2025-01-22 04:52:09', '2025-01-27 15:15:32', 'Unisex', NULL, '100ml', 0, 'ck_extra1.jpg', 'ck_extra2.jpg', 'Yes', 5, 38.00, 'discount'),
(56, 'Jo Malone Peony and Blush Suede Cologne', 'Jo Malone Peony and Blush Suede Cologne\r\nDiscover the charm of Peony &amp; Blush Suede, a delicate and luxurious cologne that captures the essence of blooming florals and soft, elegant textures. This enchanting fragrance opens with a burst of juicy red apple, leading to a heart of opulent peony, jasmine, and rose. A warm base of blush suede adds a sophisticated and sensual finish.\r\n\r\nTop Notes: Red Apple\r\nMid Notes: Peony, Jasmine, Carnation, Rose\r\nBase Notes: Soft Suede', 165.00, 'jomalone.jpg', 5, '2025-01-22 04:58:53', '2025-01-23 10:43:25', 'Men', NULL, '100ml', 0, 'jomalone_extra1.jpg', 'jomalone_extra2.jpg', 'Yes', 10, 148.50, 'discount'),
(62, 'My Way', 'My Way EDP by Giorgio Armani is a radiant and modern fragrance that embodies discovery and meaningful connections. Crafted for the sophisticated and adventurous soul, it combines floral elegance with a touch of warmth.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Bergamot, Orange Blossom\r\nMid Notes: Tuberose, Jasmine\r\nBase Notes: Vanilla, Cedarwood, White Musk', 60.00, 'myway.jpg', 2, '2025-01-22 05:24:27', '2025-01-27 15:16:18', 'Women', NULL, '90ml', 0, 'myway_extra1.jpg', 'myway_extra2.jpg', 'Yes', 10, 54.00, 'discount'),
(63, 'Giorgio Armani Acqua Di Gio', 'Discover the essence of nature’s power with Acqua di Giò EDP by Giorgio Armani, a refined and modern interpretation of the iconic classic. This fragrance embodies fresh aquatic notes blended with earthy warmth, perfect for the contemporary man who values elegance and sustainability.\r\n\r\nConcentration: Eau De Perfume\r\n\r\nTop Notes: Green Mandarin, Marine Notes\r\nMid Notes: Clary Sage, Lavender, Geranium\r\nBase Notes: Patchouli, Vetiver, Mineral Amber, Musk', 120.00, 'ACD_main.jpg', 5, '2025-01-23 05:11:21', '2025-01-23 05:12:49', 'Men', NULL, '100ml', 0, 'ADG.jpg', 'ACD_extra2.png', 'Yes', 5, 114.00, 'discount'),
(64, 'Gucci Guilty', 'Gucci Guilty EDT is a bold and captivating fragrance that challenges conventions with its fresh and aromatic composition. This scent is designed for the modern, confident woman who dares to be different, embodying freedom and allure.\r\n\r\nConcentration: Eau De Toilette\r\n\r\nTop Notes: Mandarin, Pink Pepper\r\nMid Notes: Geranium, Peach\r\nBase Notes: Patchouli, Amber, Musk', 170.00, 'Gucci_Guilty.jpg', 10, '2025-01-23 05:18:00', '2025-01-23 11:51:00', 'Men', NULL, '90ml', 0, 'Gucci_Guilty_extra1.jpg', 'gucci_guilty_extra2.jpg', 'Yes', 10, 153.00, 'discount'),
(66, 'Nautica Voyage', 'Nautica Voyage EDT is a refreshing and invigorating fragrance that captures the spirit of the open sea. This vibrant scent evokes the feeling of a cool ocean breeze, perfect for adventurous souls who seek freedom and exploration.\r\n\r\nConcentration: Eau De Toilette\r\n\r\nTop Notes: Apple, Green Leaf, Watery Notes\r\nMid Notes: Lotus, Mimosa, Jasmine\r\nBase Notes: Musk, Cedarwood, Amber', 122.00, '1.jpg', 5, '2025-01-23 10:35:43', '2025-01-27 15:15:32', 'Men', NULL, '100ml', 0, 'nautica.jpg', 'nautica2.jpg', 'Yes', 20, 97.00, 'discount'),
(67, 'Dior Jadore', 'Dior J&#039;adore Eau de Parfum (EDP)\r\nDior J&#039;adore EDP is an iconic and luxurious fragrance that embodies femininity, elegance, and sophistication. This timeless scent is a celebration of floral beauty, where delicate blossoms blend seamlessly with deep, sensual notes, creating a refined and radiant aura.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Ylang-Ylang, Bergamot, Pear\r\nMid Notes: Rose, Jasmine, Orchid\r\nBase Notes: Musk, Cedarwood, Vanilla', 138.00, 'jadore.jpg', 3, '2025-01-23 10:45:13', '2025-01-23 11:51:12', 'Women', NULL, '90ml', 0, 'jadore2.jpg', 'jadore1.jpg', 'Yes', 5, 131.00, 'discount'),
(68, 'Jean Paul Elixir', 'Jean Paul Gaultier Elixir\r\nJean Paul Gaultier Elixir is a daring and sensual fragrance that intensifies the classic Jean Paul Gaultier scent with a rich and bold twist. This luxurious perfume is designed for those who exude confidence and allure, enveloping you in a captivating blend of warmth and depth.\r\n\r\nConcentration: Elixir\r\n\r\nTop Notes: Blood Orange, Bergamot\r\nMid Notes: Jasmine, Almond, Ylang-Ylang\r\nBase Notes: Sandalwood, Vanilla, Musk', 340.00, 'JPG.jpg', 17, '2025-01-23 10:49:56', '2025-01-27 15:18:06', 'Men', NULL, '100ml', 0, 'JPG1.jpg', 'JPG2.jpg', 'No', 0, 0.00, 'featured'),
(69, 'Boss The Scent By Hugo Boss', 'Boss The Scent Eau de Toilette (EDT) by Hugo Boss\r\nBoss The Scent EDT by Hugo Boss is a captivating fragrance that exudes confidence, warmth, and sophistication. With its lighter 5-15% concentration, this Eau de Toilette offers a refreshing yet alluring scent, perfect for daytime or evening wear.\r\n\r\nConcentration: Eau De Toilette\r\n\r\nTop Notes: Ginger, Bergamot\r\nMid Notes: Maninka Fruit, Lavender\r\nBase Notes: Leather, Woody Notes, Tonka Bean', 250.00, 'boss.jpg', 23, '2025-01-23 10:54:37', '2025-01-27 15:20:06', 'Men', NULL, '200ml', 0, 'boss1.jpg', 'boss2.jpg', 'No', 0, 0.00, 'featured'),
(70, 'Versace Eros ', 'Versace Eros Eau de Parfum (EDP)\r\nVersace Eros EDP is a bold and seductive fragrance that captures the essence of desire, passion, and strength. With its rich concentration of 15-20%, this Eau de Parfum creates a powerful, long-lasting scent that makes a lasting impression.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Mint, Green Apple, Lemon\r\nMid Notes: Tonka Bean, Ambroxan, Geranium\r\nBase Notes: Vanilla, Cedarwood, Oakmoss', 115.00, 'versace_eros_edp.jpg', 5, '2025-01-23 10:57:16', '2025-01-23 11:44:12', 'Men', NULL, '100ml', 0, 'eros_edp1.jpg', 'eros_edp2.jpg', 'No', 0, 0.00, 'featured'),
(71, 'Nishane', 'Nishane Extrait de Parfum\r\nNishane Extrait de Parfum is a luxurious and opulent fragrance that stands as a true work of art in the world of perfumery. With its rich concentration of 20-30%, this extrait delivers an intense, long-lasting scent that lingers beautifully on the skin, creating a unique and unforgettable presence.\r\n\r\nConcentration: Extrait De Perfum\r\n\r\nTop Notes: Bergamot, Lemon, Green Notes\r\nMid Notes: Rose, Jasmine, Oud\r\nBase Notes: Amber, Musk, Sandalwood, Patchouli', 300.00, 'nishane.jpg', 2, '2025-01-23 11:00:26', '2025-01-29 20:55:31', 'Men', NULL, '100ml', 0, 'nishane1.jpg', 'nishane2.jpg', 'No', 0, 0.00, 'featured'),
(72, 'Ladies Delina La Rosee by Perfume De Merly', 'Delina La Rosée Eau de Parfum (EDP)\r\nDelina La Rosée by Parfums de Marly is a refreshing, delicate, and feminine fragrance that captures the essence of spring in full bloom. A lighter, more airy version of the original Delina, this fragrance offers a vibrant and fresh interpretation, making it perfect for those who seek a soft, elegant, and refined scent.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Litchi, Pear, Bergamot\r\nMid Notes: Turkish Rose, Peony, Jasmine\r\nBase Notes: Musk, Cedarwood, Cashmeran, Vanilla', 156.00, 'rosee.jpg', 6, '2025-01-23 11:06:23', '2025-01-27 15:16:18', 'Women', NULL, '100ml', 0, 'delina1.jpg', 'delina2.jpg', 'No', 0, 0.00, 'featured'),
(73, 'CoCo Medimoiselle ', 'Coco Mademoiselle Eau de Parfum (EDP)\r\nChanel Coco Mademoiselle EDP is an elegant and sophisticated fragrance that exudes modern femininity with a touch of timeless allure. This iconic scent, with its perfect balance of fresh and oriental notes, is ideal for the confident woman who embodies both grace and strength.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Orange, Bergamot, Grapefruit\r\nMid Notes: Jasmine, Rose, Litchi\r\nBase Notes: Patchouli, Vanilla, White Musk, Tonka Bean', 220.00, 'coco.jpg', 9, '2025-01-23 11:10:54', '2025-01-23 11:45:21', 'Women', NULL, '100ml', 0, 'coco1.jpg', 'coco.jpg', 'No', 0, 0.00, 'featured'),
(74, 'Versace Yellow Diamond', 'Versace Yellow Diamond Eau de Toilette (EDT)\r\nVersace Yellow Diamond EDT is a radiant and sparkling fragrance that captures the essence of elegance, luxury, and femininity. Inspired by the brilliance of yellow diamonds, this scent offers a fresh and uplifting experience, perfect for the modern woman who loves to shine with grace.\r\n\r\nConcentration: Eau De Toilette\r\n\r\nTop Notes: Lemon, Bergamot, Neroli, Pear\r\nMid Notes: Freesia, Mimosa, Orange Blossom, Lotus\r\nBase Notes: Amber, Cedarwood, Musk, Guaïac Wood', 190.00, 'versaceyellow.jpg', 4, '2025-01-23 11:14:27', '2025-01-27 15:16:18', 'Women', NULL, '100ml', 0, 'vc1.jpg', '620e59f8eba54031d6a75b4fbb7fcc16.jpg', 'No', 0, 0.00, 'featured'),
(75, 'Acqua Di Gio', 'Acqua di Giò Eau de Toilette (EDT)\r\nAcqua di Giò EDT by Giorgio Armani is a fresh, aquatic fragrance that embodies the spirit of freedom and nature. Inspired by the Mediterranean, this iconic scent is a blend of vibrant citrus and aromatic herbs, creating a light yet invigorating fragrance perfect for the modern man.\r\n\r\nConcentration: Eau De Toilette\r\n\r\nTop Notes: Lemon, Lime, Orange, Bergamot\r\nMid Notes: Jasmine, Rosemary, Persimmon\r\nBase Notes: Cedarwood, Patchouli, White Musk', 200.00, '7d95990f7742c601e29c788af3a3f34e.jpg', 32, '2025-01-23 11:16:56', '2025-01-27 15:17:25', 'Unisex', NULL, '100ml', 0, 'ADG.jpg', '8587b9af4baa174b29faeb657f0954f7.jpg', 'No', 0, 0.00, 'featured'),
(76, 'Jean Paul Gaultier Ultra Male', 'Jean Paul Gaultier Ultra Male Eau de Toilette (EDT)\r\nJean Paul Gaultier Ultra Male EDT is a powerful and seductive fragrance that exudes boldness and intensity. This fresh yet spicy scent is perfect for the modern man who seeks to make a lasting impression, offering a refined blend of sweetness and strength.\r\n\r\nConcentration: Eau De Toilette\r\n\r\nTop Notes: Pear, Bergamot, Lavender\r\nMid Notes: Mint, Cinnamon, Caraway\r\nBase Notes: Vanilla, Amber, Cedarwood, Tonka Bean', 223.00, '8698b0c4f601bf63714f398c6cd142cc.jpg', 8, '2025-01-23 11:24:11', '2025-01-23 11:51:42', 'Men', NULL, '100ml', 0, '51kllSL_8DL_removebg_preview.png', 'ee4aa1eeb5fa250cae3f659bae317b4d.jpg', 'No', 0, 0.00, 'latest'),
(77, 'Dior Elixir', 'Dior Elixir\r\nDior Elixir is a luxurious and intense fragrance that amplifies the essence of Dior’s signature scents with a more concentrated and opulent formulation. This enchanting fragrance is designed for the confident and sophisticated individual who seeks a bold and lasting presence.\r\n\r\nConcentration: Elixir\r\n\r\nTop Notes: Bergamot, Grapefruit\r\nMid Notes: Rose, Jasmine, Iris\r\nBase Notes: Patchouli, Vanilla, Amber, Musk', 300.00, 'image_removebg_preview__2_.png', 9, '2025-01-23 11:25:41', '2025-01-23 11:51:52', 'Men', NULL, '100ml', 0, '33464dac1ec97ee05209b3c953efa404.jpg', 'image_removebg_preview__2_.png', 'No', 0, 0.00, 'latest'),
(78, 'Explore by Montblanc', 'Explore by Montblanc Eau de Parfum (EDP)\r\nExplore by Montblanc EDP is a captivating and adventurous fragrance that evokes a sense of freedom and discovery. With a 15-20% concentration, this Eau de Parfum is intense, long-lasting, and perfect for the man who dares to explore new horizons.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Bergamot, Pink Pepper, Clary Sage\r\nMid Notes: Vetiver, Leather, Ambery Woods\r\nBase Notes: Patchouli, Cocoa, Tonka Bean', 213.00, '82bddf4547df8b97d8709cdd4c7f13da.jpg', 7, '2025-01-23 11:28:20', '2025-01-27 15:17:25', 'Unisex', NULL, '100ml', 0, '4972ac9f86743787ca420b3ecb7504d7.jpg', '82bddf4547df8b97d8709cdd4c7f13da.jpg', 'No', 0, 0.00, 'latest'),
(79, 'Carlisle Perfum De Marly', 'Carlisle Eau de Parfum (EDP) by Parfums de Marly\r\nCarlisle EDP by Parfums de Marly is a rich and indulgent fragrance that embodies opulence and sophistication. This warm, oriental scent is designed for the confident individual who appreciates luxurious, complex compositions. With its bold and enticing notes, Carlisle leaves a lasting impression wherever it’s worn.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Green Apple, Nutmeg, Bergamot\r\nMid Notes: Violet, Jasmine, Rose\r\nBase Notes: Amber, Patchouli, Vanilla, Tonka Bean', 166.00, '0041dc673b93d35a2720dd2e9ecf5cd8.jpg', 20, '2025-01-23 11:30:47', '2025-01-23 11:52:15', 'Men', NULL, '100ml', 0, '4b6d117525cc61576159ed6201090719.jpg', 'e42a4de1ac1c8e74c364292d4a3a561a.jpg', 'No', 0, 0.00, 'latest'),
(80, 'YSL Saint Laurent', 'Yves Saint Laurent Black Opium Eau de Parfum (EDP)\r\nYSL Black Opium EDP is a captivating, bold fragrance that embodies a sense of mystery, sensuality, and addiction. With its rich, intoxicating notes, this fragrance is perfect for the modern, confident woman who enjoys making a statement and exuding an air of sophistication and allure.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Coffee, Pink Pepper, Orange Blossom\r\nMid Notes: Jasmine, Almond, Licorice\r\nBase Notes: Vanilla, Patchouli, Cedarwood, White Musk', 170.00, '7c713d90170d6f0de3a250d58e7bd196.jpg', 29, '2025-01-23 11:35:25', '2025-01-23 11:52:27', 'Unisex', NULL, '200ml', 0, 'f13e32687bc699e8c1f0860b34670648.jpg', 'ae8672a7924fbffa32402a63a9603227.jpg', 'No', 0, 0.00, 'latest'),
(81, 'Stronger With You', 'Stronger With You Eau de Parfum (EDP) by Giorgio Armani\r\nStronger With You EDP by Giorgio Armani is a warm, spicy, and woody fragrance that exudes strength, passion, and masculinity. This scent is designed for the modern man who embraces his emotional depth while maintaining confidence and charm. With its rich composition, Stronger With You leaves a lasting impression of warmth and sensuality.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Pink Pepper, Cardamom, Violet Leaf\r\nMid Notes: Sage, Cinnamon, Nutmeg\r\nBase Notes: Vanilla, Chestnut, Amberwood, Suede', 310.00, '6c780373620f9db9a790ea2e69aac263.jpg', 26, '2025-01-23 11:37:47', '2025-01-23 20:31:04', 'Men', NULL, '100ml', 0, '886c8468995e7e04717c89fd49d0af92.jpg', '41f939643339bdc3892d413d02d03aa8.jpg', 'No', 0, 0.00, 'latest'),
(82, 'YSL Moon Paris', 'Yves Saint Laurent Mon Paris Eau de Parfum (EDP)\r\nMon Paris by Yves Saint Laurent is a passionate and seductive fragrance that captures the essence of love in Paris. Inspired by the city of romance, this fragrance combines fresh, floral, and sweet notes to create an unforgettable scent for the bold, modern woman.\r\n\r\nConcentration: Eau De Perfum\r\nTop Notes: Strawberry, Raspberry, Pear, Calabrian Bergamot\r\nMid Notes: Jasmine Sambac, Orange Blossom, Datura\r\nBase Notes: Patchouli, White Musk, Amber, Vanilla', 180.00, '5e950d59e20301512b4e98dd5f6b320b.jpg', 5, '2025-01-23 11:41:43', '2025-01-27 15:16:18', 'Women', NULL, '100ml', 0, '4ece24e849bd287c1f322afba14ffccf.jpg', 'd8dfec7368d57102de671b1279bc3ee6.jpg', 'No', 0, 0.00, 'latest'),
(83, 'Valentino Uomo Born in Roma Intense', 'Valentino Uomo Born in Roma Intense Eau de Parfum (EDP)\r\nValentino Uomo Born in Roma Intense is a bold and captivating fragrance that embodies modern sophistication and effortless style. With its luxurious and magnetic composition, this scent is designed for the confident man who lives life with intensity and elegance.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Vanilla, Spicy Ginger\r\nMid Notes: Lavandin, Clary Sage\r\nBase Notes: Vetiver, Woody Accord', 244.00, '580a4833530ce9789085780a130755d0.jpg', 44, '2025-01-23 19:06:15', '2025-01-27 17:06:15', 'Men', NULL, '100ml', 0, '1061081a03cd1daaa571d976053325a1.jpg', '1061081a03cd1daaa571d976053325a1.jpg', 'No', 0, 0.00, 'popular'),
(84, 'LV Imagination', 'Imagination Eau de Parfum (EDP) by Louis Vuitton\r\nImagination by Louis Vuitton is a luminous and sophisticated fragrance that celebrates the art of creativity and exploration. This fresh and elegant scent combines citrusy brightness with warm, aromatic undertones, evoking a sense of boundless possibility and refined luxury.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Calabrian Bergamot, Sicilian Orange, Lemon\r\nMid Notes: Tunisian Neroli, Black Tea, Ginger\r\nBase Notes: Ambroxan, Incense, Cedarwood', 244.00, '7121bb9b9582611cb9b85adf98bdb2c7.jpg', 4, '2025-01-23 19:20:32', '2025-01-23 19:22:49', 'Men', NULL, '100ml', 0, '0eb6e5c944ed8ee1bddb63459de75309.jpg', '2742ca47ccf99023f983433c4f73900c.jpg', 'No', 0, 0.00, 'popular'),
(85, 'Blue De Chanel', 'Bleu de Chanel Eau de Parfum (EDP)\r\nBleu de Chanel EDP is a timeless and sophisticated fragrance that embodies freedom, confidence, and elegance. Designed for the modern man, this scent is a harmonious blend of fresh, woody, and aromatic notes, offering depth and refinement for any occasion.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Grapefruit, Lemon, Mint, Pink Pepper\r\nMid Notes: Ginger, Jasmine, Nutmeg\r\nBase Notes: Sandalwood, Cedarwood, Amber, Incense', 155.00, '040ddcd7e7134af2d03c4ab992444e60.jpg', 22, '2025-01-23 19:24:23', '2025-01-23 19:29:42', 'Men', NULL, '100ml', 0, '994c14e2a0eeded32c47981f8dadba96.jpg', '040ddcd7e7134af2d03c4ab992444e60.jpg', 'No', 0, 0.00, 'popular'),
(86, 'Parada Luna Rossa Ocean', 'dsds', 155.00, 'c2c33ad2a78be3bfea3c096e00aea481.jpg', 12, '2025-01-23 19:31:59', '2025-01-23 19:36:38', 'Men', NULL, '100ml', 0, '30baeb96c69459cd3691dc4db99f3a07.jpg', 'ba2e6403d4867e89bbbda03b85d25b13.jpg', 'No', 0, 0.00, 'popular'),
(87, 'Parada Paraddoxe Intense', 'Prada Paradoxe Intense Eau de Parfum (EDP)\r\nPrada Paradoxe Intense EDP is a daring and sensual fragrance that celebrates the multi-faceted nature of femininity. This captivating scent blends floral, amber, and woody notes to create a bold yet refined experience, perfect for the modern woman who embraces her contradictions with confidence and grace.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Neroli, Calabrian Bergamot, Pear\r\nMid Notes: Jasmine Sambac, Orange Blossom, Amber\r\nBase Notes: Bourbon Vanilla, Musk, Benzoin', 144.00, '1106f1f4831737fff70a14ef6a058c79.jpg', 5, '2025-01-23 19:40:37', '2025-01-23 19:44:48', 'Women', NULL, '90ml', 0, 'a327968e83d072973fefd081038d76cf.jpg', '3cf35484e8d4b64fb9ebb973fa4e504d.jpg', 'No', 0, 0.00, 'popular'),
(89, 'Miss Dior', 'Miss Dior Eau de Parfum (EDP)\r\nMiss Dior EDP is a timeless and elegant fragrance that captures the essence of femininity with its sophisticated and romantic composition. This iconic scent blends floral and citrus notes to create a fresh yet sensual experience, embodying grace and modernity with every spray.\r\n\r\nConcentration: Eau De Perfum\r\n\r\nTop Notes: Italian Mandarin, Blood Orange, Bergamot\r\nMid Notes: Grasse Rose, Peony, Damask Rose\r\nBase Notes: Patchouli, Musk, Vanilla\r\n', 233.00, '1733495088_41ueuwaqVQL_removebg_preview.png', 3, '2025-01-23 19:52:54', '2025-01-23 20:23:26', 'Women', NULL, '100ml', 0, '371638b3b697f13972f94dfdcce9fe94.jpg', '670c503319a2962bcc395ff507bd9090.jpg', 'No', 0, 0.00, 'popular'),
(90, 'Dolce and Gabbana', 'Dolce &amp; Gabbana The Only One Eau de Parfum (EDP)\r\nThe Only One by Dolce &amp; Gabbana is a captivating, modern fragrance that celebrates elegance and sophistication. This warm, floral scent is a perfect balance of sweetness and depth, designed for the confident woman who knows her power and allure.\r\n\r\nConcentration: Eau De Perfum\r\n\r\n\r\nTop Notes: Violet, Bergamot, Orange\r\nMid Notes: Coffee, Iris, Rose\r\nBase Notes: Vanilla, Caramel, Patchouli, Cedarwood', 334.00, '4b72aa741249fccb2772b32075632ab5.jpg', 5, '2025-01-23 19:55:41', '2025-01-27 15:17:25', 'Unisex', NULL, '100ml', 0, 'dc298c82202786b0a17294c8bbaaf9ce.jpg', 'ad88c6389247e00b88458d54b6ed111b.jpg', 'No', 0, 0.00, 'popular'),
(97, 'Mont Blanc Legend Blue', 'Montblanc Legend Blue Eau de Parfum (EDP)\r\nMontblanc Legend Blue EDP is a refined and sophisticated fragrance that embodies the spirit of strength and elegance. With its fresh, woody, and aromatic notes, this fragrance is perfect for the modern man who values both boldness and subtlety.\r\n\r\nDescription: Eau De Perfum\r\n\r\nTop Notes: Bergamot, Lemon, Clary Sage\r\nMid Notes: Lavender, Geranium, Cedarwood\r\nBase Notes: Amber, Tonka Bean, Musk', 166.00, '79b2216c2072b28b17ba234310a0242e.jpg', 22, '2025-01-23 20:28:20', '2025-01-23 20:28:20', 'Unisex', NULL, '100ml', 0, '00d2177c3153c720849d73a68aa965ce.jpg', '1ac195ea28c710a78d8c55f54a45210f.jpg', 'No', 0, 0.00, 'popular');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `product_id`, `review_text`, `rating`, `created_at`, `updated_at`) VALUES
(15, 22, 71, 'The scent is so nice and attractive!', 5, '2025-01-24 12:47:34', '2025-01-24 18:17:34'),
(16, 22, 54, 'The scent is so nice!!', 5, '2025-01-27 07:01:20', '2025-01-27 12:31:20'),
(17, 33, 71, 'I like it', 5, '2025-01-29 15:31:39', '2025-01-29 21:01:39');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `shipping_method` varchar(50) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `delivery_time` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `shipping_method`, `shipping_fee`, `delivery_time`, `created_at`, `updated_at`) VALUES
(1, 'Standard', 20.00, 'Within 1-3 days', '2025-01-10 02:20:38', '2025-01-21 01:55:27'),
(3, 'Express', 40.00, 'Within 1 day', '2025-01-20 15:58:04', '2025-01-20 15:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `user_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `password`, `address`, `phone_number`, `user_image`, `created_at`, `updated_at`, `admin_id`) VALUES
(13, 'Thiri', 'thiri@gmail.com', '$2y$10$ugzAhorAAZe65HPYq4kche6ROvWKqYIIw6Q5T2AMmpwwFH/.CCQAi', 'Yangon', '094501974123', NULL, '2024-12-26 17:07:06', '2025-01-19 19:09:37', NULL),
(22, 'Chaw Nadi Aung', 'chaw@gmail.com', '$2y$10$.H2FTh.T1p0uiazGfWpW3eZ2Q.HkD4UNQOfKQN32db7A0.s6wYOeG', 'Yangon', '09885844357', '678cc51ed34b7_photo_2024-06-01_15-48-36.jpg', '2025-01-18 18:07:51', '2025-01-25 11:23:46', NULL),
(23, 'Ingyin', 'ingyin@gmail.com', '$2y$10$Y9ybou6e2DZEY4vB06aZa.ZyPWy3HGE8MvnDLtNqRIHricpO8ogNG', NULL, NULL, NULL, '2025-01-27 10:51:45', '2025-01-27 10:51:45', NULL),
(24, 'Lynn', 'lynn@gmail.com', '$2y$10$qRR7oZadRNUr/oa6HVP6Se3bYtMJk6WWc8fdStdOmLbqVKQl9G0cK', NULL, NULL, NULL, '2025-01-27 10:52:09', '2025-01-27 10:52:09', NULL),
(25, 'Phoo Nge', 'phoonge@gmail.com', '$2y$10$V11.TPU.jhDtYpNj35NN4.PmTasJAV6g2hVLdrdZRHiXA/IdVEdaG', NULL, NULL, NULL, '2025-01-27 10:53:01', '2025-01-27 10:53:01', NULL),
(26, 'Min Khant', 'minkhant@gmail.com', '$2y$10$BSO7LytM0XeY9Hln9Na1VeLeaq4SUdWxWQpVYFp4olKkCCKmNbMkC', NULL, NULL, NULL, '2025-01-27 10:53:44', '2025-01-27 10:53:44', NULL),
(27, 'Toe Kyi', 'toekyi@gmail.com', '$2y$10$6.dEcU.Um4hG6XBcCt5Rhe6GqsvDCeUZ7bm2H7MmgIolq1uNnjEDS', NULL, NULL, NULL, '2025-01-27 10:54:09', '2025-01-27 10:54:09', NULL),
(28, 'May Mon', 'may@gmail.com', '$2y$10$k2pNVxpDx.X7CJhiu6QN2ud37SDDqikffdj0DhQoyPflSXlbulRk.', NULL, NULL, NULL, '2025-01-27 10:54:35', '2025-01-27 10:54:35', NULL),
(29, 'Nway Thu', 'nway@gmail.com', '$2y$10$EHJfhFhzeAIfS1BI9VhBku047cV1kifoQ.Vo9yppeHsSki6x9k9HK', NULL, NULL, NULL, '2025-01-27 10:54:51', '2025-01-27 10:54:51', NULL),
(30, 'Nyi Say', 'nyisay@gmail.com', '$2y$10$JBpefhwFk/Uyk5IT2iXAZ.fQXW8kDHXbXrBfaW1iMhuS/STUmCF8K', NULL, NULL, NULL, '2025-01-27 10:55:28', '2025-01-27 10:55:28', NULL),
(31, 'Lin Myat', 'lin@gmail.com', '$2y$10$X6i6S7vcC5zwkRGtnDzjouABhJbhuykwNzEdt6UeQDBqzFal.vXYO', NULL, NULL, NULL, '2025-01-27 10:56:03', '2025-01-27 14:39:44', NULL),
(33, 'Chaw', 'chaw1@gmail.com', '$2y$10$XqFN.OBY9IvFpI9dNyZZWuj.GEBJIkVD2ShOg8oRrIDS30EtQzZPu', NULL, NULL, NULL, '2025-01-29 20:18:12', '2025-01-29 21:07:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `product_id`, `added_at`, `date_added`) VALUES
(117, 22, 54, '2025-01-24 18:39:35', '2025-01-24 18:39:35'),
(122, 33, 71, '2025-01-29 20:48:20', '2025-01-29 20:48:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `coupon_id` (`coupon_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_product_admin` (`admin_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_admin` (`admin_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=332;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1019;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
