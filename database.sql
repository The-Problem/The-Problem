-- phpMyAdmin SQL Dump
-- version 4.4.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 08, 2015 at 07:57 AM
-- Server version: 5.5.34
-- PHP Version: 5.6.12

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

CREATE TABLE IF NOT EXISTS `bugs` (
  `Bug_ID` int(11) NOT NULL,
  `Section_ID` int(11) NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Name` text COLLATE latin1_general_cs NOT NULL,
  `Status` int(11) NOT NULL,
  `Description` text COLLATE latin1_general_cs,
  `Creation_Date` datetime NOT NULL,
  `Author` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Edit_Date` varchar(20) COLLATE latin1_general_cs DEFAULT NULL,
  `RID` int(11) NOT NULL,
  `Assigned` varchar(20) COLLATE latin1_general_cs DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `bugs`:
--   `Assigned`
--       `users` -> `Username`
--   `Author`
--       `users` -> `Username`
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Section_ID`
--       `sections` -> `Section_ID`
--   `Assigned`
--       `users` -> `Username`
--   `Section_ID`
--       `sections` -> `Section_ID`
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Author`
--       `users` -> `Username`
--

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
  `Comment_Text` text COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `comments`:
--   `Bug_ID`
--       `bugs` -> `Bug_ID`
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Username`
--       `users` -> `Username`
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Bug_ID`
--       `bugs` -> `Bug_ID`
--   `Username`
--       `users` -> `Username`
--

-- --------------------------------------------------------

--
-- Table structure for table `developers`
--

CREATE TABLE IF NOT EXISTS `developers` (
  `Section_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `developers`:
--   `Section_ID`
--       `sections` -> `Section_ID`
--   `Username`
--       `users` -> `Username`
--   `Username`
--       `users` -> `Username`
--   `Section_ID`
--       `sections` -> `Section_ID`
--

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
-- RELATIONS FOR TABLE `grouppermissions`:
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Object_ID`
--       `objects` -> `Object_ID`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `notifications`:
--   `Received_By`
--       `users` -> `Username`
--   `Target_One`
--       `objects` -> `Object_ID`
--   `Target_Two`
--       `objects` -> `Object_ID`
--   `Triggered_By`
--       `users` -> `Username`
--   `Target_Two`
--       `objects` -> `Object_ID`
--   `Triggered_By`
--       `users` -> `Username`
--   `Received_By`
--       `users` -> `Username`
--   `Target_One`
--       `objects` -> `Object_ID`
--

-- --------------------------------------------------------

--
-- Table structure for table `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `Object_ID` int(11) NOT NULL,
  `Object_Type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `objects`:
--

-- --------------------------------------------------------

--
-- Table structure for table `plusones`
--

CREATE TABLE IF NOT EXISTS `plusones` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `plusones`:
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Username`
--       `users` -> `Username`
--   `Username`
--       `users` -> `Username`
--   `Object_ID`
--       `objects` -> `Object_ID`
--

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `Section_ID` int(11) NOT NULL,
  `Name` text COLLATE latin1_general_cs NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Description` text COLLATE latin1_general_cs NOT NULL,
  `Slug` text COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `sections`:
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Object_ID`
--       `objects` -> `Object_ID`
--

-- --------------------------------------------------------

--
-- Table structure for table `userpermissions`
--

CREATE TABLE IF NOT EXISTS `userpermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- RELATIONS FOR TABLE `userpermissions`:
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Username`
--       `users` -> `Username`
--   `Username`
--       `users` -> `Username`
--   `Object_ID`
--       `objects` -> `Object_ID`
--

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
-- RELATIONS FOR TABLE `users`:
--

-- --------------------------------------------------------

--
-- Table structure for table `watchers`
--

CREATE TABLE IF NOT EXISTS `watchers` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

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

--
-- RELATIONS FOR TABLE `watchers`:
--   `Object_ID`
--       `objects` -> `Object_ID`
--   `Username`
--       `users` -> `Username`
--   `Username`
--       `users` -> `Username`
--   `Object_ID`
--       `objects` -> `Object_ID`
--

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
-- Indexes for table `cookies`
--
ALTER TABLE `cookies`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bugs`
--
ALTER TABLE `bugs`
  MODIFY `Bug_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Comment_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `objects`
--
ALTER TABLE `objects`
  MODIFY `Object_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `Section_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cookies`
--
ALTER TABLE `cookies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bugs`
--
ALTER TABLE `bugs`
  ADD CONSTRAINT `bugs_ibfk_4` FOREIGN KEY (`Assigned`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `bugs_ibfk_1` FOREIGN KEY (`Section_ID`) REFERENCES `sections` (`Section_ID`),
  ADD CONSTRAINT `bugs_ibfk_2` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `bugs_ibfk_3` FOREIGN KEY (`Author`) REFERENCES `users` (`Username`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`Bug_ID`) REFERENCES `bugs` (`Bug_ID`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);

--
-- Constraints for table `developers`
--
ALTER TABLE `developers`
  ADD CONSTRAINT `developers_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `developers_ibfk_1` FOREIGN KEY (`Section_ID`) REFERENCES `sections` (`Section_ID`);

--
-- Constraints for table `grouppermissions`
--
ALTER TABLE `grouppermissions`
  ADD CONSTRAINT `grouppermissions_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`Target_Two`) REFERENCES `objects` (`Object_ID`),
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`Triggered_By`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`Received_By`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`Target_One`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `plusones`
--
ALTER TABLE `plusones`
  ADD CONSTRAINT `plusones_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `plusones_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `userpermissions`
--
ALTER TABLE `userpermissions`
  ADD CONSTRAINT `userpermissions_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `userpermissions_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

--
-- Constraints for table `watchers`
--
ALTER TABLE `watchers`
  ADD CONSTRAINT `watchers_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`),
  ADD CONSTRAINT `watchers_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
