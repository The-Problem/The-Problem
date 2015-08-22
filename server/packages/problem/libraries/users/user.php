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
}