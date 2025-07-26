-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2025 at 07:44 AM
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
-- Database: `qtime`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`) VALUES
(3, 'Apple'),
(1, 'Danielwellington'),
(2, 'Gshock');

-- --------------------------------------------------------

--
-- Table structure for table `cartitems`
--

CREATE TABLE `cartitems` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `created_at`) VALUES
(5, 15, '2025-04-17 17:45:56'),
(8, 22, '2025-04-25 16:51:22'),
(12, 23, '2025-04-29 01:20:25');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Chronograph'),
(2, 'Digital');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `color_id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`color_id`, `color_name`) VALUES
(1, 'Black'),
(6, 'Blue'),
(5, 'Green'),
(3, 'Silver'),
(8, 'White');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `feature_id` int(11) NOT NULL,
  `feature_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`feature_id`, `feature_name`) VALUES
(2, 'Bluetooth'),
(3, 'Solar'),
(1, 'Waterproof');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('paid','shipping','refund') DEFAULT 'paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`order_id`, `product_id`, `quantity`, `price`, `status`) VALUES
(2013, 2, 1, 788.00, 'paid'),
(2015, 4, 3, 788.00, 'shipping'),
(2016, 3, 1, 566.00, 'paid'),
(2017, 3, 1, 566.00, 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `created_at`) VALUES
(2011, 15, 1000.00, '2025-04-23 01:49:39'),
(2013, 22, 788.00, '2025-04-25 16:58:15'),
(2015, 22, 2364.00, '2025-04-26 17:54:39'),
(2016, 23, 566.00, '2025-04-29 01:20:59'),
(2017, 23, 566.00, '2025-04-29 01:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_logs`
--

CREATE TABLE `order_status_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `old_status` enum('paid','shipping','refund') DEFAULT NULL,
  `new_status` enum('paid','shipping','refund') DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_logs`
--

INSERT INTO `order_status_logs` (`log_id`, `admin_id`, `order_id`, `product_id`, `old_status`, `new_status`, `changed_at`) VALUES
(6, 15, 2015, 4, 'paid', 'shipping', '2025-04-27 06:49:58'),
(7, 15, 2015, 4, 'refund', 'shipping', '2025-04-27 13:34:54');

-- --------------------------------------------------------

--
-- Table structure for table `productfeatures`
--

CREATE TABLE `productfeatures` (
  `product_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productfeatures`
--

INSERT INTO `productfeatures` (`product_id`, `feature_id`) VALUES
(1, 1),
(2, 3),
(3, 3),
(4, 1),
(5, 1),
(6, 1),
(6, 3),
(7, 1),
(8, 1),
(8, 3),
(9, 1),
(9, 3),
(10, 1),
(10, 3),
(11, 1),
(11, 3),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(13, 3),
(14, 1),
(14, 2),
(14, 3),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 3);

-- --------------------------------------------------------

--
-- Table structure for table `productimages`
--

