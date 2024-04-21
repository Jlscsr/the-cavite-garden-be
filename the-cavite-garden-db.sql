-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2024 at 01:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `the-cavite-garden-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_tb`
--

CREATE TABLE `cart_tb` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers_shipping_address_tb`
--

CREATE TABLE `customers_shipping_address_tb` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `street_blk_lot` varchar(255) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers_tb`
--

CREATE TABLE `customers_tb` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(11) NOT NULL,
  `birthdate` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees_tb`
--

CREATE TABLE `employees_tb` (
  `id` int(11) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `date_started` date DEFAULT NULL,
  `role` enum('regular','admin') DEFAULT NULL,
  `status` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees_tb`
--

INSERT INTO `employees_tb` (`id`, `last_name`, `first_name`, `middle_name`, `nickname`, `birthdate`, `marital_status`, `sex`, `email`, `password`, `date_started`, `role`, `status`, `created_at`, `updated_at`) VALUES
(6, 'Raagas', 'Julius', 'Fernandez', 'JC', '2002-10-03', 'married', 'male', 'juliusraagas@email.com', '$2y$10$GPu5BuQyHPRjLNk61B5bNuuHdINSOcRB2IHyEEPc3CaX/oE1dZxbm', '2024-04-01', 'admin', 'active', '2024-04-21 02:57:52', '2024-04-21 03:12:22');

-- --------------------------------------------------------

--
-- Table structure for table `products_categories_tb`
--

CREATE TABLE `products_categories_tb` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_categories_tb`
--

INSERT INTO `products_categories_tb` (`id`, `name`, `description`, `created_at`, `modified_at`) VALUES
(2, 'flowers', 'This is the flower description', '2024-04-09', '2024-04-08 16:00:00'),
(3, 'plants', 'This is plants description.', '2024-04-09', '2024-04-21 15:29:49'),
(4, 'pots', 'This is pots description', '2024-04-09', '2024-04-21 15:29:39'),
(5, 'soils', 'This is soils description.', '2024-04-09', '2024-04-21 15:30:45'),
(6, 'rocks', 'This is rocks description', '2024-04-09', '2024-04-21 15:30:59'),
(16, 'fertilizers', 'This fertilizers descriptions', '2024-04-19', '2024-04-21 15:31:15');

-- --------------------------------------------------------

--
-- Table structure for table `products_sub_categories_tb`
--

CREATE TABLE `products_sub_categories_tb` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_sub_categories_tb`
--

