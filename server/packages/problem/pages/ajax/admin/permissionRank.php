<?php
class AjaxAdminPermissionRankPage implements IPage {
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

        // get the values from the URL
        $change_permission = $_GET["permission"];
        $change_object = $_GET["object"];
        $new_rank = $_GET["rank"];

        Library::get("objects");

        // use the object library to set the rank
        Objects::allow_rank($change_object, $change_permission, $new_rank);

        return array("success" => true);
    }
}