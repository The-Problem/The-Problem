-- phpMyAdmin SQL Dump
-- version 4.4.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 19, 2015 at 12:00 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `the-problem`
--

-- --------------------------------------------------------

--
-- Table structure for table `bugs`
--

DROP TABLE IF EXISTS `bugs`;
CREATE TABLE IF NOT EXISTS `bugs` (
  `Bug_ID` int(11) NOT NULL,
  `Section_ID` int(11) NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Name` text COLLATE latin1_general_cs NOT NULL,
  `Status` int(11) NOT NULL,
  `Description` text COLLATE latin1_general_cs,
  `Creation_Date` datetime NOT NULL,
  `Author` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Edit_Date` datetime DEFAULT NULL,
  `RID` int(11) NOT NULL,
  `Assigned` varchar(20) COLLATE latin1_general_cs DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `bugs`
--

INSERT INTO `bugs` (`Bug_ID`, `Section_ID`, `Object_ID`, `Name`, `Status`, `Description`, `Creation_Date`, `Author`, `Edit_Date`, `RID`, `Assigned`) VALUES
(43, 1, 21, 'Login Button Disappearing', 0, 'The login button disappears from the screen whenever I try to click on it. I can''t click on it and haven''t been able to log on for three weeks. Very annoying, please fix ASAP.', '2015-08-01 00:00:00', 'exterminate', '2015-08-01 00:00:00', 1, 'Andrew'),
(44, 1, 22, 'Confusion between users.', 2, 'User profile pages are being filled with details from other users. The DR2N profile page shows the avatar of Zac.', '2015-08-12 00:00:00', 'Jas', '2015-08-12 07:00:00', 2, 'Andrew'),
(45, 1, 23, 'Can''t Change Password', 1, 'There is no option to change my password. I accidentally typed in my password while saying it out loud over the intercom and now everyone is able to log in.', '2015-08-14 00:00:00', 'jackass', '2015-08-14 00:00:00', 3, 'KaiXinGuo'),
(46, 2, 26, 'Limited Character Support', 1, 'Many symbols show up as rectangles when typed out as section names.', '2015-08-13 00:00:00', 'bullseye', '2015-08-13 00:00:00', 1, 'Jas'),
(47, 2, 27, 'Cover Pixelation', 4, 'Cover photo in sections appear to be a pixelated mess. You can''t even make out my face in this one.', '2015-08-04 00:00:00', 'meltingPoint', '2015-08-04 00:00:00', 2, 'Jas'),
(48, 2, 28, 'No Colours', 2, 'All sections are in black and white.', '2015-08-01 00:00:00', 'powerRangers46', '2015-08-01 00:22:00', 3, 'dr2n'),
(49, 3, 29, 'Sections Don''t Load', 4, 'There''s an error message saying ''STATEWIDE BLOCK'' after I log on in the home page. I can''t see the sections that I''m developing in.', '2015-08-15 06:24:19', 'flame', '2015-08-15 12:03:11', 1, 'Andrew'),
(52, 3, 30, 'Statewide Block', 3, 'Section tiles on the home page are all saying ''Statewide Block''.', '2015-08-15 17:00:00', 'unhelpful', '2015-08-15 17:00:00', 2, 'Andrew'),
(53, 3, 31, 'Improvement: Rainbow Background', 0, 'A rainbow background on the homepage of The Problem would make everyone''s lives much happier. This is a much needed feature.', '2015-08-04 00:00:00', 'dr2n', '2015-08-04 00:00:00', 3, 'KaiXinGuo'),
(55, 1, 24, 'Oversized Buttons', 4, 'Buttons on the logon page are the size of the entire screen.', '2015-08-12 00:00:00', 'meltingPoint', '2015-08-12 00:00:00', 4, 'dr2n'),
(56, 1, 25, 'Character Jumble', 1, 'All the characters in my username have become jumbled up.', '2015-08-05 00:00:00', 'powerRangers46', '2015-08-05 00:00:00', 5, 'Jas');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `Comment_ID` int(11) NOT NULL,
  `Bug_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  `Edit_Date` datetime DEFAULT NULL,
  `Comment_Text` text COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`Comment_ID`, `Bug_ID`, `Username`, `Object_ID`, `Creation_Date`, `Edit_Date`, `Comment_Text`) VALUES
(1, 44, 'Andrew', 32, '2015-08-13 00:00:00', '2015-08-13 00:00:00', 'I''m looking into your issue now. It looks like there are some issues in our database.'),
(2, 44, 'Andrew', 33, '2015-08-14 00:00:00', '2015-08-14 00:00:00', 'The issue looks from my side to be resolved. Please confirm that the details on profile pages are showing correctly on your side.'),
(3, 44, 'Jas', 34, '2015-08-14 02:00:00', '2015-08-14 03:00:00', 'All good! Thanks.'),
(4, 45, 'Andrew', 35, '2015-08-14 15:00:00', '2015-08-14 17:00:00', 'We''ll be working to implement the feature as quickly as we can. In the meantime, we''ve disabled your account so that other users can''t use The Problem in your name.'),
(5, 45, 'jackass', 35, '2015-08-15 10:00:00', '2015-08-15 12:00:00', 'Well then why I am still able to post this?');

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
CREATE TABLE IF NOT EXISTS `configuration` (
  `Type` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Value` text COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`Type`, `Name`, `Value`) VALUES
