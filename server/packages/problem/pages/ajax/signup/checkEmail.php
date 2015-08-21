<?php
class AjaxSignupCheckEmailPage implements IPage {
    public function __construct(PageInfo &$page) {
 
    }
    
    public function template() {
        return Templates::findtemplate("blank");
    }
    
    public function subpages() {
        return false;
    }
    public function permission() {
        return true;
    }
    public function head(Head &$head) { }
    
    public function body() {  
	    header('Content-type: application/json');

		$result = filter_var($_GET['address'], FILTER_VALIDATE_EMAIL);
		$returnArray = array(
			"result" => $result
		);

		echo json_encode($returnArray);
	}
}