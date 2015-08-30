<?php
class AjaxUserLoginPage implements IPage {
    public function __construct(PageInfo &$info) {

    }

    public function template() {
        return Templates::findtemplate("ajax");
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) { }
    public function body() {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // validate the login information
        if (strlen($username) > 0 && strlen($password) > 0) {
            Library::get("users");
            $loggedin = Users::login($username, $password);

            if ($loggedin) {
                // capture the header bar HTML so that the client can add it to the HTML
                // without refreshing the page
                ob_start();
                Modules::getoutput("headerBar");
                $header = ob_get_flush();

                return array(
                    "header" => $header
                );

            } else return array("error" => array("message" => "These login credentials appear to be incorrect. Please try again."));
        } else return array("error" => array("message" => "Please enter a valid username/email and password."));
    }
}