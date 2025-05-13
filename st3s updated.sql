-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 12:16 PM
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
-- Database: `st3s`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_total_amount` varchar(100) NOT NULL,
  `order_date` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_total_amount`, `order_date`) VALUES
(1, '50', '2025-04-28'),
(2, '60', '2025-04-29'),
(3, '50', '2025-04-29'),
(4, '95', '2025-04-29'),
(5, '10', '2025-05-12'),
(6, '180', '2025-05-12'),
(7, '40', '2025-05-13'),
(8, '20', '2025-05-13'),
(9, '150', '2025-05-13'),
(10, '425', '2025-05-13'),
(11, '20', '2025-05-13'),
(12, '85', '2025-05-13'),
(13, '50', '2025-05-13');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `orders_item_id` int(11) NOT NULL,
  `order_id` varchar(100) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `subtotal` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`orders_item_id`, `order_id`, `product_id`, `quantity`, `subtotal`) VALUES
(1, '1', '3', '3', '30'),
(2, '1', '4', '2', '20'),
(3, '2', '3', '3', '30'),
(4, '2', '4', '3', '30'),
(5, '3', '3', '5', '50'),
(6, '4', '2', '1', '65'),
(7, '4', '3', '3', '30'),
(8, '5', '4', '1', '10'),
(9, '6', '2', '2', '130'),
(10, '6', '3', '2', '20'),
(11, '6', '4', '3', '30'),
(12, '7', '4', '4', '40'),
(13, '8', '4', '2', '20'),
(14, '9', '4', '1', '10'),
(15, '9', '3', '1', '10'),
(16, '9', '2', '2', '130'),
(17, '10', '3', '5', '50'),
(18, '10', '2', '5', '325'),
(19, '10', '4', '5', '50'),
(20, '11', '4', '2', '20'),
(21, '12', '3', '2', '20'),
(22, '12', '2', '1', '65'),
(23, '13', '16', '1', '50');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` varchar(50) NOT NULL,
  `product_status` varchar(50) NOT NULL,
  `product_image` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_price`, `product_status`, `product_image`) VALUES
(2, 'Chicken Shawarma Rice Meal', '65', 'Available', 'chicken-shawarma.jpg'),
(3, 'Fishball Snack', '10', 'Available', 'fishball.jpg'),
(4, 'Kikyam Snack', '10', 'Available', 'kikyam(1).jpg'),
(16, 'Test Drink', '50', 'Available', 'AIDEAS-DAY-1(3).jpg');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL,
  `total_sales` int(100) NOT NULL,
  `sales_date` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `total_sales`, `sales_date`) VALUES
(1, 50, '2025-04-28'),
(2, 205, '2025-04-29'),
(3, 190, '2025-05-12'),
(4, 790, '2025-05-13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `user_password` varchar(250) NOT NULL,
  `user_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `user_password`, `user_role`) VALUES
(1, 'Admin', 'Admin', 'Admin'),
(3, 'Staff', '1253208465b1efa876f982d8a9e73eef', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`orders_item_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `orders_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
