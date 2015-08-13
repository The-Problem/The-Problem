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

		if (isset($_POST)){
			foreach($_POST as $key => $value){
				echo $key . ": " . $value;
			}
		}else{
			echo "There is no post";
		}
	}
}
?>