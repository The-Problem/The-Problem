<?php

	//class User provides access to user properties and methods

	class User{

		function __construct($username){
			$userQuery = "SELECT * FROM users WHERE Username = ?";
			$queryResult = Connection::query($userQuery, "s", array($username))[0];

			//set object properties
			$this->username = $queryResult['Username'];
			$this->passwordHash = $queryResult['Password'];
			$this->name = $queryResult['Name'];
			$this->bio = $queryResult['Bio'];
			$this->lastLogon = $queryResult['Last_Logon_Time'];
			$this->email = $queryResult['Email'];
			$this->rank = $queryResult['Rank'];
		}

		//change the password
		public function setPassword($newPassword){
			//find current password
			/*$currentPasswordQuery = 
					"SELECT Password
					FROM users
					WHERE username = ?";
			$currentPassword = Connection::query($currentPasswordQuery, "s", array($this->username))[0]['password'];

			if ($newPass)
			$query = "UPDATE users SET Password = ? WHERE Username = ?'";
			$queryResult = Connection::query($query, "ss", array($newPassword, $this->username));*/

			$passwordHash = password_hash($newPassword);

			$setPasswordQuery = 
					"UPDATE users SET Password = ? WHERE Username = ?";
			$setPassword = Connection::query($setPasswordQuery, "ss", array($passwordHash, $this->username));

		}

		//changes the bio of the current user
		public function setBio($bio){
			$setQuery = 'UPDATE users SET Bio = ? WHERE Username = ?';
			$result = Connection::query($setQuery, "ss", array($bio, $this->username));
		}

		//gets a link to the current user's profile picture
		public function getAvatarLink($size = 100){
			$emailHash = md5(strtolower($this->email));
			$imageLink = "http://www.gravatar.com/avatar/" . $emailHash . "?s=" . $size . "&d=identicon";
			return $imageLink;
		}

		//if existing, gets the current user's cover picture
		public function getCoverPhoto($size = 1920){
			Library::get('image');
			$photoSettings = array(
					"format" => "jpg",
					"minsize" => "1920x280",
					"maxsize" => "1920x280"
			);

			$coverPhoto = new Image("profile-cover", $this->username, $photoSettings);

			return $coverPhoto->clientpath;
		}

		public function getSummary(){
			$infoQuery = "
				SELECT Last_Logon_Time, Rank, COUNT(developers.Section_ID) as 'Developing_Sections', COUNT(DISTINCT Bug_ID) as 'Bugs'
				FROM users 
					LEFT JOIN developers ON users.Username = developers.Username
					LEFT JOIN bugs ON bugs.Author = users.Username
				WHERE users.Username = ?";

			$userInfo = Connection::query($infoQuery, "s", array($this->username))[0];

			$lastLogon = $userInfo['Last_Logon_Time'];
			$developingSections = $userInfo['Developing_Sections'];
			$bugs = $userInfo['Bugs'];
			$rankInt = $userInfo['Rank'];

			//Find when last notification was generated by user
			$activeQuery = 
							'SELECT Creation_Date
							FROM notifications
							WHERE Triggered_By = ?
							ORDER BY Creation_Date DESC';
			$lastActive = Connection::query($activeQuery, "s", array($this->username))[0]['Creation_Date'][0];

			if ($lastLogon > $lastActive){
				$lastActive = $lastLogon;
			}

			//Find when number of bugs 

			if ($developingSections > 0 && $rankInt == 1){
				$rank = 2;
			}

			if ($rankInt == Users::RANK_ADMIN){
				$rank = 'Administrator';
			}else if($rankInt == Users::RANK_MOD){
				$rank = 'Moderator';
			}else if ($rankInt == Users::RANK_DEVELOPER){
				$rank = 'Developer';
			}else if ($rankInt == Users::RANK_STANDARD){
				$rank = 'Lurker';
			}else if ($rankInt == Users::RANK_UNVERIFIED){
				$rank = 'Unverified';
			}

			$infoHTML = $rank . "<br>" . $bugs;

			if ($bugs == 1){
				$infoHTML .= " bug";
			}else{
				$infoHTML .= " bugs";
			}

			if ($rankInt >= 2){
				$infoHTML .= " - developing in " . $developingSections ;

				if ($developingSections == 1){
					$infoHTML .= ' section';
				}else {
					$infoHTML .= ' sections';
				}
			}

			$infoHTML .= "<br>" . "Last active " . strtolower(String::timeago($lastActive));

			$infoSummary = array(
				'lastActive' => String::timeago($lastActive),
				'rank' => $rank,
				'developing' => $developingSections,
				'bugs' => $bugs
			);

			return $infoHTML;
		}

		//returns an array of associative arrays with information about the sections the current user is developing in
		public function getSections(){
			$sectionQuery =
					"SELECT sections.name as 'Name', sections.color as 'Colour', Bug_Table.Open_Bugs as 'Open_Bugs'
					FROM developers
						LEFT JOIN sections ON developers.Section_ID = sections.Section_ID
						LEFT JOIN (
										SELECT sections.Name, sections.Section_ID, COUNT(DISTINCT Open_Bug_Table.Name) as 'Open_Bugs', sections.Color
										FROM sections 
										LEFT JOIN (
														SELECT Bug_ID, Name, Section_ID 
														FROM bugs 
														WHERE Status = 1
					                    			) as Open_Bug_Table ON Open_Bug_Table.Section_ID = sections.Section_ID
										GROUP BY sections.Section_ID 
					               ) as Bug_Table ON Bug_Table.Section_ID = developers.Section_ID
					WHERE developers.Username = ?";
			$userSections = Connection::query($sectionQuery, "s", array($this->username));

			return $userSections;
		}
	
		public function getBugs(){
			$query = 
				"SELECT bugs.Name as 'Bug_Name', sections.Name as 'Section_Name', sections.Color as 'Colour'
                FROM bugs
                    LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
                    LEFT JOIN plusones ON bugs.Object_ID = plusones.Object_ID
                WHERE bugs.Assigned = ?";

			$queryResult = Connection::query($query, "s", array($this->username));
			return $queryResult;
		}

		public function sendVerificationEmail(){
			$salt = Users::VERIFY_SALT;
			$code = md5($this->email . $salt);
			$verifyLink = "http://www.theproblem.dreamhosters.com/signup/verify?username=" . $this->username . "&code=" . $code;

			$to = $this->email;
			$subject = "Verfiy your account with" .  htmlentities(Pages::$template->title) . "!";
			date_default_timezone_set("Australia/Brisbane");

			$messageHTML = 

						'<body style="padding-left: 0px; padding-right: 0px; padding-top: 0px; padding-bottom: 0px; margin-left: 0px; margin-right: 0px;' 
						. 'margin-top: 0px; margin-bottom: 0px"><div style="font-family: sans-serif">'. "\r\n" 
						."<h1>Welcome To" . $title_res[0]["Value"] . "!</h1>"
						."<p>Click the link below to verify your account and get started.</p><br>"
						.'<a href="' . $verifyLink . '">' . $verifyLink . "</a></div></body>";


			$headers = "From: noreply@theproblem.dreamhosters.com" . "\r\n";
			$headers .= "Reply-To: noreply@theproblem.dreamhosters.com" . "\r\n";
			$headers .= "Content-type: text/html" . "\r\n";


			return mail($to, $subject, $messageHTML, $headers);
		}
	}