CREATE TABLE `productimages` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productimages`
--

INSERT INTO `productimages` (`image_id`, `product_id`, `image_url`) VALUES
(37, 1, '../image/1_1.png'),
(38, 1, '../image/1_2.jpg'),
(39, 1, '../image/1_3.jpg'),
(40, 1, '../image/1_4.jpg'),
(41, 1, '../image/1_5.jpg'),
(56, 2, '../image/2_1.png'),
(64, 2, '../image/2_2.png'),
(65, 2, '../image/2_3.png'),
(66, 2, '../image/2_4.jpg'),
(67, 2, '../image/2_5.jpg'),
(68, 3, '../image/3_1.png'),
(69, 3, '../image/3_2.png'),
(75, 3, '../image/3_3.png'),
(76, 3, '../image/3_4.png'),
(77, 3, '../image/3_5.png'),
(95, 4, '../image/4_1.png'),
(96, 4, '../image/4_2.png'),
(97, 4, '../image/4_3.png'),
(98, 4, '../image/4_4.png'),
(99, 5, '../image/5_1.png'),
(100, 5, '../image/5_2.png'),
(101, 5, '../image/5_3.png'),
(102, 5, '../image/5_4.png'),
(103, 5, '../image/5_5.png'),
(104, 5, '../image/5_6.png'),
(105, 6, '../image/6_1.png'),
(106, 6, '../image/6_2.png'),
(107, 6, '../image/6_3.png'),
(109, 6, '../image/6_4.png'),
(110, 6, '../image/6_5.png'),
(111, 6, '../image/6_6.png'),
(112, 6, '../image/6_7.png'),
(113, 7, '../image/7_1.png'),
(114, 7, '../image/7_2.png'),
(115, 7, '../image/7_3.png'),
(116, 7, '../image/7_4.png'),
(117, 7, '../image/7_5.png'),
(118, 8, '../image/8_1.png'),
(119, 8, '../image/8_2.png'),
(120, 8, '../image/8_3.png'),
(121, 8, '../image/8_4.png'),
(122, 8, '../image/8_5.png'),
(123, 9, '../image/9_1.png'),
(124, 9, '../image/9_2.png'),
(125, 9, '../image/9_3.png'),
(126, 9, '../image/9_4.png'),
(127, 9, '../image/9_5.png'),
(128, 10, '../image/10_1.png'),
(129, 10, '../image/10_2.png'),
(130, 10, '../image/10_3.png'),
(131, 11, '../image/11_1.png'),
(132, 11, '../image/11_2.png'),
(133, 11, '../image/11_3.png'),
(134, 11, '../image/11_4.png'),
(135, 11, '../image/11_5.png'),
(136, 11, '../image/11_6.png'),
(137, 12, '../image/12_1.png'),
(138, 12, '../image/12_2.png'),
(139, 12, '../image/12_3.png'),
(140, 12, '../image/12_4.png'),
(142, 13, '../image/13_2.png'),
(147, 13, '../image/13_3.png'),
(150, 14, '../image/14_1.png'),
(151, 14, '../image/14_2.png'),
(153, 14, '../image/14_3.png'),
(154, 14, '../image/14_4.png'),
(158, 15, '../image/15_1.png'),
(159, 15, '../image/15_2.png'),
(160, 15, '../image/15_3.png'),
(162, 16, '../image/16_1.png'),
(163, 16, '../image/16_2.png'),
(164, 16, '../image/16_3.png'),
(201, 17, '../image/17_1.jpg'),
(202, 17, '../image/17_2.jpg'),
(203, 17, '../image/17_3.jpg'),
(204, 17, '../image/17_4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `brand_id`, `category_id`, `price`, `stock`, `description`, `created_at`) VALUES
(1, 'DW Iconic Blue Enamel Silver', 1, 1, 888.00, 8, 'The Iconic Paradigma Link draws inspiration from the iconic link.', '2025-04-23 04:18:46'),
(2, 'DW Iconic Link Black Enamel', 1, 1, 788.00, 30, 'The Iconic Paradigma Link draws inspiration from the iconic link', '2025-04-23 04:29:08'),
(3, 'Iconic Link Blue Bezel Silver', 1, 1, 566.00, 9, 'The Iconic Paradigma Link draws inspiration from the iconic link', '2025-04-23 05:01:22'),
(4, 'Iconic Chronograph Link Graphite GM', 1, 1, 788.00, 14, 'The Iconic Chronograph Link will have you look at your wrist often', '2025-04-23 05:23:09'),
(5, 'Classic Glasgow Blue', 1, 1, 668.00, 7, 'This is a beautiful watch that celebrates the timeless and elegant nautical spirit', '2025-04-23 05:28:02'),
(6, 'Classic Cornwall Silver', 1, 1, 488.00, 8, 'With its distinctive dial and all-black NATO strap, the Classic Cornwall is a true eye-catcher', '2025-04-23 05:34:04'),
(7, 'Classic Canterbury Silver', 1, 1, 466.00, 7, 'Inspired by the gorgeous colors of a classic flag, this red, white and blue NATO band', '2025-04-23 05:40:18'),
(8, 'Classic Mesh Arctic Silver', 1, 1, 388.00, 8, 'The Classic Mesh Arctic is a versatile piece with much to offer.', '2025-04-23 07:23:58'),
(9, 'GMC-B2100ZE-1A', 2, 1, 488.00, 10, 'This G-SHOCK full-metal chronograph features a black and gold color scheme that evokes the first flicker of light', '2025-04-23 07:31:34'),
(10, 'DW-5610UU-3', 2, 2, 288.00, 13, 'The Urban Utility line of G-SHOCK in blue-gray and olive-green.', '2025-04-23 07:36:44'),
(11, 'GA-100FL-1A', 2, 2, 675.00, 8, 'Beginning with the GA-100 with distinctive large case and the GA-2100 with octagonal case', '2025-04-23 07:40:52'),
(12, 'G-B001SF-7', 2, 2, 688.00, 5, 'Inspired by the pages of science fiction, the crisp and clean white design is accented', '2025-04-23 07:46:09'),
(13, 'Apple watch ultra 2', 3, 2, 988.00, 5, 'The ultimate sports and adventure watch. Now in black.', '2025-04-23 07:52:53'),
(14, 'Apple Watch Series 9 Sport Loop', 3, 2, 877.00, 5, 'A magical way to use Apple Watch without touching the screen', '2025-04-23 08:04:08'),
(15, 'Apple Watch Series 10 Nike Strap', 3, 1, 748.00, 10, 'The thinnest Apple Watch ever, with our biggest display with Nike strap', '2025-04-23 12:31:04'),
(16, 'Apple Watch SE', 3, 2, 688.00, 5, 'Easy ways to stay connected. Motivating fitness metrics. Innovative health and safety features.', '2025-04-23 12:38:40'),
(17, 'GA-110GB-1A', 2, 1, 999.00, 12, 'e', '2025-05-02 03:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `product_colours`
--

CREATE TABLE `product_colours` (
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_colours`
--

