<?php
//page for signing up
//only accessable when logged out
class SignupPage implements IPage{
	private $template;

	public function __construct(PageInfo &$page){
	}

	public function template(){
		$this->template = Templates::findtemplate("default");
		return $this->template;
	}

	public function permission(){
		return !$_SESSION["username"];
	}

	public function subpages(){
		return true;
	}

	public function head(Head &$head){
		$head->title .= " - Sign Up";
		$head->stylesheet("pages/signup");
		$head->script("https://www.google.com/recaptcha/api.js", true);
		$head->script("pages/signup");
	}
	
	private function passedCAPTCHA(){
		if (!isset($_POST['g-recaptcha-response'])){
			return false;
		}

		$verifyPage = "https://www.google.com/recaptcha/api/siteverify";
		$postData = array('secret'=> '6LevLAsTAAAAAGp5eav8VjQ9ZZAZrpNiK8TlCmZa', 'response' => $_POST['g-recaptcha-response']);

		$requestSettings = array(
    		'http' => array(
        		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        		'method'  => 'POST',
        		'content' => http_build_query($postData),
    		),
		);

		$context = stream_context_create($requestSettings);
		$googleResponse = file_get_contents($verifyPage, false, $context);

		return json_decode($googleResponse)->success;
		
	}

	private function dataValid(){
		if (isset($_POST['username'])){

			if (strlen($_POST['username']) < 1 || strpos($_POST['username'], " ") != FALSE){
				return 2;
			}
			if (!$this::validPassword()){
				return 2;
			}
			if ($_POST["password"] != $_POST['rpassword']){
				return 2;
			}
			if (!Users::emailAvailable($_POST['email'], true)){
				return 2;
			}
			if (!$this::passedCAPTCHA()){
				return 2;
			}

			return 1;

		}

		return 0;
	}


	private function validPassword(){
		$password = $_POST['password'];
		if (strlen($password) < 8){
			return false;
		}

		if (!preg_match('/\d/', $password) || !preg_match('/[A-Z]/i', $password)){
			return false;
		}

		return true;
	}


	public function body(){

		Library::get("users");
		$pageState = $this::dataValid();

		if ($pageState == 1){

			//Create user in the Database
			
			$username = $_POST['username'];
			$name = $_POST['prefName'];
			$password = $_POST['password'];
			$email = $_POST['email'];

			Users::newUser($username, $password, $name, $email);

			//Take the user to the success page
			$_SESSION['signedup'] = true;
			Path::redirect(Path::getclientfolder("signup", "success"));
		}
	
		?>

		<h1>Join <?php echo $this->template->title; ?></h1>
		
		<?php

		if($pageState == 2){
			$errorMessage = "There were some issues with the details you entered. Please fix them to continue.";
			echo "<script>window.addEventListener('load', function(){validateForm();}, false);</script>";
		}

		echo "<div id='messageDiv'><p id='invalidMessage'>" . $errorMessage . "</p></div>";
		?>



		<form id='signUpDetails' name='signUpForm' method='post'>
			<fieldset>
				
				<div class='fieldContainer'>
					<label>Username</label><input type='text' name='username' class='formInput' id='usernameField' value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>"><i id='usernameIcon' class='verifyIcon'></i>
				</div>

				<div class='fieldContainer'>
					<label>Password</label><input type='password' name='password' class='formInput' id='passwordField'><i id='passwordIcon' class='verifyIcon'></i>
				</div>

				<div class='fieldContainer'>
					<label>Confirm Password</label><input type='password' name='rpassword' class='formInput' id='rpasswordField'><i id='rpasswordIcon' class='verifyIcon'></i>
				</div>

			</fieldset>
		
			<h2>Tell us more about yourself...</h2>
			
			<fieldset>
				<div class='fieldContainer'>
					<label>Preferred Name</label><input form='signUpDetails' type='text' name='prefName' class='formInput' id='nameField' value="<?php if(isset($_POST['username'])) echo $_POST['prefName']; ?>"><i id='nameIcon' class='verifyIcon'></i>
				</div>
				<div class='fieldContainer'>
					<Label>Email</Label><input form='signUpDetails' type='text' name='email' class='formInput'  id='emailField' value="<?php if(isset($_POST['username'])) echo $_POST['email']; ?>"><i id='emailIcon' class='verifyIcon'></i>
				</div>

			</fieldset>
		
			<h2>Are you a robot?</h2>
			
			<div form="signUpDetails" id='captcha' class="g-recaptcha" data-sitekey="6LevLAsTAAAAABX9zurraMJqIijsC39x1ZgYQyEb"></div>
			
			<button action='submit' id='joinButton' class='highlight'>JOIN</button>
		</form>	


		<?php
	}
}
?>