<?php
class LoginPage implements IPage{
	public function __construct(PageInfo &$page){
	}

	public function template(){
		return Templates::findtemplate("default");
	}

	public function permission(){
		//change this, users should only be able to see this page when they are not logged in
		return true;
	}

	public function subpages(){
		return false;
	}

	public function head(Head &$head){
		$head->title .= " - Log In";
		$head->stylesheet("pages/login");
		$head->script("pages/login");
	}
	
	public function body(){
		if (isset($_POST['username'])){
			$username = trim($_POST['username']);
			$password = $_POST['password'];

			if (strlen($username) > 0 && strlen($password) > 0){
				Library::get("users");
				$loginState = Users::login($username, $password);
				echo var_dump($loginState);

				if ($loginState){
					Path::redirect(Path::getclientfolder());
					echo 'going home';
				}else{
					$message = "These login credentials appear to be incorrect. Please try again.";
				}
			}else{
				$message = "Please enter a valid username/email and password.";
			}
		}
		?>

		<div id='loginBox'>
			<form method='post'>
				<h1>Sign In</h1>
				<?php if(isset($message)) echo "<p>" . $message . "</p>";?>
				<input id='userField' type='text' name='username' placeholder='Username / Email' value='<?php if(isset($_POST['username'])) echo $_POST['username']; ?>'><br>
				<input id='passField' type='password' name='password' placeholder='Password'><br>
				<button class='highlight' action='submit'><u>SIGN IN</u></button>
			</form>
		</div>

		<?php
	}
}
?>