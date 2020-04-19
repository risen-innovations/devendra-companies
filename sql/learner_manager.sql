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
-- Table structure for table `learner_manager`
--

CREATE TABLE `learner_manager` (
  `id` int(11) NOT NULL,
  `learner_manager_id` varchar(64) NOT NULL COMMENT 'hash of nric field',
  `name` varchar(128) NOT NULL,
  `nric` varchar(16) NOT NULL,
  `work_permit` varchar(10) NOT NULL,
  `nationality` tinyint(4) NOT NULL,
  `dob` date NOT NULL,
  `sex` tinyint(1) NOT NULL COMMENT '0:femaile,1:male',
  `contact_no` varchar(10) NOT NULL,
  `email` varchar(64) NOT NULL,
  `company_id` varchar(64) NOT NULL,
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `learner_manager`
--

INSERT INTO `learner_manager` (`id`, `learner_manager_id`, `name`, `nric`, `work_permit`, `nationality`, `dob`, `sex`, `contact_no`, `email`, `company_id`, `datetime_created`, `datetime_updated`) VALUES
(1, '6cd7aa90c74eb07fe4322a1f1aed61f77b6d55ec0a793f71276a48528a8d85a7', 'SSK Learner Manager', 'MB12345267', 'AB123456', 2, '1994-11-22', 1, '097856123', 'ssk-kk7@gmail.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2020-03-10 05:54:06', '2020-03-10 05:54:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `learner_manager`
--
ALTER TABLE `learner_manager`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `learner_manager`
--
ALTER TABLE `learner_manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
