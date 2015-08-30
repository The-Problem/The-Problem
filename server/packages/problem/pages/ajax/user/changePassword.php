<?php
class AjaxUserChangePasswordPage implements IPage {
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

        $currentUser = Users::getUser('current');

        if (1 == 1 || $currentUser->username == $_POST['username']){
            $currentUser->sendPasswordEmail();
            echo json_encode(true);
        }else{
            echo json_encode(false);
        }
       
	}
}