INSERT INTO `product_colours` (`product_id`, `color_id`) VALUES
(1, 3),
(1, 6),
(2, 1),
(2, 3),
(3, 3),
(3, 6),
(4, 3),
(5, 6),
(5, 8),
(6, 1),
(6, 3),
(7, 3),
(7, 6),
(8, 3),
(9, 1),
(9, 3),
(10, 1),
(10, 5),
(11, 1),
(11, 3),
(11, 6),
(12, 1),
(12, 6),
(12, 8),
(13, 1),
(13, 6),
(14, 1),
(14, 6),
(15, 1),
(16, 1),
(16, 3),
(16, 6),
(17, 1),
(17, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `state` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `dob` date DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `profile_picture` varchar(255) NOT NULL DEFAULT 'images/default_profile.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `security_question` varchar(255) NOT NULL,
  `security_answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `address`, `postcode`, `state`, `email`, `phone`, `dob`, `password_hash`, `role`, `profile_picture`, `created_at`, `security_question`, `security_answer`) VALUES
(15, 'Lim Wilson', 'no 124,jamorant', '12992', 'Johor', 'wilsonlim@gmail.com', '0187896068', '2006-11-07', '$2y$10$FXXxmKxkK7DkXDilnMq1pu3CP41fohlxJYPi83wuHNJK6oPkNF0DW', 'admin', '../image/LimWilson68846aa25243d_profile_picture.png', '2025-04-17 17:33:05', 'What is your pet\'s name?', 'dog'),
(21, 'Chong Kim Seng', 'no 124,jamorant', '32000', 'Malacca', 'chongkimseng02@gmail.com', '01653711988', '2002-02-28', '$2y$10$YSnUqkB267zWH7.p80xEuu6405yvadA.PimnRGouLay1s9498IqiW', 'admin', '../image/ChongKimSeng68143a746eade_profile_picture.jpg', '2025-04-23 04:07:38', 'What is your pet\'s name?', 'dog'),
(22, 'Alvin', 'no9, semenyih', '43300', 'Selangor', 'alvin12@gmail.com', '0111234567', '2006-10-17', '$2y$10$dF8TXN3tcjeHvs7EmYDfxerEUoTtHLzbTFv3ZkuxRzUy5/C6.PS2C', 'customer', '../image/default_profile.jpg', '2025-04-23 04:09:27', 'What is your mother\'s maiden name?', 'ok'),
(23, 'Tong Yan', 'no16, The nest', '32000', 'Kuala Lumpur', 'tongyan06@gmail.com', '0105651693', '2006-04-06', '$2y$10$JjUWHwYYtPeG6z0S5A4MM.nZ8oyEDIBWyncyoo4Ce3U6oS.9rpfSq', 'customer', '../image/default_profile.jpg', '2025-04-23 12:49:00', 'What is your pet\'s name?', 'meow'),
(27, 'Sponge BOB', '124,CONCH ST', '11007', 'Kuala Lumpur', 'spongebob01@gmail.com', '01212121212', '1986-07-14', '$2y$10$0jLIKpN2WhTf076UannuHOq9/fX95A7cVENk.FFrcGoyDj/wqa2SO', 'customer', '../image/SpongeBOB680f4953754fc_profile_picture.jpeg', '2025-04-26 18:09:24', 'What is your mother\'s maiden name?', 'meili'),
(36, 'ivan', 'cheras', '04585', 'Selangor', 'ivan@gmail.com', '0162512884', '0000-00-00', '$2y$10$GyiK3ab2E6wSoo4F2h9HyuaE8KGWanhtU/p4IzSVPcn7AZK3IzSum', 'customer', '../image/ivan68846ae418dfa_profile_picture.png', '2025-05-06 02:11:15', 'What is your pet\'s name?', 'dog');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`user_id`, `product_id`) VALUES
(23, 5),
(23, 6),
(23, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `brand_name` (`brand_name`);

--
-- Indexes for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD PRIMARY KEY (`cart_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`color_id`),
  ADD UNIQUE KEY `color_name` (`color_name`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`feature_id`),
  ADD UNIQUE KEY `feature_name` (`feature_name`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `productfeatures`
--
ALTER TABLE `productfeatures`
  ADD PRIMARY KEY (`product_id`,`feature_id`),
  ADD KEY `feature_id` (`feature_id`);

--
-- Indexes for table `productimages`
--
ALTER TABLE `productimages`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_colours`
--
ALTER TABLE `product_colours`
  ADD PRIMARY KEY (`product_id`,`color_id`),
  ADD KEY `color_id` (`color_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2018;

--
-- AUTO_INCREMENT for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `productimages`
--
ALTER TABLE `productimages`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1010;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD CONSTRAINT `cartitems_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cartitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `order_status_logs_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_status_logs_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `productfeatures`
--
ALTER TABLE `productfeatures`
  ADD CONSTRAINT `productfeatures_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productfeatures_ibfk_2` FOREIGN KEY (`feature_id`) REFERENCES `features` (`feature_id`) ON DELETE CASCADE;

--
-- Constraints for table `productimages`
--
ALTER TABLE `productimages`
  ADD CONSTRAINT `productimages_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_colours`
--
ALTER TABLE `product_colours`
  ADD CONSTRAINT `product_colours_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_colours_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`color_id`) ON DELETE CASCADE;

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
