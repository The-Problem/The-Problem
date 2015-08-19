<?php
class AjaxModulesGetPage implements IPage {
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
		$usernameAvailable = Users::usernameAvailable($_GET['username']);
		$result = (
			"result"=>$usernameAvailable;
		);

		echo json_encode($result);
    }
}