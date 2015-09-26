<?php
	//Library for accessing notifications
	class Notifications {
		const TYPE_ASSIGNMENT = 0;
		const TYPE_SECTION = 1;
		const TYPE_BUG = 2;
		const TYPE_COMMENT = 3;
		const TYPE_PLUSONE = 4;
		const TYPE_MENTION = 5;

		private static $bugStatus = array('DELETED', 'OPEN', 'CLOSED', 'DUPLICATE', 'WIP');

		//returns array listing notifications for user logged in as objects
		//order and time range may be selected through $settings array
		public static function get($settings){

			//get notification data as associative array from database depending on arguments
			$currentUser = $_SESSION['username'];

			//create queries depending on settings
			if ($settings['time']){
				if ($settings['before']){
					//return the first $limit number of notifications before $time
					$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By= ? AND Creation_Date <= ? ORDER BY Creation_Date DESC LIMIT ?";
					$queryResult = Connection::query($notificationQuery, "sss", array($currentUser, $settings['time']));
				}else{
					//return all notifications after $time
					$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By = ? AND Creation_Date > ? ORDER BY Creation_Date DESC";
					$queryResult = Connection::query($notificationQuery, "ss", array($currentUser, date("Y:m:d H:i:s", strtotime($settings['time']))));
				}

			}else{
				//return the first $limit number of notifications as of now
				if (!isset($settings['limit'])){
					$settings['limit'] = 10;
				}
				$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By = ? AND Creation_Date <= NOW() ORDER BY Creation_Date DESC LIMIT ?";
				$queryResult = Connection::query($notificationQuery, "ss", array($currentUser, $settings['limit']));
			
			}

			//loop through notification data and process for relevvant information depending on type and push to array before returning
			$notificationElements = array();

			for ($i = 0; $i < count($queryResult); $i++){
				$notificationDetails = self::processNotification($queryResult[$i]);
				array_push($notificationElements, $notificationDetails);
			}

			return $notificationElements;

		}

		//processes through a single notification object from the database
		//returns object with notification time, message, read, location and section properties
		private static function processNotification($notification, $profile = false){
		
			Library::get('objects');

			$message = "";
			$stats = "";

			$trigger = $notification['Triggered_By'];
			$type = $notification['Type'];
			$targetOne = $notification['Target_One'];
			Library::get('string');
			$fuzzyTime = String::timeago($notification['Creation_Date']);

			//find more information and process depending on notification type
			if ($type == self::TYPE_ASSIGNMENT){
				//when notification is where the user has been assigned a bug
				$bugInfoQuery = "SELECT bugs.Bug_ID, bugs.Name as 'Bug_Name', sections.Name as 'Section_Name', sections.Slug as 'Section_Slug' FROM bugs LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID WHERE bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];
				
				$bugID = $bugInfo['Bug_ID'];
				$bugName = $bugInfo['Bug_Name'];
				$sectionName = $bugInfo['Section_Name'];
				$sectionSlug = $bugInfo['Section_Slug'];

				$message = $trigger . " has assigned you to bug #" . $bugID . ", '" . $bugName . "'";
				if ($profile){
					$message = "Assigned " . $trigger . " to bug #" . $bugID . ", '" . $bugName;
				}

			}else if ($type == self::TYPE_SECTION){
				//when notification is where a bug has been created in watched section
				$bugInfoQuery = "SELECT bugs.Bug_ID, bugs.Name as 'Bug_Name', sections.Name as 'Section_Name', sections.Slug as 'Section_Slug' FROM bugs LEFT JOIN sections ON bugs.Section_ID =sections.Section_ID WHERE bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];
				$bugName = $bugInfo['Bug_Name'];
				$sectionName = $bugInfo['Section_Name'];
				$sectionSlug = $bugInfo['Section_Slug'];

				$message = $trigger . " has just submitted '" . $bugName . "' to '" . $sectionName . "' ";
				if ($profile){
					$message = "Submitted '" . $bugName . "' to " . $sectionName;
				}

			}else if($type == self::TYPE_BUG){
				//when notification is where bug status has been changed
				$bugInfoQuery = "SELECT Status, bugs.Name as Bug_Name, bugs.RID as 'RID', sections.Name as Section_Name, sections.Slug as 'Section_Slug' FROM bugs LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID WHERE bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];

				$bugName = $bugInfo['Bug_Name'];
				$bugStatusNumber = $bugInfo['Status'];
				$bugStatusString = self::$bugStatus[$bugStatusNumber];
				$sectionName = $bugInfo['Section_Name'];
				$sectionSlug = $bugInfo['Section_Slug'];

				$link = Path::getclientfolder("bugs", $sectionSlug, $bugInfo['RID']);

				$message = $trigger . " has changed the status of '" . $bugName . "' to " . $bugStatusString . "";
				if ($profile){
					$message = "Changed to status of '" . $bugName . "' to " . $bugStatusSting;
				}
			}else if($type == self::TYPE_COMMENT){
				//when notification is where a comment has been made in a watched bug
				$commentQuery = 
				"SELECT comments.Bug_ID as 'Bug_Number', sections.Name as 'Section_Name', sections.Slug as 'Section_Slug', comments.Comment_Text, bugs.RID as 'RID', bugs.Name as 'Bug_Name'
				FROM comments
				LEFT JOIN bugs ON comments.Bug_ID = bugs.Bug_ID
				LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
				WHERE comments.Object_ID = ?";

				$commentInfo = Connection::query($commentQuery, "s", array($targetOne))[0];

				$bugNumber = $commentInfo['Bug_Number'];
				$comment = strip_tags($commentInfo['Comment_Text']);
				$sectionName = $commentInfo['Section_Name'];
				$sectionSlug = $commentInfo['Section_Slug'];
				$RID = $commentInfo['RID'];
				$link = Path::getclientfolder("bugs", $sectionSlug, $RID);

				$comment = self::shorten($comment, 35);

				$message = $trigger . " commented on bug #" . $bugNumber . ": <br>'" . $comment . "'";
				if ($profile){
					$message = "Commented on bug #" . $bugNumber . ", " . $commentInfo['Bug_Name'];
				}
				

			}else if ($type == self::TYPE_PLUSONE){
				//when notification is a +1
				$typeQuery = 
							"SELECT Object_Type
							FROM objects
							WHERE Object_ID = ?";
				$targetType = Connection::query($typeQuery, "s", array($targetOne))[0]["Object_Type"];

				if ($targetType == Objects::TYPE_BUG){
					//when a bug is +1'd
					$bugQuery = 
								"SELECT bugs.RID as 'RID', bugs.Name as 'Bug_Name', bugs.Bug_ID as 'Bug_ID', sections.Name as 'Section_Name', sections.Slug as 'Section_Slug'
								FROM bugs
									LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
								WHERE bugs.Object_ID = ?";

					$bugInfo = Connection::query($bugQuery, "s", array($targetOne))[0];
					$bugName = $bugInfo['Bug_Name'];
					$sectionName = $bugInfo['Section_Name'];
					$sectionSlug = $bugInfo['sectionSlug'];
					$RID = $bugInfo['RID'];

					$link = Path::getclientfolder("bugs", $sectionSlug, $RID);

					$message = $trigger . " +1'd your bug " . $bugName;
					if ($profile){
						$message = "+1'd bug #" . $bugInfo['Bug_ID'] . ", " . $bugName;
					}

				}else if ($targetType == Objects::TYPE_COMMENT){
					//when a comment in a bug is +1'd
					$commentQuery = 
									"SELECT Comment_Text, bugs.Name as 'Bug_Name', bugs.Bug_ID as 'Bug_ID', bugs.RID as 'RID', sections.Name as 'Section_Name', sections.slug as 'Section_Slug'
									FROM comments
										LEFT JOIN bugs ON comments.Bug_ID = bugs.Bug_ID
										LEFT JOIN sections ON sections.Section_ID = bugs.Section_ID
									WHERE comments.Object_ID = ?";

					$commentInfo = Connection::query($commentQuery, "s", array($targetOne))[0];

					$commentText = strip_tags($commentInfo['Comment_Text']);
					$bugName = $commentInfo['Bug_Name'];
					$sectionName = $commentInfo['Section_Name'];
					$sectionSlug = $commentInfo['Section_Slug'];
					$bugID = $commentInfo['Bug_ID'];

					$link = Path::getclientfolder('bugs', $sectionSlug, $commentInfo['RID']);

					$commentText = self::shorten($commentText, 35);
					$bugName = self::shorten($bugName, 15);
					$sectionName = self::shorten($sectionName, 15);

					$message = $trigger . " +1'd your comment '" . $commentText . "' from '" . $bugName . "'";
					if ($profile){
						$message = "+1'd a comment from bug #" . $commentInfo['Bug_ID'] . ", " . $commentInfo['Bug_Name'];
					}

				}


			}else if($type == self::TYPE_MENTION){
				//when notification is where user has been mentioned in a comment
				$commentQuery =
								"SELECT Comment_Text, sections.Name as 'Section_Name', sections.Slug as 'Section_Slug'
								FROM comments
									LEFT JOIN bugs ON comments.Bug_ID = bugs.Bug_ID
									LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
								WHERE comments.Object_ID = ?";

				$commentResult = Connection::query($commentQuery, "s", array($targetOne))[0];
				$sectionName = $commentResult['Section_Name'];
				$sectionSlug = $commentResult['Section_Slug'];
				$message = "You were mentioned in a comment from '" . $sectionName . "'";

			}

			$stats = $fuzzyTime . " - " . $sectionName;

			$output = array(
				"fuzzyTime" => $fuzzyTime,
				"sectionName" => $sectionName, 
				"time" => date("c", strtotime($notification['Creation_Date'])),
				"message" => $message,
				"stats" => $stats,
				"read" => $notification['IsRead'],
				"sectionSlug" => $sectionSlug,
				"link" => $link

				);


			return $output;

		}

		//returns notifications to be placed in recent activity in user pages
		public static function getWhereTriggerIs($username, $limit = 10){
			$query =
					"SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type
					FROM notifications
					WHERE Triggered_By = ?
					ORDER BY Creation_Date DESC
					LIMIT ?";

			$queryResult = Connection::query($query, "ss", array($username, $limit));

			$output = array();
			for ($i = 0; $i < count($queryResult); $i++){
				array_push($output, self::processNotification($queryResult[$i], true));
			}

			return $output;

		}

		//shortens string to required length with ellipses on the end
		private static function shorten($message, $length){
			$output = $message;

			if (strlen($output) > $length){
				$output = substr($output, 0, $length) . '...';
			}

			return $output;
		}


	}
