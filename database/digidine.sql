-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 06:10 PM
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
-- Database: `menu_scanner`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `description`, `price`, `image`) VALUES
(1, 'Margherita Pizza', 'Classic delight with 100% real mozzarella cheese', 7.99, 'images/margherita.jpg'),
(2, 'Veggie Burger', 'Grilled veggie patty with fresh lettuce and tomato', 6.50, 'images/veggieburger.jpg'),
(3, 'Cheesy Garlic Bread', 'Toasted bread with garlic butter and cheese topping', 4.00, 'images/garlicbread.jpg'),
(5, 'Tandoori Paneer', 'Paneer cubes marinated in spices and grilled to perfection', 10.00, 'images/paneer.jpg'),
(6, 'Cold Coffee', 'Chilled coffee with ice cream and chocolate syrup', 3.75, 'images/coldcoffee.jpg'),
(7, 'French Fries', 'Crispy golden fries served with ketchup', 3.50, 'images/fries.jpg'),
(8, 'Chocolate Brownie', 'Rich chocolate brownie with a gooey center', 5.00, 'images/brownie.jpg'),
(16, 'Alfredo', ' A pasta dish consisting of Parmesan cheese', 50.00, 'images/alfredo.jpg'),
(17, 'Thum\'s Up', 'Thums Up Cola Soft Drink, 250Ml', 10.00, 'images/thumsup.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `contact_number`, `total_price`, `status`, `created_at`) VALUES
(1, 1, '[value-1]', '[value-2]', 0.00, '', '0000-00-00 00:00:00'),
(2, 2, '[value-1]', '[value-2]', 0.00, '', '0000-00-00 00:00:00'),
(3, 3, 'cus3', 'N/A', 5.00, '', '2025-04-24 11:37:47'),
(4, 4, 'cus3', 'N/A', 6.50, '', '2025-04-24 11:39:43'),
(5, 5, 'cus3', 'N/A', 6.50, '', '2025-04-24 11:39:51'),
(6, 17, 'cus3', 'N/A', 6.50, '', '2025-04-24 11:59:15'),
(7, 17, 'cus3', 'N/A', 19.50, 'completed', '2025-04-25 09:57:36'),
(8, 17, 'cus3', 'N/A', 19.50, '', '2025-04-25 09:57:48'),
(9, 17, 'cus3', 'N/A', 6.50, '', '2025-04-25 09:57:54'),
(10, 17, 'cus3', 'N/A', 6.50, '', '2025-04-25 12:01:34'),
(11, 17, 'cus3', 'N/A', 18.75, 'cancelled', '2025-04-25 12:02:17'),
(12, 17, 'cus3', 'N/A', 16.49, 'cancelled', '2025-04-25 12:03:36'),
(13, 17, 'cus3', 'N/A', 8.75, 'completed', '2025-04-25 15:12:52'),
(14, 14, 'chef2', 'N/A', 3.50, 'in_progress', '2025-04-26 03:10:07'),
(15, 17, 'cus3', 'N/A', 5.00, 'completed', '2025-04-26 03:10:51'),
(16, 19, 'manager1', 'N/A', 8.99, 'cancelled', '2025-04-28 02:28:37'),
(17, 17, 'cus3', 'N/A', 5.00, 'completed', '2025-04-28 02:32:26'),
(20, 17, 'cus3', 'N/A', 7.99, 'pending', '2025-05-02 03:14:15'),
(21, 17, 'cus3', 'N/A', 14.49, 'pending', '2025-05-02 03:15:19'),
(22, 17, 'cus3', 'N/A', 4.00, 'pending', '2025-05-02 03:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `quantity`) VALUES
(1, 10, 2, 1),
(2, 11, 5, 1),
(3, 11, 8, 1),
(4, 11, 6, 1),
(5, 12, 1, 1),
(6, 12, 7, 1),
(7, 12, 3, 1),
(8, 13, 8, 1),
(9, 13, 6, 1),
(10, 14, 7, 1),
(11, 15, 8, 1),
(12, 16, 1, 1),
(13, 17, 8, 1),
(17, 20, 1, 1),
(18, 21, 1, 1),
(19, 21, 2, 1),
(20, 22, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','chef','customer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2, 'admin', 'admin123', 'admin'),
(3, 'chef', 'chef123', 'chef'),
(4, 'manager', 'manager123', 'manager'),
(5, 'c1', 'c1', 'chef'),
(6, 'c2', 'c2', 'chef'),
(13, 'chef1', '$2y$10$d0Gy44QnxigvIKYnRBL6n.pK0zcbZZL9C3aCjwMJn8sOjaEdlLM46', 'chef'),
(15, 'admin12', '$2y$10$Wifaj0SVqs76m17rMNs8mOderXxA7IpuaDh8uabQwt47FrvE5IwQe', 'admin'),
(16, 'cus', '$2y$10$INud5kqzbSutvpUrepXcvukMhpW4lvow3dDiLrvDCBEMK1F5W1iGS', 'customer'),
(17, 'cus3', '$2y$10$is/e2TYVzGp8M/aQb1dcRO07UYlqwZ9NG0xfbfax69zbxjxIFfQQa', 'customer'),
(18, 'admin1', '$2y$10$SBvojlEYYFrNmuoROG3UrOvn68MEzuqjUG3JmnUsU6.a7qYEXsExu', 'admin'),
(19, 'manager1', '$2y$10$67VhVwlBEpXaPgMVoNA4MeO4KhXAwzbqY7HFWRsWpNT8t1vV5EUYy', 'manager'),
(20, 'chef3', 'chef3', 'chef'),
(21, 'chef4', 'chef4', 'chef'),
(23, 'customer', '$2y$10$cyqrIhNw4vBPkj0eWLcsS.h9cC/1OoIvb5eHDF4XHPRGWNLstQJnm', 'customer'),
(24, 'chef5', '$2y$10$sV.H1PDTZoj28SOhz.3Tfe7TPZ.eTK16zsBPW.FBYKREC9jNsmXqS', 'chef');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id` (`menu_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
