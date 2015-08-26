
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

		//create user from required information
		public static function newUser($username, $password, $name, $email){
			$username = trim($username);
			$name = trim($name);
			$email = trim($email);
			$userCreateQuery = "INSERT INTO users (Username, Email, Name, Password, Rank) VALUES ( ? , ? , ? , ? , 0)";
			$query = Connection::query($userCreateQuery, "ssss", array($username, $email, $name, $password));

			$_SESSION['username'] = $username;

			self::login($username, $password);
			return $query;
		}

		//returns User object with properties and methods for a single user
		public static function getUser($username){
			
			if ($username == 'current'){
				$username = $_SESSION["username"];
			}else{
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

		}

		//returns whether a username may be used
		public static function usernameAvailable($username){
			//returns whether a username is availale for use
			if (strlen($username) < 2 || !strpos($username, " ")){
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
		
		//logs user in using $_SESSION[]
		public static function login($username, $password){
			echo "username: " . $username;
			echo "password: " .  $password;
			
			$passwordQuery = "SELECT *  FROM users WHERE Username = ? AND Password = ?";
			$queryResult = Connection::query($passwordQuery, "ss", array($username, $password));
			
			$updateLogonTimeQuery = "UPDATE users SET Last_Logon_Time = NOW() WHERE Username = ?";
			$updateResult = Connection::query($updateLogonTimeQuery, "s", array($username));

			echo var_dump($queryResult);

			if ($queryResult){
				$_SESSION['username'] = $username;
			}

			return $queryResult;
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
		public function verifyAccount($username, $enteredCode){
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