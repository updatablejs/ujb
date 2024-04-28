-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2024 at 10:07 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assembler`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `album_id` int(11) NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`album_id`, `name`, `date_created`) VALUES
(1, 'The Wild Places', '2024-03-01'),
(2, 'Wherever We Go', '2024-03-02'),
(3, 'On the Road', '2024-03-03'),
(4, 'See the World', '2024-03-01'),
(5, 'Lessons From a Passport', '2024-03-01'),
(6, 'A Weekend in Colorado', '2024-03-02'),
(7, 'Wherever We Go', '2024-03-04');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `photo_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `file` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`photo_id`, `album_id`, `file`) VALUES
(1, 1, 'the_wild_places_1.jpeg'),
(2, 1, 'the_wild_places_2.jpeg'),
(3, 1, 'the_wild_places_3.jpeg'),
(4, 2, 'wherever_we_go_1.jpeg'),
(5, 2, 'wherever_we_go_2.jpeg'),
(6, 3, 'on_the_road_1.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `salary_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `salary` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `salaries`
--

INSERT INTO `salaries` (`salary_id`, `user_id`, `salary`) VALUES
(1, 1, 50),
(2, 2, 100),
(3, 3, 150);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`) VALUES
(1, 'Daleyza ', 'Waller'),
(2, 'Marley', 'Bowen'),
(3, 'Sasha', 'Duffy'),
(4, 'Robin', 'Soto'),
(5, 'Bryan', 'Buck'),
(6, 'Hayden', 'Grimes'),
(7, 'Robin', 'Larsen');

-- --------------------------------------------------------

--
-- Table structure for table `users_albums`
--

CREATE TABLE `users_albums` (
  `user_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users_albums`
--

INSERT INTO `users_albums` (`user_id`, `album_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(3, 5),
(3, 6),
(4, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`album_id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`photo_id`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`salary_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users_albums`
--
ALTER TABLE `users_albums`
  ADD PRIMARY KEY (`user_id`,`album_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `album_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
