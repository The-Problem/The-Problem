<?php
class AjaxAdminDevSearchPage implements IPage {
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

    public function head(Head &$head) {
    }

    public function body() {
        $username = $_SESSION["username"];
        if (!$username) return array("error" => "You do not have the required permission to perform this action");

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];
        if ($rank < 4) return array("error" => "You do not have the required permission to perform this action");

        session_write_close();

        $query = $_GET['query'];
        $section_id = $_GET['section'];

        $devs = Connection::query("SELECT DISTINCT users.Username AS Username, Email FROM users
                                     LEFT JOIN developers ON (users.Username = developers.Username)
                                   WHERE COALESCE(developers.Section_ID, 0) != ? AND users.Username LIKE ?
                                   ORDER BY Username LIMIT 5", "is", array(
            $section_id, "%$query%"
        ));

        $items = array_map(function($dev) {
            $gravatar_id = md5(strtolower(trim($dev["Email"])));
            $gravatar = "http://www.gravatar.com/avatar/$gravatar_id?d=identicon&s=30";

            return '<tr><td class="user-image" style=\'background-image:url("' . $gravatar . '");"\'></td>' .
                   '<td class="user-name">' . htmlentities($dev["Username"]) . '</td></tr>';
        }, $devs);

        return $items;
    }

}