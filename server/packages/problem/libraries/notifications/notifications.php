<?php
	class Notifications {
		const TYPE_ASSIGNMENT = 0;
		const TYPE_SECTION = 1;
		const TYPE_BUG = 2;
		const TYPE_COMMENT = 3;
		const TYPE_PLUSONE = 4;
		const TYPE_MENTION = 5;

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
			//echo var_dump($queryResult);

			$html = "";

			foreach ($queryResult as $notification){
				$html .= self::generateHTMl($notification);
			}

			return $html;




		}

		public static function make(){
			$currentUser = $_SESSION['username'];
		}

		private static function generateHTML($notification){
			
			/*0 - "Angela has assigned you to  bug #50 'Needs More Unicorns'"
			1 - "'Oversized Buttons' has just been submitted to Users"
			2 - "'Oversized Buttons' has had its status changed to 'WIP'"
			3 - "Patrick commented on 'Needs More Unicorns'"
			4 - "Tom +1'd your bug, 'Needs More Dragons'"
			5 - "Patrick mentioned you in a comment from 'Needs More Dragons"*/

			$message = "";
			$stats = "";

			$trigger = $notification['Triggered_By'];
			$type = $notification['Type'];
			$targetOne = $notification['Target_One'];
			

			if ($type == TYPE_ASSIGNMENT){
				$bugInfoQuery = "SELECT Bugs.Bug_ID, Bugs.Name as 'Bug_Name', Sections.Name as 'Section_Name' FROM Bugs LEFT JOIN Sections ON Bugs.Section_ID =Sections.Section_ID WHERE Bugs.Object_ID = ?";
				$bugInfo = Connection::query($bugInfoQuery, "s", array($targetOne))[0];
				
				echo var_dump($bugInfo);

				$bugID = $bugInfo['Bug_ID'];
				$bugName = $bugInfo['Bug_Name'];
				$sectionName = $bugInfo['Section_Name'];

				$message = $trigger . " has assigned you to bug #" . $bugID . ", '" . $bugName . "'";
				$stats = "";

			}else if ($type == TYPE_SECTION){
				return "";

			}else if($type == TYPE_BUG){
				return "";

			}else if($type == TYPE_COMMENT){
				return "";

			}else if ($type == TYPE_PLUSONE){
				return '';

			}else if($type == TYPE_MENTION){
				return "";

			}

			$html = "<section class='notificationCell'><p class='message'>" . $message. "</p><p class='stats'>" . $stats . "</p></section>";
			return $html;

		}
	}