('overview-name', 'sitename', 'The Problem'),
('overview-visibility', 'registration', 'open'),
('overview-visibility', 'visibility', 'public');

-- --------------------------------------------------------

--
-- Table structure for table `cookies`
--

DROP TABLE IF EXISTS `cookies`;
CREATE TABLE IF NOT EXISTS `cookies` (
  `id` int(11) NOT NULL,
  `name` text COLLATE latin1_general_cs NOT NULL,
  `uniqid` text COLLATE latin1_general_cs NOT NULL,
  `value` text COLLATE latin1_general_cs NOT NULL,
  `timeout` datetime DEFAULT NULL,
  `type` text COLLATE latin1_general_cs NOT NULL,
  `domain` text COLLATE latin1_general_cs NOT NULL,
  `http` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `developers`
--

DROP TABLE IF EXISTS `developers`;
CREATE TABLE IF NOT EXISTS `developers` (
  `Section_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `developers`
--

INSERT INTO `developers` (`Section_ID`, `Username`) VALUES
(1, 'Andrew'),
(2, 'exterminate'),
(3, 'exterminate'),
(3, 'mrfishie'),
(4, 'mrfishie'),
(2, 'unhelpful');

-- --------------------------------------------------------

--
-- Table structure for table `grouppermissions`
--

DROP TABLE IF EXISTS `grouppermissions`;
CREATE TABLE IF NOT EXISTS `grouppermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Rank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `Notification_ID` int(11) NOT NULL,
  `Triggered_By` varchar(20) COLLATE latin1_general_cs DEFAULT NULL,
  `Received_By` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Target_One` int(11) NOT NULL,
  `Target_Two` int(11) DEFAULT NULL,
  `Creation_Date` datetime NOT NULL,
  `IsRead` tinyint(1) NOT NULL DEFAULT '0',
  `Type` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `objects`
--

DROP TABLE IF EXISTS `objects`;
CREATE TABLE IF NOT EXISTS `objects` (
  `Object_ID` int(11) NOT NULL,
  `Object_Type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `objects`
--

INSERT INTO `objects` (`Object_ID`, `Object_Type`) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0),
(5, 0),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2);

-- --------------------------------------------------------

--
-- Table structure for table `plusones`
--

