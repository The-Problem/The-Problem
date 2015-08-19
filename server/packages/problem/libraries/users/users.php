<?php
	class Users {

		public static function newUser($username, $password, $name, $email){
			$username = trim($username);
			$name = trim($name);
			$email = trim($email);
			$userCreateQuery = "INSERT INTO users (Username, Email, Name, Password, Rank) VALUES ( ? , ? , ? , ? , 0)";
			$query = Connection::query($userCreateQuery, "ssss", array($username, $email, $name, $password));

			Library::get('cookies');
			Cookies::prop('username', $username);

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
				Library::get('cookies');
				Cookies::prop('username', $username);
			}

			return $queryResult;
		}

		public static function getUser($username){
			
			if ($username == 'current'){
				Library::get('cookies');
				$username = Cookies::prop("username");
			}

			$user = new User($username);
			return $user;
		}

		public static function usernameAvailable($username){
			//returns whether a username is availale for use
			$usernameQuery  = "IF NOT EXISTS (SELECT 1 FROM users WHERE Username = ?)";
			$queryResult = Connection::query($usernameQuery, "s", $username);

			echo var_dump($queryResult);

			return $queryResult;
		}

		public static function logoff(){
			Library::get('cookies');
			Cookies::prop("username", NULL);
		}


	}