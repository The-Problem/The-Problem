








SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";











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







CREATE TABLE IF NOT EXISTS `comments` (
  `Comment_ID` int(11) NOT NULL,
  `Bug_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Creation_Date` datetime NOT NULL,
  `Edit_Date` datetime DEFAULT NULL,
  `Comment_Text` text COLLATE latin1_general_cs NOT NULL,
  `Raw_Text` longtext COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;







CREATE TABLE IF NOT EXISTS `configuration` (
  `Type` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Value` text COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;





INSERT INTO `configuration` (`Type`, `Name`, `Value`) VALUES
  ('overview-name', 'sitename', 'The Problem'),
  ('overview-visibility', 'registration', 'open'),
  ('overview-visibility', 'visibility', 'public');







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







CREATE TABLE IF NOT EXISTS `developers` (
  `Section_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;





INSERT INTO `developers` (`Section_ID`, `Username`) VALUES
  (1, 'Andrew'),
  (1, 'dr2n'),
  (2, 'dr2n'),
  (2, 'exterminate'),
  (3, 'exterminate'),
  (3, 'mrfishie'),
  (4, 'mrfishie'),
  (2, 'unhelpful');







CREATE TABLE IF NOT EXISTS `grouppermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Rank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;





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







CREATE TABLE IF NOT EXISTS `notifications` (
  `Notification_ID` int(11) NOT NULL,
  `Triggered_By` varchar(20) COLLATE latin1_general_cs DEFAULT NULL,
  `Received_By` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Target_One` int(11) NOT NULL,
  `Target_Two` int(11) DEFAULT NULL,
  `Creation_Date` datetime NOT NULL,
  `IsRead` tinyint(1) NOT NULL DEFAULT '0',
  `Type` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;







CREATE TABLE IF NOT EXISTS `objects` (
  `Object_ID` int(11) NOT NULL,
  `Object_Type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;





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
  (31, 1);







CREATE TABLE IF NOT EXISTS `plusones` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;







CREATE TABLE IF NOT EXISTS `sections` (
  `Section_ID` int(11) NOT NULL,
  `Name` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Object_ID` int(11) NOT NULL,
  `Description` text COLLATE latin1_general_cs NOT NULL,
  `Slug` text COLLATE latin1_general_cs NOT NULL,
  `Color` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;





INSERT INTO `sections` (`Section_ID`, `Name`, `Object_ID`, `Description`, `Slug`, `Color`) VALUES
  (1, 'Users', 1, 'The user management system in The Problem.', 'users', 6),
  (2, 'Sections', 2, 'Bug sections which are in The Problem.', 'sections', 0),
  (3, 'Home', 3, 'The home page for The Problem.', 'homepage', 11),
  (4, 'User Permissions', 4, 'User permission management system that works in The Problem.', 'user-permissions', 9),
  (5, 'Notifications', 5, 'Notification system in The Problem notifying users of bug assignment, commenting, +1 and section activities as they occur.', 'notifications', 14);







CREATE TABLE IF NOT EXISTS `userpermissions` (
  `Object_ID` int(11) NOT NULL,
  `Permission_Name` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;







CREATE TABLE IF NOT EXISTS `users` (
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL,
  `Email` text COLLATE latin1_general_cs NOT NULL,
  `Name` text COLLATE latin1_general_cs NOT NULL,
  `Password` text COLLATE latin1_general_cs NOT NULL,
  `Rank` int(11) NOT NULL,
  `Bio` text COLLATE latin1_general_cs NOT NULL,
  `Last_Logon_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;





INSERT INTO `users` (`Username`, `Email`, `Name`, `Password`, `Rank`, `Bio`, `Last_Logon_Time`) VALUES

('Andrew', 'fireme@mailinator.net', 'Andrew', 'pleaseBoss1', 0, '', NULL),
('bullseye', 'bullseye@mailinator.net', 'Mr T', 'againNagain0', 0, '', NULL),
('dr2n', 'darren.yx.fu@gmail.com', 'Darren Fu', 'superPiggy46', 4, '\r\n\r\nOften with a love of the French language comes a love of French culture and an interest in the way the French-speaking world does things differently to us.\r\n\r\nOur seasonal events are designed to bring together like-minded people in a social situation while enjoying the best food, wine, film, fashion (and much more) the French have to offer.\r\n\r\nFor all levels and interests - there’s be something for everybody...even a trip to Nouméa in April 2015, too. There will also be a three-day trip to a secret destination for a French Festival in October 2015. Let us know if you''d like to be kept in the loop.\r\n', '2015-08-26 16:50:02'),
('exterminate', 'david@mailinator.net', 'David', 'sleepyDog2', 0, '', NULL),
('flame', 'flame@mailinator.net', 'Andria', 'noOneCanSee6', 0, '', NULL),
('gottatrythis', 'gtythis@mailinator.net', 'Donald', 'takeme5555', 0, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', '2015-08-28 17:32:21'),
('hackers', 'davee3@mailinator.net', 'Dave', 'breakingIn12', 0, '', '2015-08-26 21:23:20'),
('Jas', 'jjj@mailinator.net', 'Jas', 'rightio3', 0, '', NULL),
('jackass', 'jackass@mailinator.net', 'Jake Peearaa', 'moon3Shadow', 0, '', NULL),
('KaiXinGuo', 'kai@mailinator.net', 'Kai', 'wellThi5IsAProblem', 0, '', NULL),
('KatieLilly', 'jrn@mailinator.net', 'Katie Lilly', 'flashCookies1994372', 0, '', NULL),
('Liam', 'LIAM@mailinator.net', 'Liam Prok', 'helloW0rld', 1, '', '2015-08-26 21:09:09'),
('MichaelK', 'mike@mailinator.net', 'Michael', 'don''tBePicky1', 0, '', NULL),
('meltingPoint', 'mp@mailinator.net', 'Jess', '4myDreamz', 0, '', NULL),
('mrfishie', 'mrfishie101@hotmail.com', 'Tom', 'correct horse battery staple', 4, 'Hi! I make websites and lights do cool things.', NULL),
('powerRangers46', 'pewpew@mailinator.net', 'Zac Langlands', 'Lo000L', 0, '', NULL),
('SmithJohn', 'jsjs@mailinator.net', 'John Smith', 'joinin768', 0, 'There''s probably not a single person in this development team that''s as skilled as I am in making tea.', NULL),
('that''sMe', 'hu@mailinator.net', 'DoctorHu', 'firefox555', 0, '', NULL),
('unhelpful', 'unhelpful@mailinator.net', 'Ben Loungin', 'yjhghtd44790vjhg', 0, '', NULL);
-- --------------------------------------------------------






CREATE TABLE IF NOT EXISTS `watchers` (
  `Object_ID` int(11) NOT NULL,
  `Username` varchar(20) COLLATE latin1_general_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs;

--
-- Dumping data for table `watchers`
--

INSERT INTO `watchers` (`Object_ID`, `Username`) VALUES
(1, 'dr2n'),
(5, 'dr2n');

--
-- Indexes for dumped tables
--







ALTER TABLE `bugs`
ADD PRIMARY KEY (`Bug_ID`),
ADD KEY `Section_ID` (`Section_ID`),
ADD KEY `Object_ID` (`Object_ID`),
ADD KEY `Author` (`Author`),
ADD KEY `Assigned` (`Assigned`);




ALTER TABLE `comments`
ADD PRIMARY KEY (`Comment_ID`),
ADD KEY `Bug_ID` (`Bug_ID`),
ADD KEY `Username` (`Username`),
ADD KEY `Object_ID` (`Object_ID`);




ALTER TABLE `configuration`
ADD PRIMARY KEY (`Type`,`Name`);




ALTER TABLE `cookies`
ADD PRIMARY KEY (`id`);




ALTER TABLE `developers`
ADD PRIMARY KEY (`Section_ID`,`Username`),
ADD KEY `Username` (`Username`);




ALTER TABLE `grouppermissions`
ADD PRIMARY KEY (`Object_ID`,`Permission_Name`) USING BTREE;




ALTER TABLE `notifications`
ADD PRIMARY KEY (`Notification_ID`),
ADD KEY `Triggered_By` (`Triggered_By`),
ADD KEY `Received_By` (`Received_By`),
ADD KEY `Target_One` (`Target_One`),
ADD KEY `Target_Two` (`Target_Two`);




ALTER TABLE `objects`
ADD PRIMARY KEY (`Object_ID`);




ALTER TABLE `plusones`
ADD PRIMARY KEY (`Object_ID`,`Username`),
ADD KEY `Username` (`Username`);




ALTER TABLE `sections`
ADD PRIMARY KEY (`Section_ID`),
ADD KEY `Object_ID` (`Object_ID`);




ALTER TABLE `userpermissions`
ADD PRIMARY KEY (`Object_ID`,`Permission_Name`,`Username`),
ADD KEY `Username` (`Username`);




ALTER TABLE `users`
ADD PRIMARY KEY (`Username`);




ALTER TABLE `watchers`
ADD PRIMARY KEY (`Object_ID`),
ADD KEY `Username` (`Username`);








ALTER TABLE `bugs`
MODIFY `Bug_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=57;



ALTER TABLE `comments`
MODIFY `Comment_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=41;



ALTER TABLE `cookies`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



ALTER TABLE `notifications`
MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;



ALTER TABLE `objects`
MODIFY `Object_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=114;



ALTER TABLE `sections`
MODIFY `Section_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;







ALTER TABLE `bugs`
ADD CONSTRAINT `bugs_ibfk_1` FOREIGN KEY (`Section_ID`) REFERENCES `sections` (`Section_ID`),
ADD CONSTRAINT `bugs_ibfk_2` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
ADD CONSTRAINT `bugs_ibfk_3` FOREIGN KEY (`Author`) REFERENCES `users` (`Username`),
ADD CONSTRAINT `bugs_ibfk_4` FOREIGN KEY (`Assigned`) REFERENCES `users` (`Username`);




ALTER TABLE `comments`
ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`Bug_ID`) REFERENCES `bugs` (`Bug_ID`),
ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`),
ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);




ALTER TABLE `developers`
ADD CONSTRAINT `developers_ibfk_1` FOREIGN KEY (`Section_ID`) REFERENCES `sections` (`Section_ID`),
ADD CONSTRAINT `developers_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);




ALTER TABLE `grouppermissions`
ADD CONSTRAINT `grouppermissions_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);




ALTER TABLE `notifications`
ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`Triggered_By`) REFERENCES `users` (`Username`),
ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`Received_By`) REFERENCES `users` (`Username`),
ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`Target_One`) REFERENCES `objects` (`Object_ID`),
ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`Target_Two`) REFERENCES `objects` (`Object_ID`);




ALTER TABLE `plusones`
ADD CONSTRAINT `plusones_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
ADD CONSTRAINT `plusones_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);




ALTER TABLE `sections`
ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`);




ALTER TABLE `userpermissions`
ADD CONSTRAINT `userpermissions_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
ADD CONSTRAINT `userpermissions_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);




ALTER TABLE `watchers`
ADD CONSTRAINT `watchers_ibfk_1` FOREIGN KEY (`Object_ID`) REFERENCES `objects` (`Object_ID`),
ADD CONSTRAINT `watchers_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`);
