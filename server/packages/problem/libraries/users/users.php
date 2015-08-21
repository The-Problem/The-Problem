<?php
	class Users {

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


		public static function login($username, $password){
			echo "username: " . $username;
			echo "password: " .  $password;
			
			$passwordQuery = "SELECT *  FROM users WHERE Username = ? AND Password = ?";
			$queryResult = Connection::query($passwordQuery, "ss", array($username, $password));

			echo var_dump($queryResult);

			if ($queryResult){
				$_SESSION['username'] = $username;
			}

			return $queryResult;
		}

		public static function getUser($username){
			
			if ($username == 'current'){
				$username = $_SESSION["username"];
			}

			$user = new User($username);
			return $user;
		}

		public static function usernameAvailable($username){
			//returns whether a username is availale for use
			if (strlen($username) < 2){
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

		public static function logoff(){
			$_SESSION["username"] = NULL;
		}


	}