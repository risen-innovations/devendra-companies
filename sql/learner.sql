-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2020 at 11:49 AM
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
-- Table structure for table `learner`
--

CREATE TABLE `learner` (
  `id` int(11) NOT NULL,
  `learner_id` varchar(64) NOT NULL COMMENT 'hash of nric/pp no.',
  `name` varchar(128) NOT NULL,
  `nric` varchar(16) NOT NULL,
  `work_permit` varchar(10) NOT NULL,
  `nationality` tinyint(3) UNSIGNED NOT NULL,
  `dob` date NOT NULL,
  `sex` tinyint(1) UNSIGNED NOT NULL COMMENT '0:female,1:male',
  `contact_no` varchar(10) NOT NULL,
  `learner_manager` varchar(64) NOT NULL COMMENT 'learner_manager_id',
  `coretrade_expiry` date DEFAULT NULL,
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `learner`
--

INSERT INTO `learner` (`id`, `learner_id`, `name`, `nric`, `work_permit`, `nationality`, `dob`, `sex`, `contact_no`, `learner_manager`, `coretrade_expiry`, `datetime_created`, `datetime_updated`) VALUES
(1, '4943b2917a57ed619473afcf7d45e028c1837d8db2451dae85c2e57ba57f3e5f', 'SSK', 'MB1234567', 'AB123456', 2, '1994-11-22', 1, '097856123', '6cd7aa90c74eb07fe4322a1f1aed61f77b6d55ec0a793f71276a48528a8d85a7', '2023-10-06', '2020-03-10 05:32:31', '2020-03-10 07:07:40'),
(2, '4943b2917a57ed619473afcf7d45e028c1837d8db2451dae85c2e57ba57f3e5f', 'SSK', 'MB1234567', 'AB123456', 2, '1994-11-22', 1, '097856123', '6cd7aa90c74eb07fe4322a1f1aed61f77b6d55ec0a793f71276a48528a8d85a7', '2023-10-06', '2020-03-10 05:34:41', '2020-03-10 07:07:45'),
(3, '4943b2917a57ed619473afcf7d45e028c1837d8db2451dae85c2e57ba57f3e5f', 'SSK', 'MB1234567', 'AB123456', 2, '1994-11-22', 1, '097856123', '6cd7aa90c74eb07fe4322a1f1aed61f77b6d55ec0a793f71276a48528a8d85a7', '2023-10-06', '2020-03-10 05:35:30', '2020-03-10 07:21:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `learner`
--
ALTER TABLE `learner`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `learner`
--
ALTER TABLE `learner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
