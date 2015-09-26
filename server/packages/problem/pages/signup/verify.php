<?php
//user is taken to this page when clicking on email verify link
class SignupVerifyPage implements IPage{
	public function __construct(PageInfo &$page){
	}

	public function template(){
		return Templates::findtemplate("default");
	}

	public function permission(){

		return true;
		
		if($_SESSION['signedup']){
			return true;
		}

		return false;
	}

	public function subpages(){
		return true;
	}

	public function head(Head &$head){
		//$head->stylesheet("pages/signupsuccess");
		$head->title = "Verify Your Account";
		$head->stylesheet("pages/verify");
	}

	public function body(){
		Library::get('users');

		if (isset($_GET['username'])){
			$username = $_GET['username'];
			$code = $_GET['code'];

			$verificationSuccess = Users::verifyAccount($username, $code);
		}

		if ($verificationSuccess){
			$_SESSION['verified'] = true;
			Path::redirect(Path::getclientfolder('login'));
		}else{
			?>

			<h1>Oops</h1>
			<p>It looks like there was an issue activating your accounnt.</p>
			<p>Please contact your system administrator for assistance.</p>

			<?php
		}


		
	}
}