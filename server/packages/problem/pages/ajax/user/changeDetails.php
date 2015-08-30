<?php
class AjaxUserChangeDetailsPage implements IPage {
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
        $output = true;
        $newName = $_POST['name'];
        $newEmail = $_POST['email'];
        $newBio = $_POST['bio'];

        if (strlen($newName) < 1){
            $output = false;
        }

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)){
            $output = false;
        }

        if (!$output){
            echo json_encode(false);
            die();
        }

        $updateQuery = 
                "UPDATE users SET Name = ?, Email = ?, Bio = ? WHERE Username = ?";
        $updateResult = Connection::query($updateQuery, "ssss", array($newName, $newEmail, $newBio, $currentUser->username));

        echo json_encode(true);
	}
}