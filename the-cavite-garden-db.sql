-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2024 at 11:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `the-cavite-garden-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_tb`
--

CREATE TABLE `cart_tb` (
    `id` int(11) NOT NULL, `customer_id` int(11) NOT NULL, `product_id` int(11) NOT NULL, `quantity` int(11) NOT NULL, `price` decimal(10, 2) NOT NULL, `created_at` timestamp NOT NULL DEFAULT current_timestamp(), `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers_shipping_address_tb`
--

CREATE TABLE `customers_shipping_address_tb` (
    `id` int(11) NOT NULL, `customer_id` int(11) DEFAULT NULL, `label` varchar(255) NOT NULL, `region` varchar(255) DEFAULT NULL, `province` varchar(255) DEFAULT NULL, `municipality` varchar(255) DEFAULT NULL, `barangay` varchar(255) DEFAULT NULL, `street_blk_lot` varchar(255) DEFAULT NULL, `landmark` varchar(255) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_tb`
--

CREATE TABLE `customer_tb` (
    `id` int(11) NOT NULL, `first_name` varchar(255) NOT NULL, `last_name` varchar(255) NOT NULL, `phone_number` varchar(11) NOT NULL, `birthdate` date NOT NULL, `email` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, `created_at` date NOT NULL DEFAULT current_timestamp(), `updated_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_tb`
--

CREATE TABLE `employee_tb` (
    `id` int(11) NOT NULL, `last_name` varchar(255) DEFAULT NULL, `first_name` varchar(255) DEFAULT NULL, `middle_name` varchar(255) DEFAULT NULL, `nickname` varchar(255) DEFAULT NULL, `birthdate` date DEFAULT NULL, `marital_status` varchar(50) DEFAULT NULL, `sex` char(1) DEFAULT NULL, `email` varchar(255) DEFAULT NULL, `password` varchar(255) DEFAULT NULL, `date_started` date DEFAULT NULL, `role` enum('regular', 'admin') DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT current_timestamp(), `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--

-- --------------------------------------------------------

--
-- Table structure for table `plants_tb`
--

CREATE TABLE `plants_tb` (
    `id` int(11) NOT NULL, `categoryId` int(11) NOT NULL, `plant_name` varchar(255) NOT NULL, `plant_description` varchar(255) NOT NULL, `plant_image` varchar(255) NOT NULL, `plant_price` decimal(10, 2) NOT NULL, `created_at` timestamp NOT NULL DEFAULT current_timestamp(), `modified_at` timestamp NOT NULL DEFAULT current_timestamp(), `size` varchar(10) DEFAULT NULL, `status` varchar(50) NOT NULL DEFAULT 'Available', `stock` bigint(20) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plant_categories`
--

CREATE TABLE `plant_categories` (
    `id` int(11) NOT NULL, `name` varchar(255) NOT NULL, `description` varchar(255) NOT NULL, `created_at` date NOT NULL DEFAULT current_timestamp(), `modified_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `plant_categories`
--

INSERT INTO
    `plant_categories` (
        `id`, `name`, `description`, `created_at`, `modified_at`
    )
VALUES (
        2, 'flowers', 'Test', '2024-04-09', '2024-04-09'
    ),
    (
        3, 'plants', 'QWeqweqwe', '2024-04-09', '2024-04-09'
    ),
    (
        4, 'pots', 'qasdasdasdaw', '2024-04-09', '2024-04-09'
    ),
    (
        5, 'soils', 'qweqweqweqwe', '2024-04-09', '2024-04-09'
    ),
    (
        6, 'rocks', 'qweqweqwe', '2024-04-09', '2024-04-09'
    ),
    (
        16, 'Fertilizers', 'Test', '2024-04-19', '2024-04-19'
    );

-- --------------------------------------------------------

--
-- Table structure for table `product_transaction_tb`
--

CREATE TABLE `product_transaction_tb` (
    `id` int(11) NOT NULL, `transaction_id` int(11) DEFAULT NULL, `product_id` int(11) DEFAULT NULL, `price` decimal(10, 2) DEFAULT NULL, `quantity` int(11) DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT current_timestamp(), `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_tb`
--

CREATE TABLE `transaction_tb` (
    `id` int(11) NOT NULL, `costumer_id` int(11) DEFAULT NULL, `total_price` decimal(10, 2) DEFAULT NULL, `delivery_method` varchar(255) DEFAULT NULL, `payment_method` varchar(255) DEFAULT NULL, `shipping_address` varchar(255) DEFAULT NULL, `status` varchar(50) DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT current_timestamp(), `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

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
-- Indexes for table `customer_tb`
--
ALTER TABLE `customer_tb` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_tb`
--
ALTER TABLE `employee_tb` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plants_tb`
--
ALTER TABLE `plants_tb`
ADD PRIMARY KEY (`id`),
ADD KEY `plantTypeId` (`categoryId`);

--
-- Indexes for table `plant_categories`
--
ALTER TABLE `plant_categories` ADD PRIMARY KEY (`id`);

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 38;

--
-- AUTO_INCREMENT for table `customers_shipping_address_tb`
--
ALTER TABLE `customers_shipping_address_tb`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 7;

--
-- AUTO_INCREMENT for table `customer_tb`
--
ALTER TABLE `customer_tb`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 12;

--
-- AUTO_INCREMENT for table `employee_tb`
--
ALTER TABLE `employee_tb`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT for table `plants_tb`
--
ALTER TABLE `plants_tb`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 92;

--
-- AUTO_INCREMENT for table `plant_categories`
--
ALTER TABLE `plant_categories`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 17;

--
-- AUTO_INCREMENT for table `product_transaction_tb`
--
ALTER TABLE `product_transaction_tb`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 32;

--
-- AUTO_INCREMENT for table `transaction_tb`
--
ALTER TABLE `transaction_tb`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_tb`
--
ALTER TABLE `cart_tb`
ADD CONSTRAINT `cart_tb_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_tb` (`id`),
ADD CONSTRAINT `cart_tb_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `plants_tb` (`id`);

--
-- Constraints for table `customers_shipping_address_tb`
--
ALTER TABLE `customers_shipping_address_tb`
ADD CONSTRAINT `customers_shipping_address_tb_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_tb` (`id`);

--
-- Constraints for table `plants_tb`
--
ALTER TABLE `plants_tb`
ADD CONSTRAINT `plants_tb_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `plant_categories` (`id`);

--
-- Constraints for table `product_transaction_tb`
--
ALTER TABLE `product_transaction_tb`
ADD CONSTRAINT `product_transaction_tb_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction_tb` (`id`),
ADD CONSTRAINT `product_transaction_tb_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `plants_tb` (`id`);

--
-- Constraints for table `transaction_tb`
--
ALTER TABLE `transaction_tb`
ADD CONSTRAINT `transaction_tb_ibfk_1` FOREIGN KEY (`costumer_id`) REFERENCES `customer_tb` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;