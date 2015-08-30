<?php
class AjaxAdminPermissionUserPage implements IPage {
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
        $current_user = $_SESSION["username"];
        if (!$current_user) return array("error" => "You do not have the required permission to perform this action");

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($current_user));
        $rank = $rank_res[0]["Rank"];
        if ($rank < 4) return array("error" => "You do not have the required permission to perform this action");

        $change_permission = $_POST["permission"];
        $change_object = $_POST["object"];
        $new_user = $_POST["username"];

        Library::get("objects");

        if ($_POST['remove']) Objects::deny_user($change_object, $change_permission, $new_user);
        else Objects::allow_user($change_object, $change_permission, $new_user);

        return array("success" => true);
    }
}