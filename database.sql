-- phpMyAdmin SQL Dump
-- version 4.4.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 30, 2015 at 09:43 AM
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
  `Author` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `Edit_Date` datetime DEFAULT NULL,
  `RID` int(11) NOT NULL,
  `Assigned` varchar(20) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `bugs`
--

INSERT INTO `bugs` (`Bug_ID`, `Section_ID`, `Object_ID`, `Name`, `Status`, `Description`, `Creation_Date`, `Author`, `Edit_Date`, `RID`, `Assigned`) VALUES
  (1, 1, 7, 'Login Button Disappearing', 1, '<p>The login button disappears from the screen whenever I try to click on it. I can''t click on it and haven''t been able to log on for three weeks. Very annoying, please fix ASAP.</p>', '2015-08-30 05:14:09', 'mrfishie', NULL, 1, NULL),
  (2, 1, 8, 'Confusion between users', 1, '<p>User profile pages are being filled with details from other users. The <a href="http://localhost/The-Problem/www/%7Edr2n/">@dr2n</a> profile page shows the avatar of @zac</p>', '2015-08-30 05:15:22', 'mrfishie', NULL, 2, NULL),
  (3, 1, 9, 'Can''t Change Password', 1, '<p>There is no option to change my password. I accidentally typed in my password while saying it out loud over the intercom and now everyone is able to log in.</p>', '2015-08-30 05:18:33', 'mrfishie', NULL, 3, NULL),
  (4, 2, 10, 'Limited Character Support', 1, '<p>Many symbols show up as rectangles when typed out as section names.</p>', '2015-08-30 05:19:04', 'mrfishie', NULL, 1, NULL),
  (5, 2, 11, 'Cover Pixelation', 1, '<p>Cover photo in sections appear to be a pixelated mess. You can''t even make out my face in this one.</p>', '2015-08-30 05:19:43', 'mrfishie', NULL, 2, NULL),
  (6, 2, 12, 'No Colours', 1, '<p>All sections are in black and white.</p>', '2015-08-30 05:20:08', 'mrfishie', NULL, 3, NULL),
  (7, 2, 13, 'Sections Don''t Load', 1, '<p>There''s an error message saying &quot;STATEWIDE BLOCK&quot; after I log on in the home page. I can''t see the sections that I''m developing in.</p>', '2015-08-30 05:20:58', 'mrfishie', NULL, 4, NULL),
  (8, 3, 14, 'Statewide Block', 1, '<p>Section tiles on the home page are all saying &quot;Statewide Block&quot;.</p>', '2015-08-30 05:22:20', 'mrfishie', NULL, 1, NULL),
  (9, 3, 15, 'Improvement: Rainbow Background', 1, '<p>A rainbow background on the homepage of The Problem would make everyone''s lives much happier. This is a much needed feature.</p>', '2015-08-30 05:23:09', 'mrfishie', NULL, 2, NULL),
  (10, 1, 16, 'Oversized Buttons', 1, '<p>Buttons on the logon page are the size of the entire screen.</p>', '2015-08-30 05:23:32', 'mrfishie', NULL, 4, NULL),
  (11, 1, 17, 'Character Jumble', 1, '<p>All the characters in my username have become jumbled up.</p>', '2015-08-30 05:24:00', 'mrfishie', NULL, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `Comment_ID` int(11) NOT NULL,
  `Bug_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  `Edit_Date` datetime DEFAULT NULL,
  `Comment_Text` text COLLATE latin1_general_cs NOT NULL,
  `Raw_Text` longtext COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

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
  `Username` varchar(20) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `developers`
--

INSERT INTO `developers` (`Section_ID`, `Username`) VALUES
  (1, 'dr2n'),
  (4, 'dr2n'),
  (5, 'dr2n'),
  (1, 'exterminate'),
  (2, 'exterminate'),
  (2, 'mrfishie'),
  (3, 'mrfishie'),
  (4, 'mrfishie');

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
  (2, 'section.create-bug', 1),
  (2, 'section.view', 0),
  (3, 'section.create-bug', 1),
  (3, 'section.view', 0),
  (4, 'section.create-bug', 1),
  (4, 'section.view', 0),
  (5, 'section.create-bug', 1),
  (5, 'section.view', 0),
  (6, 'section.create-bug', 1),
  (6, 'section.view', 0),
  (7, 'bug.assign', 2),
  (7, 'bug.change-status', 2),
  (7, 'bug.comment', 1),
  (7, 'bug.view', 0),
  (7, 'comment.edit', 2),
  (7, 'comment.remove', 3),
  (7, 'comment.upvote', 1),
  (8, 'bug.assign', 2),
  (8, 'bug.change-status', 2),
  (8, 'bug.comment', 1),
  (8, 'bug.view', 0),
  (8, 'comment.edit', 2),
  (8, 'comment.remove', 3),
  (8, 'comment.upvote', 1),
  (9, 'bug.assign', 2),
  (9, 'bug.change-status', 2),
  (9, 'bug.comment', 1),
  (9, 'bug.view', 0),
  (9, 'comment.edit', 2),
  (9, 'comment.remove', 3),
  (9, 'comment.upvote', 1),
  (10, 'bug.assign', 2),
  (10, 'bug.change-status', 2),
  (10, 'bug.comment', 1),
  (10, 'bug.view', 0),
  (10, 'comment.edit', 2),
  (10, 'comment.remove', 3),
  (10, 'comment.upvote', 1),
  (11, 'bug.assign', 2),
  (11, 'bug.change-status', 2),
  (11, 'bug.comment', 1),
  (11, 'bug.view', 0),
  (11, 'comment.edit', 2),
  (11, 'comment.remove', 3),
  (11, 'comment.upvote', 1),
  (12, 'bug.assign', 2),
  (12, 'bug.change-status', 2),
  (12, 'bug.comment', 1),
  (12, 'bug.view', 0),
  (12, 'comment.edit', 2),
  (12, 'comment.remove', 3),
  (12, 'comment.upvote', 1),
  (13, 'bug.assign', 2),
  (13, 'bug.change-status', 2),
  (13, 'bug.comment', 1),
  (13, 'bug.view', 0),
  (13, 'comment.edit', 2),
  (13, 'comment.remove', 3),
  (13, 'comment.upvote', 1),
  (14, 'bug.assign', 2),
  (14, 'bug.change-status', 2),
  (14, 'bug.comment', 1),
  (14, 'bug.view', 0),
  (14, 'comment.edit', 2),
  (14, 'comment.remove', 3),
  (14, 'comment.upvote', 1),
  (15, 'bug.assign', 2),
  (15, 'bug.change-status', 2),
  (15, 'bug.comment', 1),
  (15, 'bug.view', 0),
  (15, 'comment.edit', 2),
  (15, 'comment.remove', 3),
  (15, 'comment.upvote', 1),
  (16, 'bug.assign', 2),
  (16, 'bug.change-status', 2),
  (16, 'bug.comment', 1),
  (16, 'bug.view', 0),
  (16, 'comment.edit', 2),
  (16, 'comment.remove', 3),
  (16, 'comment.upvote', 1),
  (17, 'bug.assign', 2),
  (17, 'bug.change-status', 2),
  (17, 'bug.comment', 1),
  (17, 'bug.view', 0),
  (17, 'comment.edit', 2),
  (17, 'comment.remove', 3),
  (17, 'comment.upvote', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `Notification_ID` int(11) NOT NULL,
  `Triggered_By` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `Received_By` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `Target_One` int(11) NOT NULL,
  `Target_Two` int(11) DEFAULT NULL,
  `Creation_Date` datetime NOT NULL,
  `IsRead` tinyint(1) NOT NULL DEFAULT '0',
  `Type` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`Notification_ID`, `Triggered_By`, `Received_By`, `Target_One`, `Target_Two`, `Creation_Date`, `IsRead`, `Type`) VALUES
  (1, 'mrfishie', 'dr2n', 2, 8, '2015-08-30 05:15:22', 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `Object_ID` int(11) NOT NULL,
  `Object_Type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `objects`
--

INSERT INTO `objects` (`Object_ID`, `Object_Type`) VALUES
  (0, -1),
  (2, 0),
  (3, 0),
  (4, 0),
  (5, 0),
  (6, 0),
  (7, 1),
  (8, 1),
  (9, 1),
  (10, 1),
  (11, 1),
  (12, 1),
  (13, 1),
  (14, 1),
  (15, 1),
  (16, 1),
  (17, 1);

-- --------------------------------------------------------

--
-- Table structure for table `plusones`
--

CREATE TABLE IF NOT EXISTS `plusones` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`Section_ID`, `Name`, `Object_ID`, `Description`, `Slug`, `Color`) VALUES
  (1, 'Users', 2, 'The user management system in The Problem.', 'users', 7),
  (2, 'Sections', 3, 'Bug sections which are in The Problem.', 'sections', 8),
  (3, 'Home', 4, 'The home page for The Problem.', 'home', 14),
  (4, 'User Permissions', 5, 'User permission management system that works in The Problem.', 'user-permissions', 11),
  (5, 'Notifications', 6, 'Notification system in The Problem notifying users of bug assignment, commenting, +1 and section activities as they occur.', 'notifications', 1);

