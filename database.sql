-- phpMyAdmin SQL Dump
-- version 4.4.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 29, 2015 at 01:44 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `the-problem`
--

-- --------------------------------------------------------

--
-- Table structure for table `bugs`
--

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

CREATE TABLE IF NOT EXISTS `comments` (
  `Comment_ID` int(11) NOT NULL,
  `Bug_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  `Edit_Date` datetime DEFAULT NULL,
  `Comment_Text` text COLLATE latin1_general_cs NOT NULL,
  `Raw_Text` longtext COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `Type` varchar(50) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Value` text COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`Type`, `Name`, `Value`) VALUES
  ('overview-name', 'sitename', 'The Problem'),
  ('overview-visibility', 'registration', 'open'),
  ('overview-visibility', 'visibility', 'public'),
  ('permissions-default-bugs', 'assigning', '2'),
  ('permissions-default-bugs', 'create', '1'),
  ('permissions-default-bugs', 'delete', '3'),
  ('permissions-default-bugs', 'edit', '2'),
  ('permissions-default-bugs', 'status', '2'),
  ('permissions-default-bugs', 'view', '0'),
  ('permissions-default-comments', 'create', '1'),
  ('permissions-default-comments', 'delete', '3'),
  ('permissions-default-comments', 'edit', '2'),
  ('permissions-default-comments', 'upvote', '1'),
  ('permissions-default-section', 'view', '0');

-- --------------------------------------------------------

--
-- Table structure for table `cookies`
--

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

