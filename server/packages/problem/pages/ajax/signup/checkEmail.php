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
        Library::get('users');

        $status = Users::emailAvailable($_GET['address'], $_GET['new']);

		$returnArray = array(
			"result" => $status
		);

		echo json_encode($returnArray);
	}
}