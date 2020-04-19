-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2020 at 10:55 AM
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
-- Database: `tms_company_service`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `id` int(10) UNSIGNED NOT NULL,
  `application_id` varchar(16) NOT NULL,
  `course_id` tinyint(3) UNSIGNED NOT NULL,
  `learner_id` varchar(64) NOT NULL,
  `company_id` varchar(64) NOT NULL,
  `ct_ms_expiry` date DEFAULT NULL,
  `photocopy_id` varchar(64) NOT NULL,
  `cet_acknowledgment` varchar(64) NOT NULL,
  `full_payment` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '0:no,1:yes',
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `application_doc`
--

CREATE TABLE `application_doc` (
  `application_doc_id` varchar(64) NOT NULL,
  `application_doc_type` tinyint(3) UNSIGNED NOT NULL COMMENT '0:id photocopy, 1:CET Acknowledgement',
  `filepath` int(11) NOT NULL,
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(1, 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'DEFH Pte Co.Ltd', 'avcdserew', 'DPEH Admin', '097965423', ' 212-555-1234', 11071, 'orchad ', '11', 1, 'e313640c6e25ec148d4c563bd7d02add1a41bc212665006f9c7e54e47596ce9b', 1, '2020-03-05 08:57:01', '2020-03-05 08:57:01'),
(2, 'b3a8e0e1f9ab1bfe3a36f231f676f78bb30a519d2b21e6c530c0eee8ebb4a5d0', 'SUNLIFE', 'sfssds34', 'SUNLIFE Admin', '097861354', '212-555-1234', 11056, 'Abingdon Road Singapore', '222', 2, '9c86356ef997944e886953f1b814e52ddb5948384d2a6b6e08c60aac137dc71e', 1, '2020-03-05 09:02:17', '2020-03-05 09:02:55');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `country_name` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_code`, `country_name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'DS', 'American Samoa'),
(5, 'AD', 'Andorra'),
(6, 'AO', 'Angola'),
(7, 'AI', 'Anguilla'),
(8, 'AQ', 'Antarctica'),
(9, 'AG', 'Antigua and Barbuda'),
(10, 'AR', 'Argentina'),
(11, 'AM', 'Armenia'),
(12, 'AW', 'Aruba'),
(13, 'AU', 'Australia'),
(14, 'AT', 'Austria'),
(15, 'AZ', 'Azerbaijan'),
(16, 'BS', 'Bahamas'),
(17, 'BH', 'Bahrain'),
(18, 'BD', 'Bangladesh'),
(19, 'BB', 'Barbados'),
(20, 'BY', 'Belarus'),
(21, 'BE', 'Belgium'),
(22, 'BZ', 'Belize'),
(23, 'BJ', 'Benin'),
(24, 'BM', 'Bermuda'),
(25, 'BT', 'Bhutan'),
(26, 'BO', 'Bolivia'),
(27, 'BA', 'Bosnia and Herzegovina'),
(28, 'BW', 'Botswana'),
(29, 'BV', 'Bouvet Island'),
(30, 'BR', 'Brazil'),
(31, 'IO', 'British Indian Ocean Territory'),
(32, 'BN', 'Brunei Darussalam'),
(33, 'BG', 'Bulgaria'),
(34, 'BF', 'Burkina Faso'),
(35, 'BI', 'Burundi'),
(36, 'KH', 'Cambodia'),
(37, 'CM', 'Cameroon'),
(38, 'CA', 'Canada'),
(39, 'CV', 'Cape Verde'),
(40, 'KY', 'Cayman Islands'),
(41, 'CF', 'Central African Republic'),
(42, 'TD', 'Chad'),
(43, 'CL', 'Chile'),
(44, 'CN', 'China'),
(45, 'CX', 'Christmas Island'),
(46, 'CC', 'Cocos (Keeling) Islands'),
(47, 'CO', 'Colombia'),
(48, 'KM', 'Comoros'),
(49, 'CD', 'Democratic Republic of the Congo'),
(50, 'CG', 'Republic of Congo'),
(51, 'CK', 'Cook Islands'),
(52, 'CR', 'Costa Rica'),
(53, 'HR', 'Croatia (Hrvatska)'),
(54, 'CU', 'Cuba'),
(55, 'CY', 'Cyprus'),
(56, 'CZ', 'Czech Republic'),
(57, 'DK', 'Denmark'),
(58, 'DJ', 'Djibouti'),
(59, 'DM', 'Dominica'),
(60, 'DO', 'Dominican Republic'),
(61, 'TP', 'East Timor'),
(62, 'EC', 'Ecuador'),
(63, 'EG', 'Egypt'),
(64, 'SV', 'El Salvador'),
(65, 'GQ', 'Equatorial Guinea'),
(66, 'ER', 'Eritrea'),
(67, 'EE', 'Estonia'),
(68, 'ET', 'Ethiopia'),
(69, 'FK', 'Falkland Islands (Malvinas)'),
(70, 'FO', 'Faroe Islands'),
(71, 'FJ', 'Fiji'),
(72, 'FI', 'Finland'),
(73, 'FR', 'France'),
(74, 'FX', 'France, Metropolitan'),
(75, 'GF', 'French Guiana'),
(76, 'PF', 'French Polynesia'),
(77, 'TF', 'French Southern Territories'),
(78, 'GA', 'Gabon'),
(79, 'GM', 'Gambia'),
(80, 'GE', 'Georgia'),
(81, 'DE', 'Germany'),
(82, 'GH', 'Ghana'),
(83, 'GI', 'Gibraltar'),
(84, 'GK', 'Guernsey'),
(85, 'GR', 'Greece'),
(86, 'GL', 'Greenland'),
(87, 'GD', 'Grenada'),
(88, 'GP', 'Guadeloupe'),
(89, 'GU', 'Guam'),
(90, 'GT', 'Guatemala'),
(91, 'GN', 'Guinea'),
(92, 'GW', 'Guinea-Bissau'),
(93, 'GY', 'Guyana'),
(94, 'HT', 'Haiti'),
(95, 'HM', 'Heard and Mc Donald Islands'),
(96, 'HN', 'Honduras'),
(97, 'HK', 'Hong Kong'),
(98, 'HU', 'Hungary'),
(99, 'IS', 'Iceland'),
(100, 'IN', 'India'),
(101, 'IM', 'Isle of Man'),
(102, 'ID', 'Indonesia'),
(103, 'IR', 'Iran (Islamic Republic of)'),
(104, 'IQ', 'Iraq'),
(105, 'IE', 'Ireland'),
(106, 'IL', 'Israel'),
(107, 'IT', 'Italy'),
(108, 'CI', 'Ivory Coast'),
(109, 'JE', 'Jersey'),
(110, 'JM', 'Jamaica'),
(111, 'JP', 'Japan'),
(112, 'JO', 'Jordan'),
(113, 'KZ', 'Kazakhstan'),
(114, 'KE', 'Kenya'),
(115, 'KI', 'Kiribati'),
(116, 'KP', 'Korea, Democratic People\'s Republic of'),
(117, 'KR', 'Korea, Republic of'),
(118, 'XK', 'Kosovo'),
(119, 'KW', 'Kuwait'),
(120, 'KG', 'Kyrgyzstan'),
(121, 'LA', 'Lao People\'s Democratic Republic'),
(122, 'LV', 'Latvia'),
(123, 'LB', 'Lebanon'),
(124, 'LS', 'Lesotho'),
(125, 'LR', 'Liberia'),
(126, 'LY', 'Libyan Arab Jamahiriya'),
(127, 'LI', 'Liechtenstein'),
(128, 'LT', 'Lithuania'),
(129, 'LU', 'Luxembourg'),
(130, 'MO', 'Macau'),
(131, 'MK', 'North Macedonia'),
(132, 'MG', 'Madagascar'),
(133, 'MW', 'Malawi'),
(134, 'MY', 'Malaysia'),
(135, 'MV', 'Maldives'),
(136, 'ML', 'Mali'),
(137, 'MT', 'Malta'),
(138, 'MH', 'Marshall Islands'),
(139, 'MQ', 'Martinique'),
(140, 'MR', 'Mauritania'),
(141, 'MU', 'Mauritius'),
(142, 'TY', 'Mayotte'),
(143, 'MX', 'Mexico'),
(144, 'FM', 'Micronesia, Federated States of'),
(145, 'MD', 'Moldova, Republic of'),
(146, 'MC', 'Monaco'),
(147, 'MN', 'Mongolia'),
(148, 'ME', 'Montenegro'),
(149, 'MS', 'Montserrat'),
(150, 'MA', 'Morocco'),
(151, 'MZ', 'Mozambique'),
(152, 'MM', 'Myanmar'),
(153, 'NA', 'Namibia'),
(154, 'NR', 'Nauru'),
(155, 'NP', 'Nepal'),
(156, 'NL', 'Netherlands'),
(157, 'AN', 'Netherlands Antilles'),
(158, 'NC', 'New Caledonia'),
(159, 'NZ', 'New Zealand'),
(160, 'NI', 'Nicaragua'),
(161, 'NE', 'Niger'),
(162, 'NG', 'Nigeria'),
(163, 'NU', 'Niue'),
(164, 'NF', 'Norfolk Island'),
(165, 'MP', 'Northern Mariana Islands'),
(166, 'NO', 'Norway'),
(167, 'OM', 'Oman'),
(168, 'PK', 'Pakistan'),
(169, 'PW', 'Palau'),
(170, 'PS', 'Palestine'),
(171, 'PA', 'Panama'),
(172, 'PG', 'Papua New Guinea'),
(173, 'PY', 'Paraguay'),
(174, 'PE', 'Peru'),
(175, 'PH', 'Philippines'),
(176, 'PN', 'Pitcairn'),
(177, 'PL', 'Poland'),
(178, 'PT', 'Portugal'),
(179, 'PR', 'Puerto Rico'),
(180, 'QA', 'Qatar'),
(181, 'RE', 'Reunion'),
(182, 'RO', 'Romania'),
(183, 'RU', 'Russian Federation'),
(184, 'RW', 'Rwanda'),
(185, 'KN', 'Saint Kitts and Nevis'),
(186, 'LC', 'Saint Lucia'),
(187, 'VC', 'Saint Vincent and the Grenadines'),
(188, 'WS', 'Samoa'),
(189, 'SM', 'San Marino'),
(190, 'ST', 'Sao Tome and Principe'),
(191, 'SA', 'Saudi Arabia'),
(192, 'SN', 'Senegal'),
(193, 'RS', 'Serbia'),
(194, 'SC', 'Seychelles'),
(195, 'SL', 'Sierra Leone'),
(196, 'SG', 'Singapore'),
(197, 'SK', 'Slovakia'),
(198, 'SI', 'Slovenia'),
(199, 'SB', 'Solomon Islands'),
(200, 'SO', 'Somalia'),
(201, 'ZA', 'South Africa'),
(202, 'GS', 'South Georgia South Sandwich Islands'),
(203, 'SS', 'South Sudan'),
(204, 'ES', 'Spain'),
(205, 'LK', 'Sri Lanka'),
(206, 'SH', 'St. Helena'),
(207, 'PM', 'St. Pierre and Miquelon'),
(208, 'SD', 'Sudan'),
(209, 'SR', 'Suriname'),
(210, 'SJ', 'Svalbard and Jan Mayen Islands'),
(211, 'SZ', 'Swaziland'),
(212, 'SE', 'Sweden'),
(213, 'CH', 'Switzerland'),
(214, 'SY', 'Syrian Arab Republic'),
(215, 'TW', 'Taiwan'),
(216, 'TJ', 'Tajikistan'),
(217, 'TZ', 'Tanzania, United Republic of'),
(218, 'TH', 'Thailand'),
(219, 'TG', 'Togo'),
(220, 'TK', 'Tokelau'),
(221, 'TO', 'Tonga'),
(222, 'TT', 'Trinidad and Tobago'),
(223, 'TN', 'Tunisia'),
(224, 'TR', 'Turkey'),
(225, 'TM', 'Turkmenistan'),
(226, 'TC', 'Turks and Caicos Islands'),
(227, 'TV', 'Tuvalu'),
(228, 'UG', 'Uganda'),
(229, 'UA', 'Ukraine'),
(230, 'AE', 'United Arab Emirates'),
(231, 'GB', 'United Kingdom'),
(232, 'US', 'United States'),
(233, 'UM', 'United States minor outlying islands'),
(234, 'UY', 'Uruguay'),
(235, 'UZ', 'Uzbekistan'),
(236, 'VU', 'Vanuatu'),
(237, 'VA', 'Vatican City State'),
(238, 'VE', 'Venezuela'),
(239, 'VN', 'Vietnam'),
(240, 'VG', 'Virgin Islands (British)'),
(241, 'VI', 'Virgin Islands (U.S.)'),
(242, 'WF', 'Wallis and Futuna Islands'),
(243, 'EH', 'Western Sahara'),
(244, 'YE', 'Yemen'),
(245, 'ZM', 'Zambia'),
(246, 'ZW', 'Zimbabwe'),
(247, 'AF', 'Afghanistan'),
(248, 'AL', 'Albania'),
(249, 'DZ', 'Algeria'),
(250, 'DS', 'American Samoa'),
(251, 'AD', 'Andorra'),
(252, 'AO', 'Angola'),
(253, 'AI', 'Anguilla'),
(254, 'AQ', 'Antarctica'),
(255, 'AG', 'Antigua and Barbuda'),
(256, 'AR', 'Argentina'),
(257, 'AM', 'Armenia'),
(258, 'AW', 'Aruba'),
(259, 'AU', 'Australia'),
(260, 'AT', 'Austria'),
(261, 'AZ', 'Azerbaijan'),
(262, 'BS', 'Bahamas'),
(263, 'BH', 'Bahrain'),
(264, 'BD', 'Bangladesh'),
(265, 'BB', 'Barbados'),
(266, 'BY', 'Belarus'),
(267, 'BE', 'Belgium'),
(268, 'BZ', 'Belize'),
(269, 'BJ', 'Benin'),
(270, 'BM', 'Bermuda'),
(271, 'BT', 'Bhutan'),
(272, 'BO', 'Bolivia'),
(273, 'BA', 'Bosnia and Herzegovina'),
(274, 'BW', 'Botswana'),
(275, 'BV', 'Bouvet Island'),
(276, 'BR', 'Brazil'),
(277, 'IO', 'British Indian Ocean Territory'),
(278, 'BN', 'Brunei Darussalam'),
(279, 'BG', 'Bulgaria'),
(280, 'BF', 'Burkina Faso'),
(281, 'BI', 'Burundi'),
(282, 'KH', 'Cambodia'),
(283, 'CM', 'Cameroon'),
(284, 'CA', 'Canada'),
(285, 'CV', 'Cape Verde'),
(286, 'KY', 'Cayman Islands'),
(287, 'CF', 'Central African Republic'),
(288, 'TD', 'Chad'),
(289, 'CL', 'Chile'),
(290, 'CN', 'China'),
(291, 'CX', 'Christmas Island'),
(292, 'CC', 'Cocos (Keeling) Islands'),
(293, 'CO', 'Colombia'),
(294, 'KM', 'Comoros'),
(295, 'CD', 'Democratic Republic of the Congo'),
(296, 'CG', 'Republic of Congo'),
(297, 'CK', 'Cook Islands'),
(298, 'CR', 'Costa Rica'),
(299, 'HR', 'Croatia (Hrvatska)'),
(300, 'CU', 'Cuba'),
(301, 'CY', 'Cyprus'),
(302, 'CZ', 'Czech Republic'),
(303, 'DK', 'Denmark'),
(304, 'DJ', 'Djibouti'),
(305, 'DM', 'Dominica'),
(306, 'DO', 'Dominican Republic'),
(307, 'TP', 'East Timor'),
(308, 'EC', 'Ecuador'),
(309, 'EG', 'Egypt'),
(310, 'SV', 'El Salvador'),
(311, 'GQ', 'Equatorial Guinea'),
(312, 'ER', 'Eritrea'),
(313, 'EE', 'Estonia'),
(314, 'ET', 'Ethiopia'),
(315, 'FK', 'Falkland Islands (Malvinas)'),
(316, 'FO', 'Faroe Islands'),
(317, 'FJ', 'Fiji'),
(318, 'FI', 'Finland'),
(319, 'FR', 'France'),
(320, 'FX', 'France, Metropolitan'),
(321, 'GF', 'French Guiana'),
(322, 'PF', 'French Polynesia'),
(323, 'TF', 'French Southern Territories'),
(324, 'GA', 'Gabon'),
(325, 'GM', 'Gambia'),
(326, 'GE', 'Georgia'),
(327, 'DE', 'Germany'),
(328, 'GH', 'Ghana'),
(329, 'GI', 'Gibraltar'),
(330, 'GK', 'Guernsey'),
(331, 'GR', 'Greece'),
(332, 'GL', 'Greenland'),
(333, 'GD', 'Grenada'),
(334, 'GP', 'Guadeloupe'),
(335, 'GU', 'Guam'),
(336, 'GT', 'Guatemala'),
(337, 'GN', 'Guinea'),
(338, 'GW', 'Guinea-Bissau'),
(339, 'GY', 'Guyana'),
(340, 'HT', 'Haiti'),
(341, 'HM', 'Heard and Mc Donald Islands'),
(342, 'HN', 'Honduras'),
(343, 'HK', 'Hong Kong'),
(344, 'HU', 'Hungary'),
(345, 'IS', 'Iceland'),
(346, 'IN', 'India'),
(347, 'IM', 'Isle of Man'),
(348, 'ID', 'Indonesia'),
(349, 'IR', 'Iran (Islamic Republic of)'),
(350, 'IQ', 'Iraq'),
(351, 'IE', 'Ireland'),
(352, 'IL', 'Israel'),
(353, 'IT', 'Italy'),
(354, 'CI', 'Ivory Coast'),
(355, 'JE', 'Jersey'),
(356, 'JM', 'Jamaica'),
(357, 'JP', 'Japan'),
(358, 'JO', 'Jordan'),
(359, 'KZ', 'Kazakhstan'),
(360, 'KE', 'Kenya'),
(361, 'KI', 'Kiribati'),
(362, 'KP', 'Korea, Democratic People\'s Republic of'),
(363, 'KR', 'Korea, Republic of'),
(364, 'XK', 'Kosovo'),
(365, 'KW', 'Kuwait'),
(366, 'KG', 'Kyrgyzstan'),
(367, 'LA', 'Lao People\'s Democratic Republic'),
(368, 'LV', 'Latvia'),
(369, 'LB', 'Lebanon'),
(370, 'LS', 'Lesotho'),
(371, 'LR', 'Liberia'),
(372, 'LY', 'Libyan Arab Jamahiriya'),
(373, 'LI', 'Liechtenstein'),
(374, 'LT', 'Lithuania'),
(375, 'LU', 'Luxembourg'),
(376, 'MO', 'Macau'),
(377, 'MK', 'North Macedonia'),
(378, 'MG', 'Madagascar'),
(379, 'MW', 'Malawi'),
(380, 'MY', 'Malaysia'),
(381, 'MV', 'Maldives'),
(382, 'ML', 'Mali'),
(383, 'MT', 'Malta'),
(384, 'MH', 'Marshall Islands'),
(385, 'MQ', 'Martinique'),
(386, 'MR', 'Mauritania'),
(387, 'MU', 'Mauritius'),
(388, 'TY', 'Mayotte'),
(389, 'MX', 'Mexico'),
(390, 'FM', 'Micronesia, Federated States of'),
(391, 'MD', 'Moldova, Republic of'),
(392, 'MC', 'Monaco'),
(393, 'MN', 'Mongolia'),
(394, 'ME', 'Montenegro'),
(395, 'MS', 'Montserrat'),
(396, 'MA', 'Morocco'),
(397, 'MZ', 'Mozambique'),
(398, 'MM', 'Myanmar'),
(399, 'NA', 'Namibia'),
(400, 'NR', 'Nauru'),
(401, 'NP', 'Nepal'),
(402, 'NL', 'Netherlands'),
(403, 'AN', 'Netherlands Antilles'),
(404, 'NC', 'New Caledonia'),
(405, 'NZ', 'New Zealand'),
(406, 'NI', 'Nicaragua'),
(407, 'NE', 'Niger'),
(408, 'NG', 'Nigeria'),
(409, 'NU', 'Niue'),
(410, 'NF', 'Norfolk Island'),
(411, 'MP', 'Northern Mariana Islands'),
(412, 'NO', 'Norway'),
(413, 'OM', 'Oman'),
(414, 'PK', 'Pakistan'),
(415, 'PW', 'Palau'),
(416, 'PS', 'Palestine'),
(417, 'PA', 'Panama'),
(418, 'PG', 'Papua New Guinea'),
(419, 'PY', 'Paraguay'),
(420, 'PE', 'Peru'),
(421, 'PH', 'Philippines'),
(422, 'PN', 'Pitcairn'),
(423, 'PL', 'Poland'),
(424, 'PT', 'Portugal'),
(425, 'PR', 'Puerto Rico'),
(426, 'QA', 'Qatar'),
(427, 'RE', 'Reunion'),
(428, 'RO', 'Romania'),
(429, 'RU', 'Russian Federation'),
(430, 'RW', 'Rwanda'),
(431, 'KN', 'Saint Kitts and Nevis'),
(432, 'LC', 'Saint Lucia'),
(433, 'VC', 'Saint Vincent and the Grenadines'),
(434, 'WS', 'Samoa'),
(435, 'SM', 'San Marino'),
(436, 'ST', 'Sao Tome and Principe'),
(437, 'SA', 'Saudi Arabia'),
(438, 'SN', 'Senegal'),
(439, 'RS', 'Serbia'),
(440, 'SC', 'Seychelles'),
(441, 'SL', 'Sierra Leone'),
(442, 'SG', 'Singapore'),
(443, 'SK', 'Slovakia'),
(444, 'SI', 'Slovenia'),
(445, 'SB', 'Solomon Islands'),
(446, 'SO', 'Somalia'),
(447, 'ZA', 'South Africa'),
(448, 'GS', 'South Georgia South Sandwich Islands'),
(449, 'SS', 'South Sudan'),
(450, 'ES', 'Spain'),
(451, 'LK', 'Sri Lanka'),
(452, 'SH', 'St. Helena'),
(453, 'PM', 'St. Pierre and Miquelon'),
(454, 'SD', 'Sudan'),
(455, 'SR', 'Suriname'),
(456, 'SJ', 'Svalbard and Jan Mayen Islands'),
(457, 'SZ', 'Swaziland'),
(458, 'SE', 'Sweden'),
(459, 'CH', 'Switzerland'),
(460, 'SY', 'Syrian Arab Republic'),
(461, 'TW', 'Taiwan'),
(462, 'TJ', 'Tajikistan'),
(463, 'TZ', 'Tanzania, United Republic of'),
(464, 'TH', 'Thailand'),
(465, 'TG', 'Togo'),
(466, 'TK', 'Tokelau'),
(467, 'TO', 'Tonga'),
(468, 'TT', 'Trinidad and Tobago'),
(469, 'TN', 'Tunisia'),
(470, 'TR', 'Turkey'),
(471, 'TM', 'Turkmenistan'),
(472, 'TC', 'Turks and Caicos Islands'),
(473, 'TV', 'Tuvalu'),
(474, 'UG', 'Uganda'),
(475, 'UA', 'Ukraine'),
(476, 'AE', 'United Arab Emirates'),
(477, 'GB', 'United Kingdom'),
(478, 'US', 'United States'),
(479, 'UM', 'United States minor outlying islands'),
(480, 'UY', 'Uruguay'),
(481, 'UZ', 'Uzbekistan'),
(482, 'VU', 'Vanuatu'),
(483, 'VA', 'Vatican City State'),
(484, 'VE', 'Venezuela'),
(485, 'VN', 'Vietnam'),
(486, 'VG', 'Virgin Islands (British)'),
(487, 'VI', 'Virgin Islands (U.S.)'),
(488, 'WF', 'Wallis and Futuna Islands'),
(489, 'EH', 'Western Sahara'),
(490, 'YE', 'Yemen'),
(491, 'ZM', 'Zambia'),
(492, 'ZW', 'Zimbabwe');

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
  `account_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0:inactive,1:active',
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `learner`
--

INSERT INTO `learner` (`id`, `learner_id`, `name`, `nric`, `work_permit`, `nationality`, `dob`, `sex`, `contact_no`, `learner_manager`, `coretrade_expiry`, `account_status`, `datetime_created`, `datetime_updated`) VALUES
(1, '4943b2917a57ed619473afcf7d45e028c1837d8db2451dae85c2e57ba57f3e5f', 'SSM', 'MB1234567', 'AB123456', 2, '1994-11-22', 1, '097856123', 'SUSU', '2023-10-06', 0, '2020-03-05 10:16:23', '2020-03-13 07:38:43'),
(2, '4943b2917a57ed619473afcf7d45e028c1837d8db2451dae85c2e57ba57f3e5f', 'SSK', 'MB1234567', 'AB123456', 2, '1994-11-22', 1, '097856123', 'SUSU', '2023-10-06', 1, '2020-03-05 10:19:37', '2020-03-05 10:19:37');

-- --------------------------------------------------------

--
-- Table structure for table `learners_results`
--

CREATE TABLE `learners_results` (
  `id` int(11) NOT NULL,
  `learner_id` varchar(64) NOT NULL,
  `event_id` int(11) NOT NULL COMMENT 'fk events.id',
  `result` tinyint(1) NOT NULL COMMENT '1:pass,2:Not Competent,3:Retest',
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `learners_results`
--

INSERT INTO `learners_results` (`id`, `learner_id`, `event_id`, `result`, `datetime_created`, `datetime_updated`) VALUES
(1, '4943b2917a57ed619473afcf7d45e028c1837d8db2451dae85c2e57ba57f3e5f', 1, 3, '2020-03-13 08:36:26', '2020-03-13 08:10:39');

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
  `account_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0:inactive,1:active',
  `datetime_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `learner_manager`
--

INSERT INTO `learner_manager` (`id`, `learner_manager_id`, `name`, `nric`, `work_permit`, `nationality`, `dob`, `sex`, `contact_no`, `email`, `company_id`, `account_status`, `datetime_created`, `datetime_updated`) VALUES
(1, '253c800da3ad217318707a11a455dc8f8d7036b6673b0109dd3b6895b7cc407f', 'LM', 'AB123456', 'WP1234', 1, '1991-03-04', 1, '', 'lm@gmail.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 0, '2020-03-13 07:56:55', '2020-03-13 07:57:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `application_doc`
--
ALTER TABLE `application_doc`
  ADD PRIMARY KEY (`application_doc_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learner`
--
ALTER TABLE `learner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learners_results`
--
ALTER TABLE `learners_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learner_manager`
--
ALTER TABLE `learner_manager`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=493;

--
-- AUTO_INCREMENT for table `learner`
--
ALTER TABLE `learner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `learners_results`
--
ALTER TABLE `learners_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `learner_manager`
--
ALTER TABLE `learner_manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
