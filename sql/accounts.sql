-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2020 at 11:46 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tms`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `mobile` varchar(12) DEFAULT NULL,
  `role` tinyint(3) UNSIGNED NOT NULL COMMENT '1:superadmin,2:salesdirector,3:salesmanager,4:salesperson,5:trainingmanager,6:trainingsup,7:trainingadmin,8:trainer,9:hr,10:finance',
  `company` smallint(32) UNSIGNED NOT NULL DEFAULT 1,
  `avatar_path` varchar(32) NOT NULL DEFAULT 'assets/account/default_user.jpg',
  `password_hash` text DEFAULT NULL,
  `account_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0:inactive,1:active',
  `user_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid',
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `email`, `mobile`, `role`, `company`, `avatar_path`, `password_hash`, `account_status`, `user_id`, `datetime_created`, `datetime_updated`) VALUES
(1, 'Melvin Trainer', 'melvin.traineer@abc.com.sg', '65955511391', 8, 3, 'assets/account/5e6072979e316.jpg', NULL, 0, '284a5386a02894ebb01e16d78e56505abceb0e919c0fe258e2831bf22959ae07', '2020-03-04 21:48:48', '2020-03-05 03:37:22'),
(3, 'MK Trainer', 'mktrainer@gmail.com', '65955511391', 8, 2, 'assets/account/5e609b29748aa.jpg', NULL, 1, '746c28a8ae03048c67c20096a407c2544914182cc64bfe450385522f20990f2e', '2020-03-05 05:06:54', '2020-03-05 06:24:41'),
(4, 'HM Trainer', 'haymannmoe-mhs@gmail.com', '65955511391', 8, 2, 'assets/account/5e6091c5eacf0.jpg', NULL, 1, 'a910a061259b9b053a6e6da4b16580185410fb38c5df6d2d0fc432376cfd31a2', '2020-03-05 05:42:00', '2020-03-05 05:44:37'),
(6, 'MO1 Training Admin', 'mo1traingadmin.mhs@gmail.com', '6595551139', 7, 3, '', '7be2aee95af3ee6d5888b5129b938ddde63188b4558297e3eca68f83d7ed8ccc', 0, 'd147640a15c658b98c0ce07f789aeaeeff591bf192e0c3d10c66bfdbec611a7e', '2020-03-05 06:51:22', '2020-03-05 08:26:03'),
(7, 'HM1  Sales', 'haymannmoe.mhs@gmail.com', '65955511391', 4, 2, '', 'cb20df6b9b8a3c4b1fb3ca6d7c56ae397f9fabfb5b278a2dfb2c9effc230bcec', 1, '9c86356ef997944e886953f1b814e52ddb5948384d2a6b6e08c60aac137dc71e', '2020-03-05 09:06:24', '2020-03-06 09:01:36'),
(8, 'Melvin Ong', 'melvin.ong7@gmail.com', '6595551139', 4, 2, 'assets/account/default_user.jpg', NULL, 1, 'e313640c6e25ec148d4c563bd7d02add1a41bc212665006f9c7e54e47596ce9b', '2020-03-06 04:13:12', '2020-03-06 04:13:12'),
(9, 'MHS-SuperAdmin', 'admin-mhs@gmail.com', NULL, 1, 1, 'assets/account/default_user.jpg', 'cdd34b08331b003b9edbe7fde820e3f19d4ee8b22bfc177d1982c3038002fcb7', 1, '213bbab641dc37a7471de5338dbcb2039277bf4001419f5173f08b0d5da6e518', '2020-03-06 07:17:16', '2020-03-06 07:27:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