INSERT INTO `products_sub_categories_tb` (`id`, `category_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(7, 2, 'Indoor Flowers', 'Indoor Plants', '2024-04-21 15:02:45', '2024-04-21 15:04:11'),
(8, 2, 'Outdoor Flowers', 'Outdoor Plants', '2024-04-21 15:03:16', '2024-04-21 15:04:16'),
(9, 3, 'Indoor Plants', 'Indoor Plants', '2024-04-21 15:03:28', '2024-04-21 15:03:28'),
(10, 3, 'Outdoor Plants', 'Outdoor Plants', '2024-04-21 15:03:52', '2024-04-21 15:03:52');

-- --------------------------------------------------------

--
-- Table structure for table `products_tb`
--

CREATE TABLE `products_tb` (
  `id` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `subCategoryId` int(11) DEFAULT NULL,
  `plant_name` varchar(255) NOT NULL,
  `plant_description` varchar(255) NOT NULL,
  `plant_image` varchar(255) NOT NULL,
  `plant_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `size` varchar(10) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Available',
  `stock` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_tb`
--

INSERT INTO `products_tb` (`id`, `categoryId`, `subCategoryId`, `plant_name`, `plant_description`, `plant_image`, `plant_price`, `created_at`, `modified_at`, `size`, `status`, `stock`) VALUES
(97, 2, 8, 'MINIATURE ROSES', 'Miniature Roses Are True Roses That Have Been Selectively Bred To Stay Small In Size. Despite Their Petite Stature, They Come In A Delightful Variety Of Types And Colors, Similar To Their Larger Counterparts. These Charming Blooms Typically Grow To A Heig', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/flowers%2F1713711900498_photo_2024-04-21_22-24-31.jpg?alt=media&token=44e621a7-563d-46b2-9020-75ef4da89cda', 150.00, '2024-04-21 15:05:01', '2024-04-21 15:05:01', '', 'Available', 10),
(98, 3, 10, 'MECARDONIA', 'Mecardonia, Commonly Called Axilflower, Is A Genus Of About 12 Species Of Herbaceous Plants Native To Western South America North Through Central America To The Southeastern United States.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/plants%2F1713712575483_photo_2024-04-21_22-25-17.jpg?alt=media&token=2b9741c9-8242-4957-a0a3-954a04bb8a48', 200.00, '2024-04-21 15:16:16', '2024-04-21 15:16:16', '', 'Available', 10),
(99, 2, 8, 'GARDEN ROSES', 'Garden Roses, Also Known As Rosa, Belong To A Genus Of Approximately 100 Species Of Perennial Shrubs In The Rose Family (Rosaceae). These Beautiful Flowers Are Native Primarily To The Temperate Regions Of The Northern Hemisphere.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/flowers%2F1713712633921_photo_2024-04-21_22-26-38.jpg?alt=media&token=084744c2-40d3-49e2-ab32-5271550f4484', 350.00, '2024-04-21 15:17:14', '2024-04-21 15:17:14', '', 'Available', 10),
(100, 2, 7, 'PINK MINIATURE ROSES', 'Miniature Rose (Rosa Chinensis Minima) Has A Delicate Fragrance And Blooms In Flushes During The Growing Season. The Bloom Size Is About 1 1/2 Inches Wide With Varying Pink To Red Petals Derivative Of A Kiss Blown In The Wind From Cupid.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/flowers%2F1713712723540_photo_2024-04-21_22-27-14.jpg?alt=media&token=95a4aa3a-26dc-4fc6-8294-88421aea4760', 200.00, '2024-04-21 15:18:44', '2024-04-21 15:18:44', '', 'Available', 12),
(101, 2, 7, 'CHINA ROSES', 'China Rose Is One Of The Oldest Cultivated Roses, Having Been Brought From China Before 1894. It Is A Rounded, Woody, Deciduous Shrub In The Rose Family (Rosaceae) And Native To South And Central China.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/flowers%2F1713712793208_photo_2024-04-21_22-27-39.jpg?alt=media&token=c1d350e5-fc1e-4864-81bd-fabfe6c9d398', 200.00, '2024-04-21 15:19:54', '2024-04-21 15:19:54', '', 'Available', 20),
(102, 5, NULL, 'Sample Pot', 'qwqeeqweqwqwe', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/soils%2F1713717072356_flower-sample.jpg?alt=media&token=ae0c0eb8-ed26-4298-8a0e-709faa45c5fd', 150.00, '2024-04-21 16:31:13', '2024-04-21 16:31:13', '', 'Available', 12),
(103, 2, 7, 'SNOW WHITE MINIATURE ROSES', 'Snow White Miniature Rose (Rosa Chinensis Minima) Has A Delicate Fragrance And Blooms In Flushes During The Growing Season. The Bloom Size Is About 1 1/2 Inches Wide With White Petals Derivative Of A Kiss Blown In The Wind From Cupid.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/flowers%2F1713740250490_photo_2024-04-21_22-27-58.jpg?alt=media&token=866984f1-c086-478f-94c5-fbc42cb3435b', 250.00, '2024-04-21 22:57:31', '2024-04-21 22:57:31', '', 'Available', 10),
(104, 2, 8, 'YELLOW ROSES', 'Snow White Miniature Rose (Rosa Chinensis Minima) Has A Delicate Fragrance And Blooms In Flushes During The Growing Season. The Bloom Size Is About 1 1/2 Inches Wide With White Petals Derivative Of A Kiss Blown In The Wind From Cupid.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/flowers%2F1713740357285_photo_2024-04-21_22-28-24.jpg?alt=media&token=4cb77fcd-8315-4b49-a721-2cfbde4ffeb5', 200.00, '2024-04-21 22:59:17', '2024-04-21 22:59:17', '', 'Available', 6),
(105, 3, 9, 'GUINA CHESTNUT', 'The Guiana Chestnut (Pachira Aquatica) Is A Captivating Indoor Plant That Belongs To The Genus Pachira And The Mallow Family (Malvaceae).', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/plants%2F1713740424087_photo_2024-04-21_22-30-39.jpg?alt=media&token=a978d22b-57c5-47a4-bc22-aabcc2b160f7', 300.00, '2024-04-21 23:00:24', '2024-04-21 23:00:24', '', 'Available', 10),
(106, 3, 10, 'BROADLEAF LADY PALM', 'Rhapis Excelsa, Also Known As Broadleaf Lady Palm Or Bamboo Palm, Is A Species Of Fan Palm In The Genus Rhapis, Probably Native To Southern China And Taiwan.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/plants%2F1713740467610_photo_2024-04-21_22-31-01.jpg?alt=media&token=dcff987c-46d4-41c7-b9a5-06d386c49833', 350.00, '2024-04-21 23:01:08', '2024-04-21 23:01:08', '', 'Available', 8),
(107, 3, 9, 'RUBBER FIG', 'The Rubber Fig (Ficus Elastica), Also Known As The Rubber Tree, Rubber Plant, Or Indian Rubber Bush, Is A Fascinating Species Of Flowering Plant In The Family Moraceae.', 'https://firebasestorage.googleapis.com/v0/b/the-cavite-garden.appspot.com/o/plants%2F1713740528173_photo_2024-04-21_22-31-29.jpg?alt=media&token=aff06301-be79-4f91-b942-cbce4e7edf9f', 300.00, '2024-04-21 23:02:08', '2024-04-21 23:02:08', '', 'Available', 7);

-- --------------------------------------------------------

--
-- Table structure for table `product_transaction_tb`
--

CREATE TABLE `product_transaction_tb` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_tb`
--

CREATE TABLE `transaction_tb` (
  `id` int(11) NOT NULL,
  `costumer_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `delivery_method` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_tb`
--
ALTER TABLE `cart_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customers_shipping_address_tb`
--
ALTER TABLE `customers_shipping_address_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customers_tb`
--
ALTER TABLE `customers_tb`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees_tb`
--
ALTER TABLE `employees_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products_categories_tb`
--
ALTER TABLE `products_categories_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products_sub_categories_tb`
--
ALTER TABLE `products_sub_categories_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `products_tb`
--
ALTER TABLE `products_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plantTypeId` (`categoryId`),
  ADD KEY `subCategoryId` (`subCategoryId`);

--
-- Indexes for table `product_transaction_tb`
--
ALTER TABLE `product_transaction_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `transaction_tb`
--
ALTER TABLE `transaction_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`costumer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_tb`
--
ALTER TABLE `cart_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `customers_shipping_address_tb`
--
ALTER TABLE `customers_shipping_address_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers_tb`
--
ALTER TABLE `customers_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `employees_tb`
--
ALTER TABLE `employees_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products_categories_tb`
--
ALTER TABLE `products_categories_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products_sub_categories_tb`
--
ALTER TABLE `products_sub_categories_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products_tb`
--
ALTER TABLE `products_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `product_transaction_tb`
--
ALTER TABLE `product_transaction_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `transaction_tb`
--
ALTER TABLE `transaction_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_tb`
--
ALTER TABLE `cart_tb`
  ADD CONSTRAINT `cart_tb_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers_tb` (`id`),
  ADD CONSTRAINT `cart_tb_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products_tb` (`id`);

--
-- Constraints for table `customers_shipping_address_tb`
--
ALTER TABLE `customers_shipping_address_tb`
  ADD CONSTRAINT `customers_shipping_address_tb_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers_tb` (`id`);

--
-- Constraints for table `products_sub_categories_tb`
--
ALTER TABLE `products_sub_categories_tb`
  ADD CONSTRAINT `products_sub_categories_tb_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `products_categories_tb` (`id`);

--
-- Constraints for table `products_tb`
--
ALTER TABLE `products_tb`
  ADD CONSTRAINT `products_tb_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `products_categories_tb` (`id`),
  ADD CONSTRAINT `products_tb_ibfk_2` FOREIGN KEY (`subCategoryId`) REFERENCES `products_sub_categories_tb` (`id`);

--
-- Constraints for table `product_transaction_tb`
--
ALTER TABLE `product_transaction_tb`
  ADD CONSTRAINT `product_transaction_tb_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction_tb` (`id`),
  ADD CONSTRAINT `product_transaction_tb_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products_tb` (`id`);

--
-- Constraints for table `transaction_tb`
--
ALTER TABLE `transaction_tb`
  ADD CONSTRAINT `transaction_tb_ibfk_1` FOREIGN KEY (`costumer_id`) REFERENCES `customers_tb` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
