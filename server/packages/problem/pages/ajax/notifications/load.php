<?php
//returns json object from notification library with requested notifications
class AjaxNotificationsLoadPage implements IPage {
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

        Library::get('notifications');

        $requestSettings = array(
                'time' => $_GET['time'],
                'limit' => $_GET['limit'],
                'before' => $_GET['before']
            );

		$newNotifications = Notifications::get($requestSettings);

		echo json_encode($newNotifications);
	}
}