DROP TABLE IF EXISTS `plusones`;
CREATE TABLE IF NOT EXISTS `plusones` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `Section_ID` int(11) NOT NULL,
  `Name` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Description` text COLLATE latin1_general_cs NOT NULL,
  `Slug` text COLLATE latin1_general_cs NOT NULL,
  `Color` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`Section_ID`, `Name`, `Object_ID`, `Description`, `Slug`, `Color`) VALUES
(1, 'Users', 1, 'The user management system in The Problem.', 'users', 6),
(2, 'Sections', 2, 'Bug sections which are in The Problem.', 'sections', 0),
(3, 'Home', 3, 'The home page for The Problem.', 'homepage', 11),
(4, 'User Permissions', 4, 'User permission management system that works in The Problem.', 'user-permissions', 9),
(5, 'Notifications', 5, 'Notification system in The Problem notifying users of bug assignment, commenting, +1 and section activities as they occur.', 'notifications', 14);

-- --------------------------------------------------------

--
-- Table structure for table `userpermissions`
--

DROP TABLE IF EXISTS `userpermissions`;
CREATE TABLE IF NOT EXISTS `userpermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Email` text COLLATE latin1_general_cs NOT NULL,
  `Name` text COLLATE latin1_general_cs NOT NULL,
  `Password` text COLLATE latin1_general_cs NOT NULL,
  `Rank` int(11) NOT NULL,
  `Bio` text COLLATE latin1_general_cs NOT NULL,
  `Last_Logon_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Username`, `Email`, `Name`, `Password`, `Rank`, `Bio`, `Last_Logon_Time`) VALUES
('Andrew', 'fireme@mailinator.net', 'Andrew', 'pleaseBoss1', 0, '', NULL),
('bullseye', 'bullseye@mailinator.net', 'Mr T', 'againNagain0', 0, '', NULL),
('dr2n', 'darren.yx.fu@gmail.com', 'Darren Fu', 'superPiggy46', 4, 'Im working on it', '2015-08-22 12:04:06'),
('exterminate', 'david@mailinator.net', 'David', 'sleepyDog2', 0, '', NULL),
('flame', 'flame@mailinator.net', 'Andria', 'noOneCanSee6', 0, '', NULL),
('Jas', 'jjj@mailinator.net', 'Jas', 'rightio3', 0, '', NULL),
('jackass', 'jackass@mailinator.net', 'Jake Peearaa', 'moon3Shadow', 0, '', NULL),
('KaiXinGuo', 'kai@mailinator.net', 'Kai', 'wellThi5IsAProblem', 0, '', NULL),
('KatieLilly', 'jrn@mailinator.net', 'Katie Lilly', 'flashCookies1994372', 0, '', NULL),
('Liam', 'LIAM@mailinator.net', 'Liam Prok', 'helloW0rld', 0, '', NULL),
('meltingPoint', 'mp@mailinator.net', 'Jess', '4myDreamz', 0, '', NULL),
('mrfishie', 'mrfishie101@hotmail.com', 'Tom', 'correct horse battery staple', 4, 'Hi! I make websites and lights do cool things.', NULL),
('powerRangers46', 'pewpew@mailinator.net', 'Zac Langlands', 'Lo000L', 0, '', NULL),
('unhelpful', 'unhelpful@mailinator.net', 'Ben Loungin', 'yjhghtd44790vjhg', 0, '', NULL),
('MichaelK', 'mike@mailinator.net', 'Michael', 'don''tBePicky1', 0, '', NULL),
('SmithJohn', 'jsjs@mailinator.net', 'John Smith', 'joinin768', 0, 'There''s probably not a single person in this development team that''s as skilled as I am in making tea.', NULL),
('that''sMe', 'hu@mailinator.net', 'DoctorHu', 'firefox555', 0, '', NULL);


-- --------------------------------------------------------

--
-- Table structure for table `watchers`
--

DROP TABLE IF EXISTS `watchers`;
CREATE TABLE IF NOT EXISTS `watchers` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bugs`
--
ALTER TABLE `bugs`
  ADD PRIMARY KEY (`Bug_ID`),
  ADD KEY `Section_ID` (`Section_ID`),
  ADD KEY `Object_ID` (`Object_ID`),
  ADD KEY `Author` (`Author`),
  ADD KEY `Assigned` (`Assigned`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Comment_ID`),
  ADD KEY `Bug_ID` (`Bug_ID`),
  ADD KEY `Username` (`Username`),
  ADD KEY `Object_ID` (`Object_ID`);

--
-- Indexes for table `configuration`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`Type`,`Name`);

--
-- Indexes for table `cookies`
--
ALTER TABLE `cookies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `developers`
--
ALTER TABLE `developers`
  ADD PRIMARY KEY (`Section_ID`,`Username`),
  ADD KEY `Username` (`Username`);

--
-- Indexes for table `grouppermissions`
--
ALTER TABLE `grouppermissions`
  ADD PRIMARY KEY (`Object_ID`,`Permission_Name`) USING BTREE;

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`Notification_ID`),
  ADD KEY `Triggered_By` (`Triggered_By`),
  ADD KEY `Received_By` (`Received_By`),
  ADD KEY `Target_One` (`Target_One`),
  ADD KEY `Target_Two` (`Target_Two`);

--
-- Indexes for table `objects`
--
ALTER TABLE `objects`
  ADD PRIMARY KEY (`Object_ID`);

--
-- Indexes for table `plusones`
--
ALTER TABLE `plusones`
  ADD PRIMARY KEY (`Object_ID`,`Username`),
  ADD KEY `Username` (`Username`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`Section_ID`),
  ADD KEY `Object_ID` (`Object_ID`);

--
-- Indexes for table `userpermissions`
--
ALTER TABLE `userpermissions`
  ADD PRIMARY KEY (`Object_ID`,`Permission_Name`,`Username`),
  ADD KEY `Username` (`Username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `watchers`
--
ALTER TABLE `watchers`
  ADD PRIMARY KEY (`Object_ID`),
  ADD KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bugs`
--
ALTER TABLE `bugs`
  MODIFY `Bug_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Comment_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `cookies`
--
ALTER TABLE `cookies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `objects`
--
ALTER TABLE `objects`
  MODIFY `Object_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `Section_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bugs`
--
ALTER TABLE `bugs`
  ADD CONSTRAINT `bugs_ibfk_1` FOREIGN KEY (`Section_ID`) REFERENCES `sections` (`Section_ID`),
  ADD CONSTRAINT `bugs_ibfk_2` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `bugs_ibfk_3` FOREIGN KEY (`Author`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `bugs_ibfk_4` FOREIGN KEY (`Assigned`) REFERENCES `users` (`Username`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`Bug_ID`) REFERENCES `bugs` (`Bug_ID`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `developers`
--
ALTER TABLE `developers`
  ADD CONSTRAINT `developers_ibfk_1` FOREIGN KEY (`Section_ID`) REFERENCES `sections` (`Section_ID`),
  ADD CONSTRAINT `developers_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);

--
-- Constraints for table `grouppermissions`
--
ALTER TABLE `grouppermissions`
  ADD CONSTRAINT `grouppermissions_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`Triggered_By`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`Received_By`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`Target_One`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`Target_Two`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `plusones`
--
ALTER TABLE `plusones`
  ADD CONSTRAINT `plusones_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `plusones_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `userpermissions`
--
ALTER TABLE `userpermissions`
  ADD CONSTRAINT `userpermissions_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `userpermissions_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);

--
-- Constraints for table `watchers`
--
ALTER TABLE `watchers`
  ADD CONSTRAINT `watchers_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `watchers_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