CREATE TABLE IF NOT EXISTS `developers` (
  `Section_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `developers`
--

INSERT INTO `developers` (`Section_ID`, `Username`) VALUES
  (1, 'Andrew'),
  (1, 'dr2n'),
  (2, 'dr2n'),
  (2, 'exterminate'),
  (3, 'exterminate'),
  (3, 'mrfishie'),
  (4, 'mrfishie'),
  (2, 'unhelpful');

-- --------------------------------------------------------

--
-- Table structure for table `grouppermissions`
--

CREATE TABLE IF NOT EXISTS `grouppermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Rank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `grouppermissions`
--

INSERT INTO `grouppermissions` (`Object_ID`, `Permission_Name`, `Rank`) VALUES
  (0, 'site.view', 0),
  (1, 'section.view', 0),
  (2, 'section.view', 0),
  (3, 'section.view', 0),
  (4, 'section.view', 0),
  (5, 'section.view', 0),
  (21, 'bug.comment', 1),
  (22, 'bug.comment', 1),
  (23, 'bug.comment', 1),
  (24, 'bug.comment', 1),
  (25, 'bug.comment', 1),
  (26, 'bug.comment', 1),
  (27, 'bug.comment', 1),
  (28, 'bug.comment', 1),
  (29, 'bug.comment', 1),
  (30, 'bug.comment', 1),
  (31, 'bug.comment', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `Notification_ID` int(11) NOT NULL,
  `Triggered_By` varchar(20) COLLATE latin1_general_cs DEFAULT NULL,
  `Received_By` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Target_One` int(11) NOT NULL,
  `Target_Two` int(11) DEFAULT NULL,
  `Creation_Date` datetime NOT NULL,
  `IsRead` tinyint(1) NOT NULL DEFAULT '0',
  `Type` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`Notification_ID`, `Triggered_By`, `Received_By`, `Target_One`, `Target_Two`, `Creation_Date`, `IsRead`, `Type`) VALUES
  (1, 'mrfishie', 'mrfishie', 32, NULL, '2015-08-29 05:03:57', 0, 4),
  (2, 'mrfishie', 'mrfishie', 32, NULL, '2015-08-29 05:03:59', 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `Object_ID` int(11) NOT NULL,
  `Object_Type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `objects`
--

INSERT INTO `objects` (`Object_ID`, `Object_Type`) VALUES
  (0, -1),
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
  (32, 2);

-- --------------------------------------------------------

--
-- Table structure for table `plusones`
--

CREATE TABLE IF NOT EXISTS `plusones` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `Section_ID` int(11) NOT NULL,
  `Name` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Description` text COLLATE latin1_general_cs NOT NULL,
  `Slug` text COLLATE latin1_general_cs NOT NULL,
  `Color` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

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

CREATE TABLE IF NOT EXISTS `userpermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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
  ('Andrew', 'fireme@mailinator.net', 'Andrew', '$2y$10$o0bQZMYLGzVNqexQ9X5mTeGVTFLEtmdg2cRiyyWtlOzeAgGk6tUlm', 3, '', NULL),
  ('bullseye', 'bullseye@mailinator.net', 'Mr T', '$2y$10$/WfS9BksZkg7ca3CCVTIsOGapLHFp1NH/HRKpITBqp.MPKkO/0ZXW', 1, '', NULL),
  ('dr2n', 'darren.yx.fu@gmail.com', 'Darren Fu', '$2y$10$wKEIz4twWBU4zwd.1ufuHuN.903OQleNp3VK8CcwDvQM0KwODp9pC', 4, '\r\n\r\nOften with a love of the French language comes a love of French culture and an interest in the way the French-speaking world does things differently to us.\r\n\r\nOur seasonal events are designed to bring together like-minded people in a social situation while enjoying the best food, wine, film, fashion (and much more) the French have to offer.\r\n\r\nFor all levels and interests - there’s be something for everybody...even a trip to Nouméa in April 2015, too. There will also be a three-day trip to a secret destination for a French Festival in October 2015. Let us know if you''d like to be kept in the loop.\r\n', '2015-08-28 20:04:57'),
  ('exterminate', 'david@mailinator.net', 'David', '$2y$10$GRuSi8B2e3pG2RcuQ9TemuwaUBa.qzepKjgAJjcmMLa8DDpgRtOTe', 1, '', NULL),
  ('flame', 'flame@mailinator.net', 'Andria', '$2y$10$8.xBNUavPm7.hD3iG6QkxuNithxCGTMcgpUWIIuVRvrtVsbFAcWsO', 1, '', NULL),
  ('gottatrythis', 'gtythis@mailinator.net', 'Donald', '$2y$10$YqzKYPd1hQyDuIpbXu1pbeFIxnUGKoMKeq8e0rW5UNpzns1XkxqqG', 3, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', '2015-08-28 17:32:21'),
  ('hackers', 'davee3@mailinator.net', 'Dave', '$2y$10$UDb3Zn8D0yjl2zo5p2AHJeG54z8XzbGy3fi3lYGQkO065cfpkQ8tm', 1, '', '2015-08-26 21:23:20'),
  ('Jas', 'jjj@mailinator.net', 'Jas', '$2y$10$iZ2eKISWEvIQ7xyTElt.Terl.w9AvZSdFg4Ui3UZ/TLfFt4uh5k1a', 1, '', NULL),
  ('jackass', 'jackass@mailinator.net', 'Jake Peearaa', '$2y$10$Doo6SZyzZmrxdKPBpJYYaeNk0fhiIGcXvR.9lJcU9Pmd4Y5WGSQKq', 1, '', NULL),
  ('KaiXinGuo', 'kai@mailinator.net', 'Kai', '$2y$10$cA9eTBMpI5yZqkVJMMrrgubMMfdeNATCpV4DCziDNowlHdCTs9nfG', 1, '', NULL),
  ('KatieLilly', 'jrn@mailinator.net', 'Katie Lilly', '$2y$10$NXiQVXX/ABzC/UXdR/YWMOjlqOO7HwJ2.oifS1GdC8Ial9RtqjyxS', 1, '', NULL),
  ('Liam', 'LIAM@mailinator.net', 'Liam Prok', '$2y$10$.rnGQiCFaato0Km1.HJ5wenLgu/C5F.3hz/4mSvK33xWUsM3TCymK', 1, '', '2015-08-26 21:09:09'),
  ('MichaelK', 'mike@mailinator.net', 'Michael', '$2y$10$vT7TaMeRGk6oomeNsB3AWOPB/f7lA9YsAWPtfxcoljF1OTOmqOB3y', 1, '', NULL),
  ('madEagle', 'knights@mailinator.net', 'KnightsOfTheRoundTable', '$2y$10$Qgi2LRFQut0WV90Qz4omWe79Kh6ZUlmSxY.zB7/aFrvX1q8qfbxha', 1, 'Introducing I, knightsOfTheRoundTable, the first user on The Problem to take advantage of hashed password security!!.', '2015-08-28 21:26:50'),
  ('meltingPoint', 'mp@mailinator.net', 'Jess', '$2y$10$N87Mlr4M/ITnZYEO7WOk.ePLQlaSHAMNbX.8mtBLLc42ehkWGaH5W', 1, '', NULL),
  ('mrfishie', 'mrfishie101@hotmail.com', 'Tom', '$2y$10$c/qtBe2YnScne15pFX0fce88pwYbdmwdoJQCLqKCR7H1PBOijh7YG', 4, 'Hi! I make websites and lights do cool things.', NULL),
  ('No.', 'nonono@no.com.au', 'No.', '$2y$10$pjEvx04jAMTHFLVd59WwD./ONCOJfLY.i.B2D.EhhoonLnAMa4why', 1, '', '2015-08-28 19:51:43'),
  ('powerRangers46', 'pewpew@mailinator.net', 'Zac Langlands', '$2y$10$HZ8d0bep.Vie7VyAmsk9i.yy.DEFN8cIUQqUcJXpFE28iKN0QUfqe', 1, '', NULL),
  ('SmithJohn', 'jsjs@mailinator.net', 'John Smith', '$2y$10$yA0PIxwKiSbrrLom7outhemOtfu9ksmqUQ/oWnYV/oP6zfKq6712O', 1, 'There''s probably not a single person in this development team that''s as skilled as I am in making tea.', NULL),
  ('that''sMe', 'hu@mailinator.net', 'DoctorHu', '$2y$10$aetQkTLivZd1/ZnlX2ah8e3AvFHJzpQ3U.tXCPc18LC4b.HBSdKji', 1, '', NULL),
  ('unhelpful', 'unhelpful@mailinator.net', 'Ben Loungin', '$2y$10$lxONG2iNdaphSGdE77G.Ee9hhhbFHUr7GI5cd55mWXRJU0y8LtKaa', 1, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `watchers`
--

CREATE TABLE IF NOT EXISTS `watchers` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `watchers`
--

INSERT INTO `watchers` (`Object_ID`, `Username`) VALUES
  (1, 'dr2n'),
  (5, 'dr2n'),
  (29, 'mrfishie'),
  (32, 'mrfishie');

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
MODIFY `Comment_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `cookies`
--
ALTER TABLE `cookies`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `objects`
--
ALTER TABLE `objects`
MODIFY `Object_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
MODIFY `Section_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
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
