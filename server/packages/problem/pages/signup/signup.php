<?php
class SignUpPage implements IPage{
	public function __construct(PageInfo &$page){
	}

	public function template(){
		return Templates::findtemplate("default");
	}

	public function permission(){
		return true;
	}

	public function subpages(){
		return true;
	}

	public function head(Head &$head){
		$head->title .= " - Sign Up";
		$head->stylesheet("pages/signup");
		$head->script("https://www.google.com/recaptcha/api.js", true);
	}
	
	private function passedCAPTCHA(){
		if (!isset($_POST['g-recaptcha-response'])){
			return false;
		}else{
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

		if ($googleResponse.success){
			return true;
		}else{
			return false;
		}
		
	}

	private function dataValid(){
		if (isset($_POST['username'])){

			if (strlen($_POST['username']) < 1 || strpos($_POST['username'], " ") != FALSE){
				return 2;
			}
			//echo "Nice username<br>";
			if (!$this::validPassword()){
				return 2;
			}
			//echo "Nice password<br>";
			if ($_POST["password"] != $_POST['rpassword']){
				return 2;
			}
			//echo "They match as well<br>";
			if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
				return 2;
			}
			//echo "I like that email address<br>";
			if (!$this::passedCAPTCHA()){
				echo "failed captcha<br>";
				return 2;
			}
			//echo "You're not a robot!";

			return 1;

		}else{
			return 0;
		}
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
		$pageState = $this::dataValid();
		echo "Pagestate: " . $pageState;

		if ($pageState == 1){
			Path::redirect(Path::getclientfolder("signup", "success"));
		}
	
		?>

			<div id='contentWrapper'>

				<h1>Join The Problem</h1>
				
				<?php

				if($pageState == 2){
					$errorMessage = "There were some issues with the details you entered. Please fix them to continue.";
					echo "<p class='invalidMessage'>" . $errorMessage . "</p>";
				}

				?>

				<form id='signUpDetails' name='signUpForm' method='post'>
					<fieldset>
					<label>Username</label><input type='text' name='username' class='formInput'><br>
					<label>Password</label><input type='password' name='password' class='formInput'><br>
					<label>Confirm Password</label><input type='password' name='rpassword' class='formInput'>
					</fieldset>
				
					<h2>Tell us more about yourself...</h2>
					
					<fieldset>
						<label>Preferred Name</label><input form='signUpDetails' type='text' name='prefName' class='formInput'><br>
						<Label>Email</Label><input form='signUpDetails' type='text' name='email' class='formInput'>
					</fieldset>
				
					<h2>Are you a robot?</h2>
					
					<div form="signUpDetails" id='captcha' class="g-recaptcha" data-sitekey="6LevLAsTAAAAABX9zurraMJqIijsC39x1ZgYQyEb"></div>
					
					<button action='submit' id='joinButton'>JOIN</button>
				</form>

			</div>


		
		<?php
	}
}
?>