<?php
	class Notifications {
		const TYPE_ASSIGNMENT = 0;
		const TYPE_SECTION = 1;
		const TYPE_BUG = 2;
		const TYPE_COMMENT = 3;
		const TYPE_PLUSONE = 4;
		const TYPE_MENTION = 5;

		private static $bugStatus = array('DELETED', 'OPEN', 'CLOSED', 'DUPLICATE', 'WIP');

		public static function get($limit = 10, $time = NULL, $before = false){

			//get notification data as associative array from database depending on arguments
			$currentUser = $_SESSION['username'];

			if ($time != NULL){
				if ($before){
					//return the first $limit number of notifications before $time
					$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By= ? AND Creation_Date <= ? ORDER BY Creation_Date DESC LIMIT ?";
					$queryResult = Connection::query($notificationQuery, "sss", array($currentUser, $time));
				}else{
					//return all notifications after $time
					$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By= ? AND Creation_Date >= ? ORDER BY Creation_Date DESC";
					$queryResult = Connection::query($notificationQuery, "ss", array($currentUser, $time));
				}

			}else{
				//return the first $limit number of notifications as of now
				$notificationQuery = "SELECT Triggered_By, Target_One, Target_Two, Creation_Date, IsRead, Type FROM notifications WHERE Received_By = ? ORDER BY Creation_Date DESC LIMIT ?";
				$queryResult = Connection::query($notificationQuery, "ss", array($currentUser, $limit));
			}

			//loop through notification data from create html

			$html = "";

			for ($i = 0; $i < count($queryResult); $i++){
				$notificationDetails = self::processNotification($queryResult[$i]);
				$notSection = "<section class='notificationCell'><p class='message'>" . $notificationDetails['message'] . "</p><p class='stats'>" . $notificationDetails['stats'] . "</p></section>";
				$html .= $notSection;
			}

			return $html;

		}

		public static function make(){
			$currentUser = $_SESSION['username'];
		}

		private static function processNotification($notification){
			
			/*0 - "Angela has assigned you to  bug #50 'Needs More Unicorns'"
			1 - "'Oversized Buttons' has just been submitted to Users"
			2 - "'Oversized Buttons' has had its status changed to 'WIP'"
			3 - "Patrick commented on 'Needs More Unicorns'"
			4 - "Tom +1'd your bug, 'Needs More Dragons'"
			5 - "Patrick mentioned you in a comment from 'Needs More Dragons"*/

			Library::get('objects');

			$message = "";
			$stats = "";

			$trigger = $notification['Triggered_By'];
			$type = $notification['Type'];
			$targetOne = $notification['Target_One'];
			Library::get('string');
			$fuzzyTime = String::timeago($notification['Creation_Date']);

			if ($type == self::TYPE_ASSIGNMENT){
				$bugInfoQuery = "SELECT Bugs.Bug_ID, Bugs.Name as 'Bug_Name', Sections.Name as 'Section_Name' FROM Bugs LEFT JOIN Sections ON Bugs.Section_ID =Sections.Section_ID WHERE Bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];
				
				$bugID = $bugInfo['Bug_ID'];
				$bugName = $bugInfo['Bug_Name'];
				$sectionName = $bugInfo['Section_Name'];

				$message = $trigger . " has assigned you to bug #" . $bugID . ", '" . $bugName . "'";

			}else if ($type == self::TYPE_SECTION){
				$bugInfoQuery = "SELECT Bugs.Bug_ID, Bugs.Name as 'Bug_Name', Sections.Name as 'Section_Name' FROM Bugs LEFT JOIN Sections ON Bugs.Section_ID =Sections.Section_ID WHERE Bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];
				$bugName = $bugInfo['Bug_Name'];
				$sectionName = $bugInfo['Section_Name'];

				$message = $trigger . " has just submitted '" . $bugName . "' to '" . $sectionName . "' ";

			}else if($type == self::TYPE_BUG){
				$bugInfoQuery = "SELECT Status, Bugs.Name as Bug_Name, Sections.Name as Section_Name FROM Bugs LEFT JOIN Sections ON Bugs.Section_ID = Sections.Section_ID WHERE Bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];

				$bugName = $bugInfo['Bug_Name'];
				$bugStatusNumber = $bugInfo['Status'];
				$bugStatusString = self::$bugStatus[$bugStatusNumber];
				$sectionName = $bugInfo['Section_Name'];

				$message = $trigger . " has changed the status of '" . $bugName . "' to '" . $bugStatusString . "'";


			}else if($type == self::TYPE_COMMENT){
				$commentQuery = 
				"SELECT Comments.Bug_ID as 'Bug_Number', Sections.Name as 'Section_Name', Comments.Comment_Text
				FROM Comments
				LEFT JOIN Bugs ON Comments.Bug_ID = Bugs.Bug_ID
				LEFT JOIN Sections ON Bugs.Section_ID = Sections.Section_ID
				WHERE Comments.Object_ID = ?";

				$commentInfo = Connection::query($commentQuery, "s", array($targetOne))[0];

				$bugNumber = $commentInfo['Bug_Number'];
				$comment = strip_tags($commentInfo['Comment_Text']);
				$sectionName = $commentInfo['Section_Name'];

				$comment = self::shorten($comment, 35);

				$message = $trigger . " commented on bug #" . $bugNumber . ": <br>'" . $comment . "'";
				

			}else if ($type == self::TYPE_PLUSONE){
				$typeQuery = 
							"SELECT Object_Type
							FROM Objects
							WHERE Object_ID = ?";
				$targetType = Connection::query($typeQuery, "s", array($targetOne))[0]["Object_Type"];

				if ($targetType == Objects::TYPE_BUG){
					$bugQuery = 
								"SELECT Bugs.Name as 'Bug_Name', Sections.Name as 'Section_Name'
								FROM Bugs
									LEFT JOIN Sections ON Bugs.Section_ID = Sections.Section_ID
								WHERE Bugs.Object_ID = ?";

					$bugInfo = Connection::query($bugQuery, "s", array($targetOne))[0];
					$bugName = $bugInfo['Bug_Name'];
					$sectionName = $bugInfo['Section_Name'];

					$message = $trigger . " +1'd your bug " . $bugName;
				}else if ($targetType == Objects::TYPE_COMMENT){
					$commentQuery = 
									"SELECT Comment_Text, Bugs.Name as 'Bug_Name', Sections.Name as 'Section_Name'
									FROM Comments
										LEFT JOIN Bugs ON Comments.Bug_ID = Bugs.Bug_ID
										LEFT JOIN Sections ON Sections.Section_ID = Bugs.Section_ID
									WHERE Comments.Object_ID = ?";

					$commentInfo = Connection::query($commentQuery, "s", array($targetOne))[0];

					$commentText = $commentInfo['Comment_Text'];
					$bugName = $commentInfo['Bug_Name'];
					$sectionName = $commentInfo['Section_Name'];

					$commentText = self::shorten($commentText, 35);
					$bugName = self::shorten($bugName, 15);
					$sectionName = self::shorten($sectionName, 15);

					$message = $trigger . " +1'd your comment '" . $commentText . "' from '" . $bugName . "'";

				}


			}else if($type == self::TYPE_MENTION){

			}else{
				
			}

			$stats = $fuzzyTime . " - " . $sectionName;

			$output = array(
				"fuzzyTime" => $fuzzyTime,
				"sectionName" => $sectionName, 
				"time" => $notification['Creation_Date'],
				"message" => $message,
				"stats" => $stats

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

		private static function shorten($message, $length){
			$output = $message;

			if (strlen($output) > $length){
				$output = substr($output, 0, $length) . '...';
			}

			return $output;
		}


	}