-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2020 at 11:47 AM
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
-- Table structure for table `application_doc`
--

CREATE TABLE `application_doc` (
  `application_doc_id` varchar(64) NOT NULL,
  `application_doc_type` tinyint(3) UNSIGNED NOT NULL COMMENT '0:id photocopy, 1:CET Acknowledgement',
  `filepath` varchar(32) NOT NULL,
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `application_doc`
--

INSERT INTO `application_doc` (`application_doc_id`, `application_doc_type`, `filepath`, `datetime_created`, `datetime_updated`) VALUES
('2ddd6b0581a55f8008110903dc196b82f2429f3bb0886362fc373d00e4c68781', 1, 'assets/learner/5e672722190b6.jpg', '2020-03-10 05:35:30', '2020-03-10 05:35:30'),
('ea2f6cffad9f1a50531b5c533c50661031ca6e39e7bf792fbd1cb7558d8aa533', 2, 'assets/learner/5e6727221c40b.jpg', '2020-03-10 05:35:30', '2020-03-10 05:35:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application_doc`
--
ALTER TABLE `application_doc`
  ADD PRIMARY KEY (`application_doc_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
