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
		$head->stylesheet("pages/signup");
		echo "<title>Join The Problem</title>";
		echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
	}

	public function body(){
	}
}
?>