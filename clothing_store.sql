-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 02:29 PM
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
-- Database: `clothing_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblmessage`
--

CREATE TABLE `tblmessage` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblorder`
--

CREATE TABLE `tblorder` (
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','paid','delivered') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorder`
--

INSERT INTO `tblorder` (`order_id`, `buyer_id`, `product_id`, `total_amount`, `order_date`, `status`) VALUES
(9, 8, 5, 399.99, '2026-05-04 12:09:08', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `tblproduct`
--

CREATE TABLE `tblproduct` (
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `condition` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','sold') DEFAULT 'pending',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproduct`
--

INSERT INTO `tblproduct` (`product_id`, `seller_id`, `item_name`, `brand`, `category`, `size`, `condition`, `price`, `description`, `image_path`, `status`, `upload_date`) VALUES
(4, 8, 'Nike-shirt', 'nike', 'tops', '20', 'Like New', 300.00, 'Black nike tshirt', 'uploads/1777896417_Nike-Men.webp', 'approved', '2026-05-04 12:06:57'),
(5, 8, 'Nike-shirt', 'nike', 'tops', '22', 'Like New', 399.99, 'Blue Nike shirt', 'uploads/1777896453_Nike-men2.jpeg', 'approved', '2026-05-04 12:07:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `delivery_address` text DEFAULT NULL,
  `seller_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `full_name`, `email`, `username`, `password_hash`, `delivery_address`, `seller_status`, `registration_date`, `is_verified`) VALUES
(8, '', 'isameltawil@gmail.com', 'Isam', '$2y$10$h1Lb1E8OBR2fpo3KxbpsKuVNNns97pGc98b1n.0dYD51NmnSqDmLy', NULL, 'pending', '2026-05-04 08:59:13', 1),
(9, '', 'Motau123@gmail.com', 'Motau', '$2y$10$C3se8.brIvvhLRu0p6.O1eGTMLulgz1.QtHgnD1hnq9oB.ZwwRy82', NULL, 'pending', '2026-05-04 12:02:51', 1),
(10, '', 'Matome@gmail.com', 'Matome', '$2y$10$BIL3ymXJkIO7QwYHJHo2AOy7KXblUjk0sk8eFAbYvFAgaPCkoV4KS', NULL, 'pending', '2026-05-04 12:03:17', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblmessage`
--
ALTER TABLE `tblmessage`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_msg_sender` (`sender_id`),
  ADD KEY `fk_msg_receiver` (`receiver_id`),
  ADD KEY `fk_msg_product` (`product_id`);

--
-- Indexes for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_order_buyer` (`buyer_id`),
  ADD KEY `fk_order_product` (`product_id`);

--
-- Indexes for table `tblproduct`
--
ALTER TABLE `tblproduct`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_product_seller` (`seller_id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblmessage`
--
ALTER TABLE `tblmessage`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblorder`
--
ALTER TABLE `tblorder`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tblproduct`
--
ALTER TABLE `tblproduct`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblmessage`
--
ALTER TABLE `tblmessage`
  ADD CONSTRAINT `fk_msg_product` FOREIGN KEY (`product_id`) REFERENCES `tblproduct` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `tbluser` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `tbluser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD CONSTRAINT `fk_order_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `tbluser` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_product` FOREIGN KEY (`product_id`) REFERENCES `tblproduct` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblproduct`
--
ALTER TABLE `tblproduct`
  ADD CONSTRAINT `fk_product_seller` FOREIGN KEY (`seller_id`) REFERENCES `tbluser` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
