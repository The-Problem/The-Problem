<?php
	class Notifications {
		const TYPE_ASSIGNMENT = 0;
		const TYPE_SECTION = 1;
		const TYPE_BUG = 2;
		const TYPE_COMMENT = 3;
		const TYPE_PLUSONE = 4;
		const TYPE_MENTION = 5;

		private static $bugStatus = array('DELETED', 'OPEN', 'CLOSED', 'DUPLICATE', 'WIP');

		public static function get($settings){

			//get notification data as associative array from database depending on arguments
			$currentUser = $_SESSION['username'];

			if ($settings['time']){
				if ($settings['before']){
					//return the first $limit number of notifications before $time
					$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By= ? AND Creation_Date <= ? ORDER BY Creation_Date DESC LIMIT ?";
					$queryResult = Connection::query($notificationQuery, "sss", array($currentUser, $settings['time']));
				}else{
					//return all notifications after $time
					$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By = ? AND Creation_Date >= ? ORDER BY Creation_Date DESC";
					$queryResult = Connection::query($notificationQuery, "ss", array($currentUser, date("Y:m:d H:i:s", $settings['time'])));
				}

			}else{
				//return the first $limit number of notifications as of now
				if (!isset($settings['limit'])){
					$settings['limit'] = 10;
				}
				$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By = ? ORDER BY Creation_Date DESC LIMIT ?";
				$queryResult = Connection::query($notificationQuery, "ss", array($currentUser, $settings['limit']));
			
			}

			//loop through notification data from create html

			$notificationElements = array();

			for ($i = 0; $i < count($queryResult); $i++){
				$notificationDetails = self::processNotification($queryResult[$i]);
				array_push($notificationElements, $notificationDetails);
			}

			return $notificationElements;

		}

		private static function processNotification($notification){
		
			Library::get('objects');

			$message = "";
			$stats = "";

			$trigger = $notification['Triggered_By'];
			$type = $notification['Type'];
			$targetOne = $notification['Target_One'];
			Library::get('string');
			$fuzzyTime = String::timeago($notification['Creation_Date']);

			if ($type == self::TYPE_ASSIGNMENT){
				$bugInfoQuery = "SELECT bugs.Bug_ID, bugs.Name as 'Bug_Name', sections.Name as 'Section_Name' FROM bugs LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID WHERE bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];
				
				$bugID = $bugInfo['Bug_ID'];
				$bugName = $bugInfo['Bug_Name'];
				$sectionName = $bugInfo['Section_Name'];

				$message = $trigger . " has assigned you to bug #" . $bugID . ", '" . $bugName . "'";

			}else if ($type == self::TYPE_SECTION){
				$bugInfoQuery = "SELECT bugs.Bug_ID, bugs.Name as 'Bug_Name', sections.Name as 'Section_Name' FROM bugs LEFT JOIN sections ON bugs.Section_ID =sections.Section_ID WHERE bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];
				$bugName = $bugInfo['Bug_Name'];
				$sectionName = $bugInfo['Section_Name'];

				$message = $trigger . " has just submitted '" . $bugName . "' to '" . $sectionName . "' ";

			}else if($type == self::TYPE_BUG){
				$bugInfoQuery = "SELECT Status, bugs.Name as Bug_Name, sections.Name as Section_Name FROM bugs LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID WHERE bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];

				$bugName = $bugInfo['Bug_Name'];
				$bugStatusNumber = $bugInfo['Status'];
				$bugStatusString = self::$bugStatus[$bugStatusNumber];
				$sectionName = $bugInfo['Section_Name'];

				$message = $trigger . " has changed the status of '" . $bugName . "' to '" . $bugStatusString . "'";


			}else if($type == self::TYPE_COMMENT){
				$commentQuery = 
				"SELECT comments.Bug_ID as 'Bug_Number', sections.Name as 'Section_Name', comments.Comment_Text
				FROM comments
				LEFT JOIN bugs ON comments.Bug_ID = bugs.Bug_ID
				LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
				WHERE comments.Object_ID = ?";

				$commentInfo = Connection::query($commentQuery, "s", array($targetOne))[0];

				$bugNumber = $commentInfo['Bug_Number'];
				$comment = strip_tags($commentInfo['Comment_Text']);
				$sectionName = $commentInfo['Section_Name'];

				$comment = self::shorten($comment, 35);

				$message = $trigger . " commented on bug #" . $bugNumber . ": <br>'" . $comment . "'";
				

			}else if ($type == self::TYPE_PLUSONE){
				$typeQuery = 
							"SELECT Object_Type
							FROM objects
							WHERE Object_ID = ?";
				$targetType = Connection::query($typeQuery, "s", array($targetOne))[0]["Object_Type"];

				if ($targetType == Objects::TYPE_BUG){
					$bugQuery = 
								"SELECT bugs.Name as 'Bug_Name', sections.Name as 'Section_Name'
								FROM bugs
									LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
								WHERE bugs.Object_ID = ?";

					$bugInfo = Connection::query($bugQuery, "s", array($targetOne))[0];
					$bugName = $bugInfo['Bug_Name'];
					$sectionName = $bugInfo['Section_Name'];

					$message = $trigger . " +1'd your bug " . $bugName;

				}else if ($targetType == Objects::TYPE_COMMENT){

					$commentQuery = 
									"SELECT Comment_Text, bugs.Name as 'Bug_Name', sections.Name as 'Section_Name'
									FROM comments
										LEFT JOIN bugs ON comments.Bug_ID = bugs.Bug_ID
										LEFT JOIN sections ON sections.Section_ID = bugs.Section_ID
									WHERE comments.Object_ID = ?";

					$commentInfo = Connection::query($commentQuery, "s", array($targetOne))[0];

					$commentText = strip_tags($commentInfo['Comment_Text']);
					$bugName = $commentInfo['Bug_Name'];
					$sectionName = $commentInfo['Section_Name'];

					$commentText = self::shorten($commentText, 35);
					$bugName = self::shorten($bugName, 15);
					$sectionName = self::shorten($sectionName, 15);

					$message = $trigger . " +1'd your comment '" . $commentText . "' from '" . $bugName . "'";

				}


			}else if($type == self::TYPE_MENTION){
				$commentQuery =
								"SELECT Comment_Text, sections.Name as 'Section_Name'
								FROM comments
									LEFT JOIN bugs ON comments.Bug_ID = bugs.Bug_ID
									LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
								WHERE comments.Object_ID = ?";

				$commentResult = Connection::query($commentQuery, "s", array($targetOne))[0];
				$sectionName = $commentResult['Section_Name'];
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
				"section" => $sectionName

				);


			return $output;

		}

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
				array_push($output, self::processNotification($queryResult[$i]));
			}

			return $output;

		}

		public static function newEvent($type, $triggerUser, $triggerObject){
			if ($type == self::TYPE_ASSIGNMENT){
				$insertQuery =
								"INSERT INTO notifications
								VALUES 
								WHERE
								";
			}else if ($type == self::TYPE_SECTION){

			}else if ($type == self::TYPE_BUG){

			}else if ($type == self::TYPE_COMMENT){

			}else if ($type == self::TYPE_PLUSONE){

			}else if ($type == self::TYPE_MENTION){

			}else{
				return false;
			}

		}

		private static function shorten($message, $length){
			$output = $message;

			if (strlen($output) > $length){
				$output = substr($output, 0, $length) . '...';
			}

			return $output;
		}


	}