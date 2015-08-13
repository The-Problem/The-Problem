<?php
class SignUpSuccessPage implements IPage{
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
		//$head->stylesheet("pages/signupsuccess");
		$head->title = "Account Created";
	}

	public function body(){

		?>
		<h1>Welcome to The Problem</h1>
		<p>To begin, verify your account by clicking the link in the email we sent you.</p>
		<?php
	}
}
?>