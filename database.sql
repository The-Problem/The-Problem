-- phpMyAdmin SQL Dump
-- version 4.4.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2015 at 02:05 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `the-problem`
--

-- --------------------------------------------------------

--
-- Table structure for table `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `Object_ID` int(11) NOT NULL,
  `Object_Type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

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
  (36, 2),
  (41, 2),
  (42, 2),
  (43, 2),
  (44, 2),
  (45, 2),
  (46, 2),
  (47, 2),
  (48, 2),
  (49, 2),
  (50, 2),
  (51, 2),
  (52, 2),
  (53, 2),
  (54, 2),
  (55, 2),
  (56, 2),
  (57, 2),
  (58, 2),
  (59, 2),
  (60, 2),
  (61, 2),
  (62, 2),
  (63, 2),
  (64, 2),
  (65, 2),
  (66, 2),
  (67, 2),
  (68, 2),
  (69, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `objects`
--
ALTER TABLE `objects`
ADD PRIMARY KEY (`Object_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `objects`
--
ALTER TABLE `objects`
MODIFY `Object_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=70;