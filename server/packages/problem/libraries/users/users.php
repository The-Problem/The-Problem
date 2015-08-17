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
			$passwordQuery = "SELECT Password FROM users WHERE Username = ?";
			$queryResult = Connection::query($passwordQuery, "s", array($username));

			echo var_dump($queryResult[0]["Password"]);

			Library::get('cookies');
			Cookies::prop('username', $username);

			return ($queryResult[0]['Password'] == $password);
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
		}

		public static function logoff(){
			Library::get('cookies');
			Cookies::prop("username", NULL);
		}


	}