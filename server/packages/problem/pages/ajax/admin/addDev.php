<?php
class AjaxAdminAddDevPage implements IPage {
    public function __construct(PageInfo &$page) {

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
        // prevent changing anything without being logged in as an administrator
        $current_user = $_SESSION["username"];
        if (!$current_user) return array("error" => "You do not have the required permission to perform this action");

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($current_user));
        $rank = $rank_res[0]["Rank"];
        if ($rank < 4) return array("error" => "You do not have the required permission to perform this action");

        // fetch from the URL
        $dev_user = $_GET['username'];
        $dev_section = $_GET['section'];

        // insert into the developer table
        Connection::query("INSERT INTO developers (Section_ID, Username) VALUES (?, ?)", "is", array(
            $dev_section,
            $dev_user
        ));

        // start watching the section
        Connection::query("INSERT INTO watchers (Object_ID, Username)
                                         VALUES ((SELECT Object_ID FROM sections
                                                  WHERE Section_ID = ?), ?)", "is", array($dev_section, $dev_user));

        return array("success" => true);
    }
}