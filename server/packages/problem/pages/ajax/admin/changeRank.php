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
        $current_user = $_SESSION["username"];
        if (!$current_user) return array("error" => "You do not have the required permission to perform this action");

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($current_user));
        $rank = $rank_res[0]["Rank"];
        if ($rank < 4) return array("error" => "You do not have the required permission to perform this action");

        $change_user = $_GET["username"];
        $new_rank = $_GET["rank"];
        if ($new_rank === 2) $new_rank = 1;

        Connection::query("UPDATE users SET Rank = ? WHERE Username = ?", "is", array($new_rank, $change_user));

        return array("success" => true);
    }
}