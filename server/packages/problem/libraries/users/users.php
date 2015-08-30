<?php

	//class Users provides access to user related properties and methods within The Problem
	class Users {

		//define user type constants
		const RANK_ADMIN = 4;
		const RANK_MOD = 3;
		const RANK_DEVELOPER = 2;
		const RANK_STANDARD = 1;
		const RANK_UNVERIFIED = 0;

		const VERIFY_SALT = 'salty';
		const PASSWORD_SALT = 'wellthatsaproblem';

		//create user from required information
		public static function newUser($username, $password, $name, $email){
			$username = htmlentities(trim($username));
			$name = htmlentities(trim($name));
			$email = htmlentities(trim($email));
			$password = htmlentities(trim($password));

			$userCreateQuery = "INSERT INTO users (Username, Email, Name, Rank) VALUES ( ? , ? , ? , 0)";
			$query = Connection::query($userCreateQuery, "sss", array($username, $email, $name));

			$freshUser = self::getUser($username);
			$freshUser->setPassword($password);

			$_SESSION['username'] = $username;

			self::login($username, $password);
			return $query;
		}

		//returns User object with properties and methods for a single user
		public static function getUser($username){
			
			if ($username == 'current'){
				$username = $_SESSION["username"];
			}

			$validQuery = 
						"SELECT COUNT(Username) as 'count' FROM users WHERE Username = ?";
			$queryResult = Connection::query($validQuery, "s", array($username))[0];
			if ($queryResult['count'] == 1){
				$user = new User($username);
				return $user;
			}else{
				return false;
			}
		}

	

		//returns whether a username may be used
		public static function usernameAvailable($username){
			//returns whether a username is availale for use
			if (!preg_match('/^[\w\d-_]{1,20}$/', $username)){
				return false;
			}

			$usernameQuery  = "SELECT COUNT(Username) FROM users WHERE Username = ? ";
			$queryResult = Connection::query($usernameQuery, "s", array($username));

			if ($queryResult[0]["COUNT(Username)"] == 0){
				return true;
			}else{
				return false;
			}

		}

		//checks whether an email is valid or already used
		public static function emailAvailable($email){
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				return false;
			}

			$emailQuery = 
				"SELECT Username FROM users WHERE Email = ?";
			$emailResult = Connection::query($emailQuery, "s", array($email));

			if (count($emailResult) != 0){
				return false;
			}

			return true;
		}
		
		//logs user in using $_SESSION[]
		public static function login($username, $password){
			Library::get('password');

			if (filter_var($username, FILTER_VALIDATE_EMAIL)){
				$passwordQuery =
						"SELECT Username, Password
						FROM users
						WHERE Email = ?";
			}else{
				$passwordQuery = "SELECT Username, Password FROM users WHERE Username = ?";
			}

			$passwordHashResult = Connection::query($passwordQuery, "s", array($username));

			if ($passwordHashResult && password_verify($password, $passwordHashResult[0]['Password'])){
				$_SESSION['username'] = $passwordHashResult[0]['Username'];
				$updateLogonTimeQuery = "UPDATE users SET Last_Logon_Time = ? WHERE Username = ?";
				$currentTime = date("Y:m:d H:i:s");
				$updateResult = Connection::query($updateLogonTimeQuery, "ss", array($currentTime, $passwordHashResult[0]['Username']));
				return true;
			}

			return false;

		}

		//logs current user off
		public static function logoff(){
			if ($_SESSION['username'] == NULL){
				return false;
			}else{
				$_SESSION["username"] = NULL;
			}
			return true;
		}

		//verifies account from GET information
		public static function verifyAccount($username, $enteredCode){
			$currentUser = self::getUser($username);
			$currentEmail = $currentUser->email;
			$correctCode = md5($currentEmail . self::VERIFY_SALT);

			if ($enteredCode == $correctCode){
				$updateQuery = 
						"UPDATE users
						SET Rank = 1
						WHERE Username = ?";
				$query = Connection::query($updateQuery, "s", array($username));
				return true;
			}else{
				return false;
			}
		}
	}