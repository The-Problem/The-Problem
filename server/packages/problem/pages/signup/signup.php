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
		$head->stylesheet("pages/signup");
		echo "<title>Join The Problem</title>";
		echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
	}

	public function body(){
		?>

			<div id='contentWrapper'>
				<h1>Join The Problem</h1>

				<form id='signUpDetails' name='signUpForm' method='post' action='signupsubmission.php'>
					<label>Username</label><input type='text' name='username' class='formInput'><br>
					<label>Password</label><input type='password' name='password' class='formInput'><br>
					<label>Confirm Password</label><input type='password' name='password' class='formInput'>
				</form>

				<h2>Tell us more about yourself...</h2>

				<div id='form2'>
					<label>Preferred Name</label><input form='signUpDetails' type='text' name='prefName' class='formInput'><br>
					<Label>Email</Label><input form='signUpDetails' type='text' name='prefName' class='formInput'>
				</div>

				<h2>Are you a robot?</h2>
					<div form="signUpDetails" id='captcha' class="g-recaptcha" data-sitekey="6LevLAsTAAAAABX9zurraMJqIijsC39x1ZgYQyEb"></div>

				<button form='signUpDetails' action='submit' id='joinButton'>JOIN</button>

			</div>


		
		<?php
	}
}
?>