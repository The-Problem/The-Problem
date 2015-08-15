<?php
	class Users {
		public static function newUser($username, $password, $name, $email){
			$userCreateQuery = "INSERT INTO users (Username, Email, Name, Password, Rank) VALUES ('". $username . "', '" . $email . "', '" . $name . "', '" . $password . "', 0)";
			$query = Connection::query($userCreateQuery);

			return $query;
		}

	}