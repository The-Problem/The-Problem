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
        // prevent changing anything without being logged in as an administrator
        $username = $_SESSION["username"];
        if (!$username) return array("error" => "You do not have the required permission to perform this action");

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];
        if ($rank < 4) return array("error" => "You do not have the required permission to perform this action");

        // close the session to avoid hanging future queries, since the session blocks requests
        session_write_close();

        // get values from the URL
        $query = $_GET['query'];
        $section_id = $_GET['section'];

        // get a list of all users *not* a developer in the section specified, because you can't add
        // a user as a developer if they are already a developer
        $devs = Connection::query("SELECT * FROM users
  WHERE users.Username LIKE ?
  AND users.Username NOT IN (SELECT developers.Username
                                 FROM developers
                               WHERE developers.Section_ID = ?)
ORDER BY users.Username LIMIT 5", "si", array(
            "%$query%", $section_id
        ));

        // create the HTML to insert into the database
        $items = array_map(function($dev) {
            // create the user icon URL for gravatar
            $gravatar_id = md5(strtolower(trim($dev["Email"])));
            $gravatar = "http://www.gravatar.com/avatar/$gravatar_id?d=identicon&s=30";

            $username = htmlentities($dev["Username"]);

            return '<tr data-username="' . $username . '"><td class="user-image" style=\'background-image:url("' . $gravatar . '");"\'></td>' .
                   '<td class="user-name">' . $username . '</td></tr>';
        }, $devs);

        return $items;
    }

}