-- --------------------------------------------------------

--
-- Table structure for table `userpermissions`
--

CREATE TABLE IF NOT EXISTS `userpermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `userpermissions`
--

INSERT INTO `userpermissions` (`Object_ID`, `Permission_Name`, `Username`) VALUES
  (7, 'bug.change-status', 'mrfishie'),
  (7, 'bug.comment', 'mrfishie'),
  (7, 'bug.view', 'mrfishie'),
  (7, 'comment.assign', 'mrfishie'),
  (7, 'comment.edit', 'mrfishie'),
  (7, 'comment.upvote', 'mrfishie'),
  (8, 'bug.change-status', 'mrfishie'),
  (8, 'bug.comment', 'mrfishie'),
  (8, 'bug.view', 'mrfishie'),
  (8, 'comment.assign', 'mrfishie'),
  (8, 'comment.edit', 'mrfishie'),
  (8, 'comment.upvote', 'mrfishie'),
  (9, 'bug.change-status', 'mrfishie'),
  (9, 'bug.comment', 'mrfishie'),
  (9, 'bug.view', 'mrfishie'),
  (9, 'comment.assign', 'mrfishie'),
  (9, 'comment.edit', 'mrfishie'),
  (9, 'comment.upvote', 'mrfishie'),
  (10, 'bug.change-status', 'mrfishie'),
  (10, 'bug.comment', 'mrfishie'),
  (10, 'bug.view', 'mrfishie'),
  (10, 'comment.assign', 'mrfishie'),
  (10, 'comment.edit', 'mrfishie'),
  (10, 'comment.upvote', 'mrfishie'),
  (11, 'bug.change-status', 'mrfishie'),
  (11, 'bug.comment', 'mrfishie'),
  (11, 'bug.view', 'mrfishie'),
  (11, 'comment.assign', 'mrfishie'),
  (11, 'comment.edit', 'mrfishie'),
  (11, 'comment.upvote', 'mrfishie'),
  (12, 'bug.change-status', 'mrfishie'),
  (12, 'bug.comment', 'mrfishie'),
  (12, 'bug.view', 'mrfishie'),
  (12, 'comment.assign', 'mrfishie'),
  (12, 'comment.edit', 'mrfishie'),
  (12, 'comment.upvote', 'mrfishie'),
  (13, 'bug.change-status', 'mrfishie'),
  (13, 'bug.comment', 'mrfishie'),
  (13, 'bug.view', 'mrfishie'),
  (13, 'comment.assign', 'mrfishie'),
  (13, 'comment.edit', 'mrfishie'),
  (13, 'comment.upvote', 'mrfishie'),
  (14, 'bug.change-status', 'mrfishie'),
  (14, 'bug.comment', 'mrfishie'),
  (14, 'bug.view', 'mrfishie'),
  (14, 'comment.assign', 'mrfishie'),
  (14, 'comment.edit', 'mrfishie'),
  (14, 'comment.upvote', 'mrfishie'),
  (15, 'bug.change-status', 'mrfishie'),
  (15, 'bug.comment', 'mrfishie'),
  (15, 'bug.view', 'mrfishie'),
  (15, 'comment.assign', 'mrfishie'),
  (15, 'comment.edit', 'mrfishie'),
  (15, 'comment.upvote', 'mrfishie'),
  (16, 'bug.change-status', 'mrfishie'),
  (16, 'bug.comment', 'mrfishie'),
  (16, 'bug.view', 'mrfishie'),
  (16, 'comment.assign', 'mrfishie'),
  (16, 'comment.edit', 'mrfishie'),
  (16, 'comment.upvote', 'mrfishie'),
  (17, 'bug.change-status', 'mrfishie'),
  (17, 'bug.comment', 'mrfishie'),
  (17, 'bug.view', 'mrfishie'),
  (17, 'comment.assign', 'mrfishie'),
  (17, 'comment.edit', 'mrfishie'),
  (17, 'comment.upvote', 'mrfishie');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `Username` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
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
  ('Andrew', 'fireme@mailinator.net', 'Andrew', '$2y$10$lXc/DY.A4UYejVGAE.Wp1.WLPu9F6tB4ug1j2p/ihIxED.J64DniK', 1, '', NULL),
  ('bullseye', 'bullseye@mailinator.net', 'Mr T', '$2y$10$tFMLnwu2k4bMvTUQv9JU3ut/y7vike5OYM7h/CL0xBVBne9UdZy/u', 1, '', NULL),
  ('dr2n', 'darren.yx.fu@gmail.com', 'Darren Fu', '$2y$10$7Du9oKK0PH06Snxl4FgacekKhWzwyMXFUcQYrkuIJB.jdcez05LZe', 4, '', NULL),
  ('exterminate', 'david@mailinator.net', 'David', '$2y$10$Y0RbUXWev/t53lzZfv3PW.cYbVUDUGvNU4JihTpj4Ews9xqPtS4ne', 1, '', NULL),
  ('flame', 'flame@mailinator.net', 'Andria', '$2y$10$HTVsREh6wUUCXvkS/j2jn.8pGYK8xasclpQ3Y.AgL200gJaX/n6fe', 1, '', NULL),
  ('gottatrythis', 'gtythis@mailinator.net', 'Donald', '$2y$10$WsXkXva/GjCZH4RVh8PimOhbck3iE7zocD4dggoFNnG/N/96JLJ7O', 1, '', NULL),
  ('hackers', 'davee3@mailinator.net', 'Dave', '$2y$10$WDLr8vRly3T8xAK.FIgrrOwEicx/T.atsAMLJFr3zsDK18e4iYf0q', 1, '', NULL),
  ('jackass', 'jackass@mailinator.net', 'Jake Peearaa', '$2y$10$VpoA34YIoKM9e4hdgyhS6O471OnGnr.ZsU.hf3Igx5L7DxoO3AZba', 1, '', NULL),
  ('Jas', 'jjj@mailinator.net', 'Jas', '$2y$10$2MGjSeNqQPpzbRwv7NlT2ONRi6dy5H.LEiN3s5sHmWPF5g8hNyefm', 1, '', NULL),
  ('KaiXinGuo', 'kai@mailinator.net', 'Kai', '$2y$10$/f65xs.Da1AXJAqCmxpT1.onHjAb9XNfWaMZ9B0p8BXLMHUxUKjCm', 1, '', NULL),
  ('KatieLilly', 'jrn@mailinator.net', 'Katie Lilly', '$2y$10$jc5WCLrj3rD8NNlcef7weepoJ.aZ1vlqjCs9RdQM16XfCPqbDbE/m', 1, '', NULL),
  ('Liam', 'LIAM@mailinator.net', 'Liam Prok', '$2y$10$Kkmh9Y3aAjmqVraLy0jGd.8y0joHcnL4gWDCidDLhEjBp568z3TK6', 1, '', NULL),
  ('meltingPoint', 'mp@mailinator.net', 'Jess', '$2y$10$8mQX8.RooNnye.zStiHmc.45lVDR2uU11a3zMVmaQ9H3F.lptjeP.', 1, '', NULL),
  ('MichaelK', 'mike@mailinator.net', 'Michael', '$2y$10$WvnzAt1GpmdtzTpWBlIAt.7hjD6hV3GPdXhj2OucoUTKCHqIwO9Om', 1, '', NULL),
  ('mrfishie', 'mrfishie101@hotmail.com', 'Tom', '$2y$10$U5ETgL5NPBp9tBnXvch2DOJa3sdv4cwzsn6R4KtWvFjP0Mn/0pAdW', 4, '', NULL),
  ('powerRangers46', 'pewpew@mailinator.net', 'Zac Langlands', '$2y$10$gaWKZaH7MynEmM3jh2kedufZteWHLylkSIq3OSemQc.MndX1JtZ46', 1, '', NULL),
  ('SmithJohn', 'jsjs@mailinator.net', 'John Smith', '$2y$10$2li2WzaQKhw/3zV23ViRKeXxRaHzHczYyw0V.w3nQtMBosSICxp4.', 1, 'Part human, part Timelord.', NULL),
  ('that''''sMe', 'hu@mailinator.net', 'DoctorHu', '$2y$10$l0.XSdUuBCmRBKQg3Sk1WOVtBi5Th2dJkgKsQwmP3fJZCB1pNkcC.', 1, '', NULL),
  ('unhelpful', 'unhelpful@mailinator.net', 'Ben Loungin', '$2y$10$YSUbxjAhtgjbGsD3sudrweyR/HYkrs6v6K9LorRlE0BZrsAf/ZWv2', 1, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `watchers`
--

CREATE TABLE IF NOT EXISTS `watchers` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `watchers`
--

INSERT INTO `watchers` (`Object_ID`, `Username`) VALUES
  (2, 'dr2n'),
  (5, 'dr2n'),
  (6, 'dr2n'),
  (3, 'mrfishie'),
  (4, 'mrfishie'),
  (7, 'mrfishie'),
  (8, 'mrfishie'),
  (9, 'mrfishie'),
  (10, 'mrfishie'),
  (11, 'mrfishie'),
  (12, 'mrfishie'),
  (13, 'mrfishie'),
  (14, 'mrfishie'),
  (15, 'mrfishie'),
  (16, 'mrfishie'),
  (17, 'mrfishie');

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
ADD PRIMARY KEY (`Object_ID`,`Username`),
ADD KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bugs`
--
ALTER TABLE `bugs`
MODIFY `Bug_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
MODIFY `Comment_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cookies`
--
ALTER TABLE `cookies`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `objects`
--
ALTER TABLE `objects`
MODIFY `Object_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
MODIFY `Section_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;