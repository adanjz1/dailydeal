-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2014 a las 19:48:28
-- Versión del servidor: 5.5.32
-- Versión de PHP: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `abbijan`
--
CREATE DATABASE IF NOT EXISTS `abbijan` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `abbijan`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_categories`
--

CREATE TABLE IF NOT EXISTS `abbijan_categories` (
  `category_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `abbijan_categories`
--

INSERT INTO `abbijan_categories` (`category_id`, `parent_id`, `name`, `description`) VALUES
(1, 0, 'Software', ''),
(2, 0, 'T-Shirts', ''),
(3, 0, 'Home', ''),
(4, 0, 'Electronics', ''),
(5, 0, 'Health', ''),
(6, 0, 'Education', ''),
(7, 0, 'Sport', ''),
(8, 0, 'Other', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_content`
--

CREATE TABLE IF NOT EXISTS `abbijan_content` (
  `content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `abbijan_content`
--

INSERT INTO `abbijan_content` (`content_id`, `name`, `title`, `description`, `modified`) VALUES
(1, 'aboutus', 'About Us', '<p>Information about site.</p>', '2014-06-16 14:09:48'),
(2, 'help', 'Help', '<p>Some information about site, delivery, payment methods, etc.</p>', '2014-06-16 14:09:48'),
(3, 'terms', 'Terms and Conditions', '<p>Your site terms and conditions (edit from admin area).</p>', '2014-06-16 14:09:48'),
(4, 'privacy', 'Privacy Policy', '<p>Privacy Policy information (edit from admin area).</p>', '2014-06-16 14:09:48'),
(5, 'contact', 'Contact Us', '<p>If you have any questions, please feel free to contact us.</p>\r\n<p>Email: support@yourdomain.com</p>', '2014-06-16 14:09:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_countries`
--

CREATE TABLE IF NOT EXISTS `abbijan_countries` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=241 ;

--
-- Volcado de datos para la tabla `abbijan_countries`
--

INSERT INTO `abbijan_countries` (`country_id`, `code`, `name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AX', 'Aland Islands'),
(3, 'AL', 'Albania'),
(4, 'DZ', 'Algeria'),
(5, 'AS', 'American Samoa'),
(6, 'AD', 'Andorra'),
(7, 'AO', 'Angola'),
(8, 'AI', 'Anguilla'),
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
(49, 'CG', 'Congo'),
(50, 'CD', 'Congo, The Democratic Republic of the'),
(51, 'CK', 'Cook Islands'),
(52, 'CR', 'Costa Rica'),
(53, 'CI', 'Cote D''Ivoire'),
(54, 'HR', 'Croatia'),
(55, 'CU', 'Cuba'),
(56, 'CY', 'Cyprus'),
(57, 'CZ', 'Czech Republic'),
(58, 'DK', 'Denmark'),
(59, 'DJ', 'Djibouti'),
(60, 'DM', 'Dominica'),
(61, 'DO', 'Dominican Republic'),
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
(74, 'GF', 'French Guiana'),
(75, 'PF', 'French Polynesia'),
(76, 'TF', 'French Southern Territories'),
(77, 'GA', 'Gabon'),
(78, 'GM', 'Gambia'),
(79, 'GE', 'Georgia'),
(80, 'DE', 'Germany'),
(81, 'GH', 'Ghana'),
(82, 'GI', 'Gibraltar'),
(83, 'GR', 'Greece'),
(84, 'GL', 'Greenland'),
(85, 'GD', 'Grenada'),
(86, 'GP', 'Guadeloupe'),
(87, 'GU', 'Guam'),
(88, 'GT', 'Guatemala'),
(89, 'GN', 'Guinea'),
(90, 'GW', 'Guinea-Bissau'),
(91, 'GY', 'Guyana'),
(92, 'HT', 'Haiti'),
(93, 'HM', 'Heard Island and McDonald Islands'),
(94, 'VA', 'Holy See (Vatican City State)'),
(95, 'HN', 'Honduras'),
(96, 'HK', 'Hong Kong'),
(97, 'HU', 'Hungary'),
(98, 'IS', 'Iceland'),
(99, 'IN', 'India'),
(100, 'ID', 'Indonesia'),
(101, 'IR', 'Iran, Islamic Republic of'),
(102, 'IQ', 'Iraq'),
(103, 'IE', 'Ireland'),
(104, 'IL', 'Israel'),
(105, 'IT', 'Italy'),
(106, 'JM', 'Jamaica'),
(107, 'JP', 'Japan'),
(108, 'JO', 'Jordan'),
(109, 'KZ', 'Kazakhstan'),
(110, 'KE', 'Kenya'),
(111, 'KI', 'Kiribati'),
(112, 'KP', 'Korea, Democratic People''s Republic of'),
(113, 'KR', 'Korea, Republic of'),
(114, 'KW', 'Kuwait'),
(115, 'KG', 'Kyrgyzstan'),
(116, 'LA', 'Lao People''s Democratic Republic'),
(117, 'LV', 'Latvia'),
(118, 'LB', 'Lebanon'),
(119, 'LS', 'Lesotho'),
(120, 'LR', 'Liberia'),
(121, 'LY', 'Libyan Arab Jamahiriya'),
(122, 'LI', 'Liechtenstein'),
(123, 'LT', 'Lithuania'),
(124, 'LU', 'Luxembourg'),
(125, 'MO', 'Macao'),
(126, 'MK', 'Macedonia'),
(127, 'MG', 'Madagascar'),
(128, 'MW', 'Malawi'),
(129, 'MY', 'Malaysia'),
(130, 'MV', 'Maldives'),
(131, 'ML', 'Mali'),
(132, 'MT', 'Malta'),
(133, 'MH', 'Marshall Islands'),
(134, 'MQ', 'Martinique'),
(135, 'MR', 'Mauritania'),
(136, 'MU', 'Mauritius'),
(137, 'YT', 'Mayotte'),
(138, 'MX', 'Mexico'),
(139, 'FM', 'Micronesia, Federated States of'),
(140, 'MD', 'Moldova, Republic of'),
(141, 'MC', 'Monaco'),
(142, 'MN', 'Mongolia'),
(143, 'ME', 'Montenegro'),
(144, 'MS', 'Montserrat'),
(145, 'MA', 'Morocco'),
(146, 'MZ', 'Mozambique'),
(147, 'MM', 'Myanmar'),
(148, 'NA', 'Namibia'),
(149, 'NR', 'Nauru'),
(150, 'NP', 'Nepal'),
(151, 'NL', 'Netherlands'),
(152, 'AN', 'Netherlands Antilles'),
(153, 'NC', 'New Caledonia'),
(154, 'NZ', 'New Zealand'),
(155, 'NI', 'Nicaragua'),
(156, 'NE', 'Niger'),
(157, 'NG', 'Nigeria'),
(158, 'NU', 'Niue'),
(159, 'NF', 'Norfolk Island'),
(160, 'MP', 'Northern Mariana Islands'),
(161, 'NO', 'Norway'),
(162, 'OM', 'Oman'),
(163, 'PK', 'Pakistan'),
(164, 'PW', 'Palau'),
(165, 'PS', 'Palestinian Territory, Occupied'),
(166, 'PA', 'Panama'),
(167, 'PG', 'Papua New Guinea'),
(168, 'PY', 'Paraguay'),
(169, 'PE', 'Peru'),
(170, 'PH', 'Philippines'),
(171, 'PN', 'Pitcairn'),
(172, 'PL', 'Poland'),
(173, 'PT', 'Portugal'),
(174, 'PR', 'Puerto Rico'),
(175, 'QA', 'Qatar'),
(176, 'RE', 'Reunion'),
(177, 'RO', 'Romania'),
(178, 'RU', 'Russian Federation'),
(179, 'RW', 'Rwanda'),
(180, 'SH', 'Saint Helena'),
(181, 'KN', 'Saint Kitts and Nevis'),
(182, 'LC', 'Saint Lucia'),
(183, 'PM', 'Saint Pierre and Miquelon'),
(184, 'VC', 'Saint Vincent and the Grenadines'),
(185, 'WS', 'Samoa'),
(186, 'SM', 'San Marino'),
(187, 'ST', 'Sao Tome and Principe'),
(188, 'SA', 'Saudi Arabia'),
(189, 'SN', 'Senegal'),
(190, 'RS', 'Serbia'),
(191, 'SC', 'Seychelles'),
(192, 'SL', 'Sierra Leone'),
(193, 'SG', 'Singapore'),
(194, 'SK', 'Slovakia'),
(195, 'SI', 'Slovenia'),
(196, 'SB', 'Solomon Islands'),
(197, 'SO', 'Somalia'),
(198, 'ZA', 'South Africa'),
(199, 'GS', 'South Georgia'),
(200, 'ES', 'Spain'),
(201, 'LK', 'Sri Lanka'),
(202, 'SD', 'Sudan'),
(203, 'SR', 'Suriname'),
(204, 'SJ', 'Svalbard and Jan Mayen'),
(205, 'SZ', 'Swaziland'),
(206, 'SE', 'Sweden'),
(207, 'CH', 'Switzerland'),
(208, 'SY', 'Syrian Arab Republic'),
(209, 'TW', 'Taiwan, Province Of China'),
(210, 'TJ', 'Tajikistan'),
(211, 'TZ', 'Tanzania, United Republic of'),
(212, 'TH', 'Thailand'),
(213, 'TL', 'Timor-Leste'),
(214, 'TG', 'Togo'),
(215, 'TK', 'Tokelau'),
(216, 'TO', 'Tonga'),
(217, 'TT', 'Trinidad and Tobago'),
(218, 'TN', 'Tunisia'),
(219, 'TR', 'Turkey'),
(220, 'TM', 'Turkmenistan'),
(221, 'TC', 'Turks and Caicos Islands'),
(222, 'TV', 'Tuvalu'),
(223, 'UG', 'Uganda'),
(224, 'UA', 'Ukraine'),
(225, 'AE', 'United Arab Emirates'),
(226, 'GB', 'United Kingdom'),
(227, 'US', 'United States'),
(228, 'UM', 'United States Minor Outlying Islands'),
(229, 'UY', 'Uruguay'),
(230, 'UZ', 'Uzbekistan'),
(231, 'VU', 'Vanuatu'),
(232, 'VE', 'Venezuela'),
(233, 'VN', 'Viet Nam'),
(234, 'VG', 'Virgin Islands, British'),
(235, 'VI', 'Virgin Islands, U.S.'),
(236, 'WF', 'Wallis And Futuna'),
(237, 'EH', 'Western Sahara'),
(238, 'YE', 'Yemen'),
(239, 'ZM', 'Zambia'),
(240, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_email_templates`
--

CREATE TABLE IF NOT EXISTS `abbijan_email_templates` (
  `template_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_message` text COLLATE utf8_unicode_ci NOT NULL,
  `email_variables` text COLLATE utf8_unicode_ci NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Volcado de datos para la tabla `abbijan_email_templates`
--

INSERT INTO `abbijan_email_templates` (`template_id`, `email_name`, `email_title`, `email_subject`, `email_message`, `email_variables`, `modified`) VALUES
(1, 'signup', 'Sign Up email', 'Welcome to our deal of the day site!', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\nDear {first_name},<br /><br />\nThank you for registering!<br /><br />\n\nHere is your login information:<br /><br />\nLogin: <b>{username}</b><br />\nPassword: <b>{password}</b><br /><br />\nPlease <a href=''{login_url}''>click here</a> to login in to your account.<br /><br />Thank you.\n</p>', '{first_name}	- Member First Name<br />\n{username} - Member Username<br />\n{password} - Member Password<br />\n{login_url} - Login Link', '2014-06-16 14:09:48'),
(2, 'activate', 'Activation email', 'Registration Confirmation Email', '<p style=''font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nHi {first_name},<br /><br />\r\nThank you for registering!<br /><br />\r\nHere is your login information:<br /><br />\r\nUsername: <b>{username}</b><br />\r\nPassword: <b>{password}</b><br /><br />\r\n\r\nPlease click the following link to activate your account: <a href=''{activate_link}''>{activate_link}</a><br /><br />Thanks!\r\n</p>', '{first_name} - Member First Name<br />\r\n{username} - Member Username<br />\r\n{password}	- Member Password<br />\r\n{activate_link}	- Activation Link', '2014-06-16 14:09:48'),
(3, 'forgot_password', 'Forgot Password email', 'Forgot password email', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nDear {first_name},<br /><br />\r\nAs you requested, here is new password for your account:<br /><br />\r\nLogin: <b>{username}</b><br />Password: <b>{password}</b> <br /><br />\r\nPlease <a href=''{login_url}''>click here</a> to log in.\r\n<br /><br />\r\nThank you.\r\n</p>', '{first_name} - Member First Name<br />\r\n{username} - Member Username<br />\r\n{password} - Member Password<br />\r\n{login_url}	- Login Link', '2014-06-16 14:09:48'),
(4, 'order_receipt', 'Order Receipt', 'Order Receipt', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nHello {first_name}, <br /><br />\r\nThank you for your order.<br />\r\nYour Order Number: {order_id}<br /><br />\r\n{order_items}<br /><br />\r\nBest Regards.\r\n</p>', '{first_name} - User First Name<br />\r\n{order_id} - Order Reference ID<br />\r\n{order_items}- Order Items List & Order Total', '2014-06-16 14:09:48'),
(5, 'order_status', 'Order status changed', 'Your order updated', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nHello {first_name}, <br /><br />\r\nYour Order #{order_id} status has been updated.<br /><br />\r\nCurrent status: {order_status}<br /><br />\r\nBest Regards.\r\n</p>', '{first_name} - User First Name<br />\r\n{order_id} - Order Reference ID<br />\r\n{order_status}- Order Status', '2014-06-16 14:09:48'),
(6, 'daily_deal_alert', 'Daily Deal Alert', 'Daily Deal Alert', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nHello {first_name}, <br /><br />\r\nNew deal <b>{deal_name}</b> is availiable! Don''t miss out it! Just <b>{deal_price}</b>!<br /><br />\r\n{deal_brief_description}<br /><br />\r\nPrice: <b>{deal_price}</b><br /><br />\r\nSales start date: <b>{deal_start_date}</b><br />\r\n Sales end date: <b>{deal_end_date}</b><br /><br />\r\n <a href=''{deal_url}''>Click here</a> to buy it.\r\n<br /><br />\r\nBest Regards.\r\n</p>', '{first_name} - User First Name<br />\r\n{deal_name}	- Deal Name<br />\r\n{deal_image_url} - Deal Image Link<br />\r\n{deal_brief_description} - Deal Brief Description<br />\r\n{deal_description}	- Deal Description<br />\r\n{deal_start_date} - Deal Start Date<br />\r\n{deal_end_date}	- Deal End Date<br />\r\n{deal_price} - Deal Price<br />\r\n{deal_url} - Deal Link', '2014-06-16 14:09:48'),
(7, 'subscribe', 'Subscription Activation email', 'Subscription Confirmation Email', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nHello new subscriber, <br /><br />\r\nPlease click the following link to activate your subscription: <b>{activate_link}</b><br /><br />\r\nIf you did make this subscription request, then someone else entered your email address.<br />\r\nWe apologize for any inconvenience; please ignore this email.<br /><br />\r\nBest Regards.\r\n</p>', '{activate_link} - activation link', '2014-06-16 14:09:48'),
(8, 'admin_order_alert', 'Admin New Order Alert', 'New order placed', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nA customer has placed an order.<br /><br />\r\n{fname} {lname}<br /><br />\r\n{order_items}\r\n</p>', '{fname} - Cutomer First Name<br />\r\n{lname} - Customer Last Name<br />\r\n{order_items} - Purchased items', '2014-06-16 14:09:48'),
(9, 'admin_testimonial_alert', 'Admin New Testimonial Alert', 'New testimonial added', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nA customer has submitted a Testimonial.<br /><br />\r\nAuthor: {fname} {lname}<br /><br />\r\n{testimonial}\r\n</p>', '{fname} - User First Name<br />\r\n{lname} - User Last Name<br />\r\n{testimonial} - Testimonial', '2014-06-16 14:09:48'),
(10, 'admin_support_alert', 'Admin Support Alert', 'New support ticket created', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nA customer has {request} a Support Ticket.<br /><br />\r\nFrom: {fname} {lname}<br />\r\nSubject: {subject}<br /><br />\r\n{message}\r\n</p>', '{fname} - Sender First Name<br />\r\n{lname} - Sender Last Name<br />\r\n{subject} - Subject<br />\r\n{message} - Message', '2014-06-16 14:09:48'),
(11, 'admin_sold_out_alert', 'Admin Sold Out Alert', 'Deal Sold Out', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nDeal <b>{deal_name}</b> sold out.\r\n</p>', '{deal_name} - Deal Name', '2014-06-16 14:09:48'),
(12, 'admin_expired_alert', 'Admin Expired Deal Alert', 'Deal Expired', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nDeal <b>{deal_name}</b> expired.\r\n</p>', '{deal_name} - Deal Name', '2014-06-16 14:09:48'),
(13, 'admin_comment_alert', 'Admin New Comment Alert', 'New comment', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nUser has submitted a comment.<br /><br />\r\nAuthor: {fname} {lname}<br /><br />\r\n{comment}\r\n</p>', '{fname} - User First Name<br />\r\n{lname} - User Last Name<br />\r\n{comment} - Comment', '2014-06-16 14:09:48'),
(14, 'admin_new_deal_alert', 'Admin New Deal Alert', 'New deal submitted', '<p style=''font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px''>\r\nUser has submitted new deal.<br /><br />\r\nAuthor: {fname} {lname}<br /><br />\r\n{deal_name}\r\n</p>', '{fname} - User First Name<br />\r\n{lname} - User Last Name<br />\r\n{deal_name} - Deal Name', '2014-06-16 14:09:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_faqs`
--

CREATE TABLE IF NOT EXISTS `abbijan_faqs` (
  `faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `answer` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`faq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_favorites`
--

CREATE TABLE IF NOT EXISTS `abbijan_favorites` (
  `favorite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`favorite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_forums`
--

CREATE TABLE IF NOT EXISTS `abbijan_forums` (
  `forum_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `discussion` text COLLATE utf8_unicode_ci NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) unsigned NOT NULL DEFAULT '0',
  `status` enum('active','pending','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_forum_comments`
--

CREATE TABLE IF NOT EXISTS `abbijan_forum_comments` (
  `forum_comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `reply` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','pending','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`forum_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_items`
--

CREATE TABLE IF NOT EXISTS `abbijan_items` (
  `item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `deal_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `thumb` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `quantity` int(8) NOT NULL DEFAULT '0',
  `customer_limit` int(8) NOT NULL DEFAULT '0',
  `retail_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(19,2) NOT NULL DEFAULT '0.00',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conditions` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `brief_description` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `specs` text COLLATE utf8_unicode_ci NOT NULL,
  `youtube_video` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `meta_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `main_deal` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '0',
  `alert_sent` tinyint(1) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','expired','sold') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `abbijan_items`
--

INSERT INTO `abbijan_items` (`item_id`, `forum_id`, `title`, `deal_type`, `url`, `image`, `thumb`, `quantity`, `customer_limit`, `retail_price`, `price`, `discount`, `start_date`, `end_date`, `conditions`, `brief_description`, `description`, `specs`, `youtube_video`, `meta_description`, `meta_keywords`, `featured`, `main_deal`, `allow_comments`, `alert_sent`, `views`, `visits`, `status`, `added`) VALUES
(1, 0, 'adsasdsad', 'own', 'http://', 'deal_83471403069756.jpg', 'deal_83471403069756_thumb.jpg', 0, 0, '0.00', '11.00', '0.00', '0000-00-00 00:00:00', '2099-12-31 00:00:00', '', '', '<p>\r\n	adasdsa</p>\r\n', '', '', '', '', 0, 0, 0, 0, 0, 0, 'active', '2014-06-18 02:35:57'),
(2, 0, 'dfsdfdsf', 'own', 'http://', 'deal_26461403069857.jpg', 'deal_26461403069857_thumb.jpg', 0, 0, '0.00', '33.00', '0.00', '0000-00-00 00:00:00', '2099-12-31 00:00:00', '', '', '<p>\r\n	asdsad</p>\r\n', '', '', '', '', 0, 0, 0, 0, 0, 0, 'active', '2014-06-18 02:36:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_item_images`
--

CREATE TABLE IF NOT EXISTS `abbijan_item_images` (
  `item_image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `thumb_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `medium_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `main_image` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_image_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `abbijan_item_images`
--

INSERT INTO `abbijan_item_images` (`item_image_id`, `item_id`, `image`, `thumb_image`, `medium_image`, `main_image`, `sort_order`) VALUES
(1, 1, 'deal_83471403069756.jpg', 'deal_83471403069756_thumb.jpg', 'deal_83471403069756_medium.jpg', 1, 0),
(2, 2, 'deal_26461403069857.jpg', 'deal_26461403069857_thumb.jpg', 'deal_26461403069857_medium.jpg', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_item_options`
--

CREATE TABLE IF NOT EXISTS `abbijan_item_options` (
  `item_option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT '0',
  `option_id` int(11) NOT NULL DEFAULT '0',
  `option_value` text COLLATE utf8_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_item_option_values`
--

CREATE TABLE IF NOT EXISTS `abbijan_item_option_values` (
  `item_option_value_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_option_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `option_id` int(11) NOT NULL DEFAULT '0',
  `option_value_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(3) NOT NULL DEFAULT '0',
  `substract` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `price_prefix` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`item_option_value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_item_to_category`
--

CREATE TABLE IF NOT EXISTS `abbijan_item_to_category` (
  `item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `abbijan_item_to_category`
--

INSERT INTO `abbijan_item_to_category` (`item_id`, `category_id`) VALUES
(1, 1),
(1, 6),
(2, 5),
(3, 1),
(4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_messages`
--

CREATE TABLE IF NOT EXISTS `abbijan_messages` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `subject` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('new','replied','closed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'new',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_messages_answers`
--

CREATE TABLE IF NOT EXISTS `abbijan_messages_answers` (
  `answer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `answer` text COLLATE utf8_unicode_ci NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `answer_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`answer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_news`
--

CREATE TABLE IF NOT EXISTS `abbijan_news` (
  `news_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `news_description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `abbijan_news`
--

INSERT INTO `abbijan_news` (`news_id`, `news_title`, `news_description`, `status`, `added`) VALUES
(1, 'Site news', 'some site news here.', 'active', '2014-06-16 14:09:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_orders`
--

CREATE TABLE IF NOT EXISTS `abbijan_orders` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `shipping_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `shipping_method_id` int(11) NOT NULL DEFAULT '0',
  `shipping_details` text COLLATE utf8_unicode_ci NOT NULL,
  `payment_method_id` int(11) NOT NULL DEFAULT '0',
  `payment_method` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `payment_details` text COLLATE utf8_unicode_ci NOT NULL,
  `currency` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `shipping_total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `status` enum('pending','complete','shipped','delivered','refunded','declined') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `reason` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `abbijan_orders`
--

INSERT INTO `abbijan_orders` (`order_id`, `reference_id`, `shipping_id`, `user_id`, `shipping_method_id`, `shipping_details`, `payment_method_id`, `payment_method`, `payment_details`, `currency`, `shipping_total`, `total`, `status`, `reason`, `created`, `updated`, `viewed`) VALUES
(1, '42150292', 0, 2, 1, '<br/>,  <br/><br/>sadsad', 0, '', '', 'USD', '9.9900', '33.0000', 'complete', '', '2014-06-20 00:36:44', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_order_items`
--

CREATE TABLE IF NOT EXISTS `abbijan_order_items` (
  `order_item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `item_option_id` int(11) NOT NULL DEFAULT '0',
  `item_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `item_quantity` int(8) NOT NULL DEFAULT '0',
  `item_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`order_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `abbijan_order_items`
--

INSERT INTO `abbijan_order_items` (`order_item_id`, `order_id`, `user_id`, `item_id`, `item_option_id`, `item_title`, `item_quantity`, `item_price`) VALUES
(1, 1, 2, 2, 0, 'dfsdfdsf', 1, '33.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_payment_methods`
--

CREATE TABLE IF NOT EXISTS `abbijan_payment_methods` (
  `payment_method_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pmethod_name` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pmethod_type` enum('withdraw','deposit') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'deposit',
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `min_withdrawal` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `withdrawal_fee` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `min_deposit` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `pmethod_image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pmethod_details` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`payment_method_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `abbijan_payment_methods`
--

INSERT INTO `abbijan_payment_methods` (`payment_method_id`, `pmethod_name`, `pmethod_type`, `title`, `min_withdrawal`, `withdrawal_fee`, `min_deposit`, `pmethod_image`, `pmethod_details`, `description`, `status`) VALUES
(1, 'paypal', 'deposit', 'PayPal', '0.0000', '', '0.0000', 'paypal.png', '', '', 'active'),
(2, 'creditcard', 'deposit', 'Credit Card', '0.0000', '', '0.0000', 'cc.png', '', '', 'active'),
(3, 'account', 'deposit', 'Account Balance', '0.0000', '', '0.0000', '', '', '', 'active');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_reports`
--

CREATE TABLE IF NOT EXISTS `abbijan_reports` (
  `report_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `report` text COLLATE utf8_unicode_ci NOT NULL,
  `reply` text COLLATE utf8_unicode_ci NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','pending','declined') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`report_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_settings`
--

CREATE TABLE IF NOT EXISTS `abbijan_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `setting_value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1067 ;

--
-- Volcado de datos para la tabla `abbijan_settings`
--

INSERT INTO `abbijan_settings` (`setting_id`, `setting_key`, `setting_value`) VALUES
(1000, 'website_title', 'Deal of the Day Site'),
(1001, 'website_url', 'http://www.yourdomain.com/'),
(1002, 'website_home_title', 'Deal of the Day - Grab one today!'),
(1003, 'website_email', 'admin@yourdomain.com'),
(1004, 'website_alerts_email', 'alerts@yourdomain.com'),
(1005, 'website_language', 'english'),
(1006, 'website_currency', '$'),
(1007, 'website_currency_format', '1'),
(1008, 'website_currency_code', 'USD'),
(1009, 'maintenance_mode', '0'),
(1010, 'website_timezone', '-6'),
(1011, 'countdown_compact', 'false'),
(1012, 'countdown_format', 'DHMS'),
(1013, 'countdown_layout', '{dn} days  {hn} hours  {mn} minutes  {sn} seconds'),
(1014, 'paypal_account', 'payments@yourdomain.com'),
(1015, 'email_new_order', '1'),
(1016, 'email_new_ticket', '0'),
(1017, 'email_deal_expired', '0'),
(1018, 'email_sold_out', '1'),
(1019, 'email_new_comment', '0'),
(1020, 'email_new_testimonial', '0'),
(1021, 'email_new_deal', '1'),
(1022, 'cc_gateway', 'paypal'),
(1023, 'paypal_ipn', '1'),
(1024, 'paypal_api_username', ''),
(1025, 'paypal_api_password', ''),
(1026, 'paypal_api_signature', ''),
(1027, 'refer_credit', '5'),
(1028, 'min_payout', '30'),
(1029, 'block_same_ip', '0'),
(1030, 'show_random', '1'),
(1031, 'show_sales_stats', '1'),
(1032, 'show_stats', '1'),
(1033, 'show_quantity', '1'),
(1034, 'show_stock_bar', '1'),
(1035, 'allow_avatars', '1'),
(1036, 'avatar_width', '50'),
(1037, 'avatar_height', '50'),
(1038, 'checkout_reservation_time', '30'),
(1039, 'on_comments', '1'),
(1040, 'results_per_page', '10'),
(1041, 'discussions_per_page', '20'),
(1042, 'news_per_page', '10'),
(1043, 'testimonials_per_page', '7'),
(1044, 'comments_per_page', '20'),
(1045, 'comments_approve', '0'),
(1046, 'max_comment_length', '300'),
(1047, 'other_deals_results', '6'),
(1048, 'sidebar_results', '3'),
(1049, 'past_deals_results', '15'),
(1050, 'thumb_width', '80'),
(1051, 'thumb_height', '80'),
(1052, 'medium_image_width', '360'),
(1053, 'medium_image_height', '260'),
(1054, 'small_image_width', '50'),
(1055, 'small_image_height', '50'),
(1056, 'facebook_url', ''),
(1057, 'facebook_box', '1'),
(1058, 'facebook_app_id', ''),
(1059, 'twitter_url', ''),
(1060, 'gplus_url', ''),
(1061, 'pinterest_url', ''),
(1062, 'tumblr_url', ''),
(1063, 'google_analytics', ''),
(1064, 'license', '2127-1908-6796-5753-7895'),
(1065, 'word', '30c1942caee2b672bcf67a1dc8c7956d'),
(1066, 'iword', 'whiTEBunny557');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_shipping`
--

CREATE TABLE IF NOT EXISTS `abbijan_shipping` (
  `shipping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `shipping_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fname` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lname` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address2` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zip` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`shipping_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `abbijan_shipping`
--

INSERT INTO `abbijan_shipping` (`shipping_id`, `user_id`, `shipping_name`, `fname`, `lname`, `address`, `address2`, `city`, `state`, `zip`, `country`, `phone`, `modified`) VALUES
(1, 2, 'My Shipping Address', 'Sdfds', 'Asdasd', '', '', '', '', '', '', 'sadsad', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_shipping_methods`
--

CREATE TABLE IF NOT EXISTS `abbijan_shipping_methods` (
  `shipping_method_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `countries` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `delivery_time` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `first_item_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `next_item_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `free_shipping_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`shipping_method_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `abbijan_shipping_methods`
--

INSERT INTO `abbijan_shipping_methods` (`shipping_method_id`, `title`, `countries`, `delivery_time`, `first_item_cost`, `next_item_cost`, `cost`, `free_shipping_cost`, `description`, `status`) VALUES
(1, 'Express', 'all', '', '0.00', '0.00', '9.99', '0.00', '', 'active');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_subscribers`
--

CREATE TABLE IF NOT EXISTS `abbijan_subscribers` (
  `subscriber_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `unsubscribe_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`subscriber_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_testimonials`
--

CREATE TABLE IF NOT EXISTS `abbijan_testimonials` (
  `testimonial_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `author` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `testimonial` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`testimonial_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_transactions`
--

CREATE TABLE IF NOT EXISTS `abbijan_transactions` (
  `transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `recipient_id` int(11) NOT NULL DEFAULT '0',
  `payment_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `payment_method` int(10) NOT NULL DEFAULT '0',
  `payment_details` text COLLATE utf8_unicode_ci NOT NULL,
  `transaction_fee` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `currency` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `amount` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `status` enum('pending','paid','request','declined') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `process_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abbijan_users`
--

CREATE TABLE IF NOT EXISTS `abbijan_users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fname` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lname` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `nickname` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `company` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address2` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `state` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zip` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_id` int(11) NOT NULL DEFAULT '0',
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `balance` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `show_as` tinyint(1) NOT NULL DEFAULT '1',
  `ref_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ref_bonus` tinyint(1) NOT NULL DEFAULT '0',
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `activation_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_count` int(8) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `block_reason` tinytext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `abbijan_users`
--

INSERT INTO `abbijan_users` (`user_id`, `username`, `password`, `email`, `fname`, `lname`, `nickname`, `avatar`, `company`, `address`, `address2`, `city`, `state`, `zip`, `country_id`, `phone`, `balance`, `show_as`, `ref_id`, `ref_bonus`, `newsletter`, `ip`, `status`, `activation_key`, `last_login`, `login_count`, `last_ip`, `created`, `block_reason`) VALUES
(1, 'test', '202cb962ac59075b964b07152d234b70', 'kljlkj@lkjklj.com', 'Jlkj', 'Lkjlkj', '', 'no_avatar.png', '', '', '', '', '', '', 193, '1', '0.0000', 1, 0, 0, 0, '127.0.0.1', 'active', 'b32d89b6df0860b22cd1b632fc7c8a60', '2014-06-18 02:59:15', 1, '127.0.0.1', '2014-06-18 02:55:31', ''),
(2, 'adanzweig@gmail.com', '74b293efad9155dacc77972d48a383ca', 'adanzweig@gmail.com', 'Adan', 'Zweig', '', 'no_avatar.png', '', '', '', '', '', '', 13, '01 342 343 2432', '0.0000', 1, 0, 0, 0, '127.0.0.1', 'active', 'a2263fc205fde391360b7732db36f0ae', '2014-06-20 00:28:51', 1, '127.0.0.1', '2014-06-20 00:28:21', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
