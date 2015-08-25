<?php

class User{

	function __construct($username){
		$userQuery = "SELECT * FROM users WHERE Username = ?";
		$queryResult = Connection::query($userQuery, "s", array($username))[0];

		$this->username = $queryResult['Username'];
		$this->password = $queryResult['Password'];
		$this->name = $queryResult['Name'];
		$this->bio = $queryResult['Bio'];
		$this->lastLogon = $queryResult['Last_Logon_Time'];
		$this->email = $queryResult['Email'];
		$this->rank = $queryResult['Rank'];
	}

	public function setPassword($newPassword){
		$query = "UPDATE users SET Password = ? WHERE Username = ?'";
		$queryResult = Connection::query($query, "ss", array($newPassword, $this->username));

	}

	public function setBio($bio){
		$setQuery = 'UPDATE users SET Bio = ? WHERE Username = ?';
		$result = Connection::query($setQuery, "ss", array($bio, $this->username));
	}

	public function getAvatarLink($size = 100){
		$emailHash = md5(strtolower($this->email));
		$imageLink = "http://www.gravatar.com/avatar/" . $emailHash . "?s=" . $size . "d=identicon";
		return $imageLink;
	}

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
			SELECT Last_Logon_Time, Rank, COUNT(Section_ID) as 'Developing_Sections', Creation_Date
			FROM users 
				LEFT JOIN developers ON users.Username = developers.Username
				LEFT JOIN notifications ON users.Username = notifications.Triggered_By
			WHERE users.Username = ?";

		$userInfo = Connection::query($infoQuery, "s", array($this->username))[0];

		$lastLogon = String::timeago($userInfo['Last_Logon_Time']);
		$developingSections = $userInfo['Developing_Sections'];
		$rankInt = $userInfo['Rank'];

		if ($developingSections > 0 && $rankInt == 1){
			$rank = 2;
		}

		if ($rankInt == RANK_ADMIN){
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

		$infoSummary = array(
			'lastActive');

		return var_dump($userInfo);
	}

	public function getInfo(){
		return "Last signed in 6 days ago";
	}
}