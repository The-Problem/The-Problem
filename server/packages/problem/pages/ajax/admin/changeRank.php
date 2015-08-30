<?php
class AjaxAdminChangeRankPage implements IPage {
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

        // get values from the URL
        $change_user = $_GET["username"];
        $new_rank = $_GET["rank"];
        // setting the user to have a developer rank globally wouldn't make sense
        if ($new_rank === 2) $new_rank = 1;

        // users shouldn't be able to change their own rank to be below admin, because that would
        // mean they couldn't access the admin page anymore to change their rank
        if ($change_user === $current_user && $new_rank < 4) return array("error" => "You can't change your own rank");

        // actually do the update
        Connection::query("UPDATE users SET Rank = ? WHERE Username = ?", "is", array($new_rank, $change_user));

        return array("success" => true);
    }
}