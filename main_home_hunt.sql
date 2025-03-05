-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2025 at 11:20 PM
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
-- Database: `main_home_hunt`
--

-- --------------------------------------------------------

--
-- Table structure for table `agreements`
--

CREATE TABLE `agreements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone` int(11) NOT NULL,
  `nid` char(20) NOT NULL,
  `members` int(11) NOT NULL,
  `user_type` enum('family','office','bachelor') NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `house_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agreements`
--

INSERT INTO `agreements` (`id`, `user_id`, `phone`, `nid`, `members`, `user_type`, `gender`, `created_at`, `house_id`) VALUES
(27, 8, 1768002207, '1233554', 4, 'office', 'male', '2025-01-25 17:05:38', 20),
(32, 9, 1768002207, '1233554', 323, 'bachelor', 'male', '2025-01-25 22:33:11', 20),
(33, 10, 1768002207, '1233554', 4, 'bachelor', 'male', '2025-01-25 22:40:30', 20),
(34, 8, 1768002207, '1233554', 34, 'family', 'female', '2025-01-26 10:19:51', 22),
(35, 8, 423, '1233554', 43234, 'family', 'male', '2025-01-26 10:26:40', 22),
(36, 8, 1768002207, '1233554', 3, 'bachelor', 'male', '2025-01-26 10:33:47', 23),
(37, 10, 1768002207, '1233554', 432, 'bachelor', 'male', '2025-01-26 12:02:19', 23),
(38, 10, 1768002207, '1233554', 45, 'family', 'male', '2025-01-26 13:27:24', 20);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `house_id`, `comment`, `created_at`, `comment_status`) VALUES
(16, 8, 20, 'good house', '2025-01-25 22:26:10', 1),
(17, 9, 20, 'so good behaviour', '2025-01-25 22:35:05', 1),
(18, 8, 23, 'fucking good', '2025-01-26 10:34:45', 1),
(19, 10, 23, 'good house', '2025-01-26 12:04:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `home_id` int(11) NOT NULL,
  `agreement_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `home_id`, `agreement_id`, `status`, `created_at`, `owner_id`) VALUES
(12, 9, 20, 32, 'approved', '2025-01-25 22:33:11', 6),
(14, 8, 22, 34, 'approved', '2025-01-26 10:19:51', 6),
(15, 8, 23, 36, 'approved', '2025-01-26 10:33:47', 6),
(16, 10, 23, 37, 'approved', '2025-01-26 12:02:19', 6);

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE `houses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `available` varchar(255) NOT NULL,
  `rent` int(11) NOT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `service_charge` varchar(255) DEFAULT NULL,
  `garage` varchar(255) DEFAULT NULL,
  `floors` int(11) DEFAULT NULL,
  `restrictions` varchar(255) DEFAULT NULL,
  `active_status` enum('1','0') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `renter_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `houses`
--

INSERT INTO `houses` (`id`, `user_id`, `location_id`, `name`, `description`, `available`, `rent`, `bedrooms`, `bathrooms`, `service_charge`, `garage`, `floors`, `restrictions`, `active_status`, `created_at`, `renter_id`) VALUES
(20, 6, 11, 'saifuls', 'sdasd', 'May', 20000988, 0, 0, '2000', 'Yes', 0, 'qweq', '1', '2025-01-25 16:32:42', NULL),
(22, 6, 13, 'House Paradise ', 'sadasd', 'May', 20000, 3, 22, '2000', 'Yes', 3, 'no restriction', '0', '2025-01-26 10:14:45', NULL),
(23, 6, 14, 'Abrar', 'dasd', 'MAY', 39999, 23, 3, '32323', 'Yes', 23232, '2323', '0', '2025-01-26 10:33:00', NULL),
(24, 6, 15, 'Nahar Garden', 'Such a nice house', 'May', 20000, 3, 2, '2000', 'Yes', 3, 'No pet', '1', '2025-01-26 15:27:38', NULL),
(25, 6, 19, 'Ahnafs House', 'Wonderful House', 'January', 30000, 4, 3, '20000', 'Yes', 2, 'No entry after 12', '1', '2025-01-26 15:33:48', NULL),
(26, 11, 20, 'Kollani Home', 'Cute House', 'june', 40000, 5, 4, '4000', 'No', 5, 'No entry after 12,no pet', '1', '2025-01-26 15:38:30', NULL),
(27, 11, 21, 'Chowdhory Villa', 'good', 'MAY', 30000, 6, 5, '2000', 'Yes', 7, 'No entry after 12', '1', '2025-01-26 15:41:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `house_essentials`
--

CREATE TABLE `house_essentials` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `essential_name` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_essentials`
--

INSERT INTO `house_essentials` (`id`, `post_id`, `essential_name`) VALUES
(23, 20, 'none'),
(25, 23, '323'),
(26, 22, 'none'),
(27, 24, 'Bank, Transportation'),
(29, 25, 'Bank,Transportation'),
(30, 26, 'Bank, Transportation'),
(31, 27, 'Bank, Transportation');

-- --------------------------------------------------------

--
-- Table structure for table `house_features`
--

CREATE TABLE `house_features` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `feature_name` varchar(100) DEFAULT NULL,
  `gas` enum('Yes','No') DEFAULT 'No',
  `wifi` enum('Yes','No') DEFAULT 'No',
  `cctv` enum('Yes','No') DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_features`
--

INSERT INTO `house_features` (`id`, `post_id`, `feature_name`, `gas`, `wifi`, `cctv`) VALUES
(23, 20, 'everything', 'Yes', 'Yes', 'Yes'),
(25, 23, '2', 'Yes', 'Yes', 'Yes'),
(26, 22, 'everything', 'Yes', 'Yes', 'Yes'),
(27, 24, 'Lift', 'Yes', 'Yes', 'Yes'),
(29, 25, 'lift', 'Yes', 'No', 'Yes'),
(30, 26, 'everything', 'Yes', 'No', 'No'),
(31, 27, 'everything', 'Yes', 'Yes', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `house_images`
--

CREATE TABLE `house_images` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('main','small') DEFAULT 'main'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_images`
--

INSERT INTO `house_images` (`id`, `post_id`, `image_path`, `uploaded_at`, `type`) VALUES
(41, 20, 'uploads/6.jpg', '2025-01-25 16:32:42', 'main'),
(43, 20, 'uploads/222.jpg', '2025-01-26 00:04:26', 'small'),
(44, 20, 'uploads/333.jpg', '2025-01-26 00:04:26', 'small'),
(45, 20, 'uploads/g2.jpg', '2025-01-26 00:04:26', 'small'),
(46, 22, 'uploads/Screenshot 2025-01-21 at 19-07-29 Single Bedroom To-let _ Rent from February for Family in Khilgaon Dhaka - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 10:14:45', 'main'),
(47, 22, 'uploads/Screenshot 2025-01-21 at 19-31-16 Double Bedroom House To-let _ Rent from February for Family in Rajshahi Sadar Rajshahi - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 10:14:45', 'small'),
(48, 23, 'uploads/333.jpg', '2025-01-26 10:33:00', 'main'),
(49, 23, 'uploads/g4.jpg', '2025-01-26 10:33:00', 'small'),
(50, 24, 'uploads/Screenshot 2025-01-21 at 18-58-23 3 Bedroom House To-let _ Rent from March for Family in Kazipara Mirpur Dhaka - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:27:38', 'main'),
(51, 24, 'uploads/Screenshot 2025-01-21 at 19-07-38 Single Bedroom To-let _ Rent from February for Family in Khilgaon Dhaka - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:27:38', 'small'),
(52, 25, 'uploads/Screenshot 2025-01-21 at 19-31-16 Double Bedroom House To-let _ Rent from February for Family in Rajshahi Sadar Rajshahi - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:33:48', 'main'),
(53, 25, 'uploads/Screenshot 2025-01-21 at 19-35-28 Double Bedroom House To-let _ Rent from February for Family in Rajshahi Sadar Rajshahi - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:33:48', 'small'),
(54, 26, 'uploads/Screenshot 2025-01-21 at 19-39-58 Single Bedroom House To-let _ Rent from February for Family in Bogra Sadar Bogra - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:38:30', 'main'),
(55, 26, 'uploads/Screenshot 2025-01-21 at 19-35-28 Double Bedroom House To-let _ Rent from February for Family in Rajshahi Sadar Rajshahi - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:38:30', 'small'),
(56, 27, 'uploads/Screenshot 2025-01-21 at 19-31-26 Double Bedroom House To-let _ Rent from February for Family in Rajshahi Sadar Rajshahi - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:41:21', 'main'),
(57, 27, 'uploads/Screenshot 2025-01-21 at 19-35-28 Double Bedroom House To-let _ Rent from February for Family in Rajshahi Sadar Rajshahi - Basa Vara THE TOLET Bangladesh.png', '2025-01-26 15:41:21', 'small');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `address`, `city`, `latitude`, `longitude`) VALUES
(1, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(2, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(3, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(4, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(5, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(6, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(7, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(8, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(9, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(10, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(11, 'uiu', 'uiu', 23.79784735, 90.45005242761974),
(12, 'abdullahpur', 'uiu', 29.7499614, 76.9841254),
(13, 'sayednogor', 'NATORE', 23.7975283, 90.435233),
(14, 'haydrabad', 'Dhaka', 23.9239357, 90.4236284),
(15, 'uttara', 'Dhaka', 23.8693275, 90.3926893),
(16, 'cumilla', 'Cumilla', 23.4610615, 91.1808748),
(17, 'cumilla', 'Cumilla', 0, 0),
(18, 'cumilla', 'Cumilla', 0, 0),
(19, 'cumilla', 'Cumilla', 23.4610615, 91.1808748),
(20, 'Badda', 'Dhaka', 23.784088152266808, 90.43001174926759),
(21, 'Dhaka', 'Dhaka', 23.7544529, 90.393336);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `house_id`, `rating`, `created_at`) VALUES
(30, 8, 20, 5, '2025-01-25 22:26:10'),
(31, 9, 20, 4, '2025-01-25 22:35:05'),
(32, 8, 23, 4, '2025-01-26 10:34:45'),
(33, 10, 23, 5, '2025-01-26 12:04:41');

-- --------------------------------------------------------

--
-- Table structure for table `renter_type`
--

CREATE TABLE `renter_type` (
  `id` int(11) NOT NULL,
  `renter_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` enum('Owner','Customer') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Owner'),
(2, 'Customer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `address`, `phone`, `password`, `dob`, `photo`, `created_at`, `role_id`) VALUES
(6, 'Abrar Junaed', 'abrarjunaed24@gmail.com', '', '0', 'qwqww', '1999-02-22', '', '2025-01-22 17:39:45', 1),
(8, 'Mahfuz', 'rmahfuzur805@gmail.com', '', '0', '11111', '2002-08-02', '', '2025-01-24 09:17:31', 2),
(9, 'Rohim mia', 'rohimmia@gmail.com', 'Dhaka', '1768002207', '11111', '1999-11-22', '', '2025-01-25 18:10:18', 2),
(10, 'korim', 'abrarjunaed22@gmail.com', 'Dhaka', '1768002207', '12345', '1999-03-23', '', '2025-01-25 22:40:03', 2),
(11, 'Ahnaf Ahmed', 'abrarjunaed22@gmail.com', 'Dhaka', '01768002207', 'qwerty', '2014-06-10', '', '2025-01-26 15:35:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_photos`
--

CREATE TABLE `user_photos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `photo_path` varchar(255) NOT NULL,
  `type` enum('profile','aggrement') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_photos`
--

INSERT INTO `user_photos` (`id`, `user_id`, `photo_path`, `type`, `uploaded_at`) VALUES
(3, 6, 'uploads/pexels-photo-2379005.jpeg', 'profile', '2025-01-22 17:39:45'),
(5, 8, 'uploads/57.jpg', 'profile', '2025-01-24 09:17:31'),
(6, 9, 'uploads/Untitled1.jpg', 'profile', '2025-01-25 18:10:18'),
(7, 10, 'uploads/3.jpg', 'profile', '2025-01-25 22:40:03'),
(8, 11, 'uploads/01759339079 x.jpg', 'profile', '2025-01-26 15:35:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agreements`
--
ALTER TABLE `agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_house_id` (`house_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_ibfk_1` (`user_id`),
  ADD KEY `comments_ibfk_2` (`house_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_owner` (`owner_id`),
  ADD KEY `enrollments_ibfk_1` (`user_id`),
  ADD KEY `enrollments_ibfk_2` (`home_id`),
  ADD KEY `enrollments_ibfk_3` (`agreement_id`);

--
-- Indexes for table `houses`
--
ALTER TABLE `houses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_houses_locations` (`location_id`),
  ADD KEY `fk_renter_type` (`renter_id`);

--
-- Indexes for table `house_essentials`
--
ALTER TABLE `house_essentials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `house_features`
--
ALTER TABLE `house_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `house_images`
--
ALTER TABLE `house_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_ibfk_1` (`user_id`),
  ADD KEY `ratings_ibfk_2` (`house_id`);

--
-- Indexes for table `renter_type`
--
ALTER TABLE `renter_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_photos_ibfk_1` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agreements`
--
ALTER TABLE `agreements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `houses`
--
ALTER TABLE `houses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `house_essentials`
--
ALTER TABLE `house_essentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `house_features`
--
ALTER TABLE `house_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `house_images`
--
ALTER TABLE `house_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `renter_type`
--
ALTER TABLE `renter_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_photos`
--
ALTER TABLE `user_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agreements`
--
ALTER TABLE `agreements`
  ADD CONSTRAINT `agreements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_house_id` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`home_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`agreement_id`) REFERENCES `agreements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `houses`
--
ALTER TABLE `houses`
  ADD CONSTRAINT `fk_houses_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_renter_type` FOREIGN KEY (`renter_id`) REFERENCES `renter_type` (`id`),
  ADD CONSTRAINT `houses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `house_essentials`
--
ALTER TABLE `house_essentials`
  ADD CONSTRAINT `house_essentials_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `house_features`
--
ALTER TABLE `house_features`
  ADD CONSTRAINT `house_features_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `house_images`
--
ALTER TABLE `house_images`
  ADD CONSTRAINT `house_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD CONSTRAINT `user_photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
