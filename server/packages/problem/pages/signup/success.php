<?php
class SignupSuccessPage implements IPage{
	public function __construct(PageInfo &$page){
	}

	public function template(){
		return Templates::findtemplate("default");
	}

	public function permission(){
		/*if (!(isset($_SESSION['signedup']) && $_SESSION['signedup'] == true)){
			return false;
		}*/

		return true;
	}

	public function subpages(){
		return true;
	}

	public function head(Head &$head){
		//$head->stylesheet("pages/signupsuccess");
		$head->title = "Account Created";
		$head->stylesheet("pages/success");
	}

	public function body(){
		?>

		<h1>You are now part of The Problem</h1>
		<p>You'll need to verify your account before you begin working.</p>

		<?php
		
	}
}
?>