<?php
class HeaderBarModule implements IModule {
    public $headers = array();

    private $username;
    private $user;

    public function __construct() {
        $username = $_SESSION["username"];
        $this->username = $username;

        // add some header buttons if the user is logged in
        if ($username) {
            $user = Connection::query("SELECT Name, Rank FROM users WHERE Username = ?", "s", array($username));
            $this->user = $user;

            // admin button for if the user is an administrator
            if ($user[0]["Rank"] >= 4) array_push($this->headers, array("cogs", Path::getclientfolder("admin"), "Admin Control Panel", ""));
            array_push($this->headers, array("sign-out", Path::getclientfolder("ajax", "user", "logout") . "?return=" . urlencode($_SERVER['REQUEST_URI']), "Logout", ""));
            array_push($this->headers, array("bell", "javascript:void(0);", "Notifications", "notificationButton"));
        }
    }

    public function spinnersize() { return Modules::SPINNER_SMALL; }

    public function getcode($params = array(), Head $h) {
        echo '<div class="right">';

        // display the "Hi" text
        if ($this->username) {
            echo '<span class="user">Hi, <a href="' . Path::getclientfolder("~" . htmlentities($this->username)) . '">' . htmlentities($this->user[0]["Name"]) . '</a></span>';
        }

        // show the buttons
        echo '<span class="buttons">';
        foreach ($this->headers as $header) {
            echo "<a id='$header[3]' title='" . htmlentities($header[2]) . "' class='header-button fa fa-$header[0]' href='" . htmlentities($header[1]) . "'></a>";
        }
        echo '</span></div>';
    }

    public function getsurround($code, $params) {
        return $code;
    }
}