<?php
//password reset page
class LoginNewPasswordPage implements IPage{
	public function __construct(PageInfo &$page){
	}

	public function template(){
		return Templates::findtemplate("default");
	}

	public function permission(){	
		//change this, users should only be able to see this page when they are not logged in
		
	Library::get('users');
	$currentUser = Users::getUser('current');
	
	if ($_SESSION['username'] == $_GET['username']){
		$code = $_GET['code'];
		$actualCode = md5($currentUser->username . $currentUser::PASSWORD_SALT);
		if ($code == $actualCode){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
		
	}

	public function subpages(){
		return true;
	}

	public function head(Head &$head){
		$head->title .= " - Log In";
		$head->stylesheet("pages/login");
	}
	
	public function body(){

		function validPassword($password){
			if (strlen($password) < 8){
				return false;
			}

			if (!preg_match('/\d/', $password) || !preg_match('/[A-Z]/i', $password)){
				return false;
			}

			return true;
		}

		function verifyFields(){
			if (!isset($_POST['oldPassword'])){
				return false;
			}
			if (!isset($_POST['newPassword'])){
				return false;
			}
			if (!isset($_POST['rPassword'])){
				return false;
			}

			if ($_POST['newPassword'] == $_POST['oldPassword']){
				return false;
			}

			if ($_POST['newPassword'] != $_POST['rPassword']){
				return false;
			}

			return true;
		}

		//if there is POST data, validate before changing password
		//if there is an error, show message to user
		$currentUser = Users::getUser('current');

		if (isset($_POST['oldPassword'])){
			$entriesValid = verifyFields();

			$canChange = true;

			if (!$entriesValid){
				$canChange = false;
				$message = 'Looks like there was a problem.';
			}

			if (!validPassword($_POST['newPassword'])){
				$canChange  = false;
				$message = "Your new password is not valid.";
			}

			if (!Users::checkPassword($currentUser->username, $_POST['oldPassword'])){
				$canChange = false;
				$message = "Your old password was incorrect.";
			}

			if ($canChange){
				$currentUser->setPassword($_POST['newPassword']);
				$message = 'Your password has been changed';
			}
		}

		?>
	

		<div id='loginBox'>
			<form method='post'>
				<h1>Change Your Password</h1>
				<?php if(isset($message)) echo "<p>" . $message . "</p>";?>
				<input id='userField' type='password' name='oldPassword' placeholder='Old Password'><br>
				<input id='passField' type='password' name='newPassword' placeholder='New Password'><br>
				<input id='rpassField' type='password' name='rPassword' placeholder='Re-enter New Password'><br>
				<button action='submit'>CHANGE PASSWORD</button>
			</form>
		</div>

		<?php
	}
}
