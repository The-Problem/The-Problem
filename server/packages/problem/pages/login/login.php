<?php
//page for logging in
class LoginPage implements IPage{
	public function __construct(PageInfo &$page){
	}

	public function template(){
		return Templates::findtemplate("default");
	}

	public function permission(){
		//change this, users should only be able to see this page when they are not logged in
		return !$_SESSION["username"];
	}

	public function subpages(){
		return true;
	}

	public function head(Head &$head){
		$head->title .= " - Log In";
		$head->stylesheet("pages/login");
	}
	
	public function body(){
		Library::get('users');

		//if POST request is received, verify login details to log the user
		//otherwise show an error message
		if (isset($_POST['username'])){
			$username = trim($_POST['username']);
			$password = $_POST['password'];

			if (strlen($username) > 0 && strlen($password) > 0){
				Library::get("users");
				$loginState = Users::login($username, $password);

				if ($loginState){
					Path::redirect(Path::getclientfolder());
				}else{
					$message = "These login credentials appear to be incorrect. Please try again.";
				}
			}else{
				$message = "Please enter a valid username/email and password.";
			}
		}else if ($_SESSION['verified']){
			$message = "Your account has been activated. Please enter your username and password to login.";
			$_SESSION['verified'] = false;
		}
		?>
	

		<div id='loginBox'>
			<form method='post'>
				<h1>Sign In</h1>
				<?php if(isset($message)) echo "<p>" . $message . "</p>";?>
				<input id='userField' type='text' name='username' placeholder='Username / Email' value='<?php if(isset($_POST['username'])) echo $_POST['username']; ?>'><br>
				<input id='passField' type='password' name='password' placeholder='Password'><br>
				<button action='submit'>SIGN IN</button>
			</form>
		</div>

		<?php
	}
}