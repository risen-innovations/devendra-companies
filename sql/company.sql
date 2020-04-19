-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2020 at 11:48 AM
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
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` text NOT NULL COMMENT 'hash of UEN',
  `company_name` varchar(128) NOT NULL,
  `uen` varchar(10) NOT NULL,
  `contact_person` varchar(128) NOT NULL,
  `contact_number` varchar(16) NOT NULL,
  `fax` varchar(16) NOT NULL,
  `postal_code` int(6) UNSIGNED NOT NULL,
  `street` varchar(256) NOT NULL,
  `unit` varchar(8) NOT NULL,
  `payment_terms` tinyint(3) UNSIGNED NOT NULL COMMENT '0:COD,1:30 Days',
  `sales_person` varchar(256) NOT NULL COMMENT 'an array of sales person user_id',
  `status` tinyint(3) UNSIGNED NOT NULL COMMENT '0:inactive,1:active',
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `company_id`, `company_name`, `uen`, `contact_person`, `contact_number`, `fax`, `postal_code`, `street`, `unit`, `payment_terms`, `sales_person`, `status`, `datetime_created`, `datetime_updated`) VALUES
(1, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'DEFH Pte Co.Ltd', 'avcdserew', 'DPEH Admin', '097965423', ' 212-555-1234', 11071, 'orchad ', '11', 1, 'e313640c6e25ec148d4c563bd7d02add1a41bc212665006f9c7e54e47596ce9b', 1, '2020-03-06 08:57:01', '2020-03-06 08:57:01'),
(2, 'b3a8e0e1f9ab1bfe3a36f231f676f78bb30a519d2b21e6c530c0eee8ebb4a5d0', 'SUNLIFE', 'sfssds34', 'SUNLIFE Admin', '097861354', '212-555-1234', 11056, 'Abingdon Road Singapore', '222', 2, '9c86356ef997944e886953f1b814e52ddb5948384d2a6b6e08c60aac137dc71e', 1, '2020-03-06 09:02:17', '2020-03-06 09:02:55'),
(3, '110d3a66a2e90be5139304d802311db90e499785ae177cb017a4172b2b303127', 'KKK Co.Ltd', 'kkk12356', 'KKK Admin', '0978613524', '212-555-2234', 11086, 'Singapore', '333', 1, '9c86356ef997944e886953f1b814e52ddb5948384d2a6b6e08c60aac137dc71e,e313640c6e25ec148d4c563bd7d02add1a41bc212665006f9c7e54e47596ce9b', 1, '2020-03-10 03:38:06', '2020-03-10 03:38:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
