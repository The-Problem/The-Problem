<?php
class AjaxBugsPlusOneCommentPage implements IPage {
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
        //if (is_null($_SESSION["username"])) return array("error" => "You do not have permission to perform that action");

        $id = $_POST['id'];
        $action = $_POST['action'];

        Library::get("objects");

        if (!Objects::permission($id, 'comment.upvote', $_SESSION["username"])) return array("error" => "You do not have permission to perform that action");

        if ($action === 'downvote') {
            Connection::query("DELETE FROM plusones WHERE Object_ID = ? AND Username = ?", "is", array(
                $id, $_SESSION["username"]
            ));
        } else {
            $now = date("Y-m-d H:i:s");

            Connection::query("INSERT INTO plusones (Object_ID, Username, Time) VALUES (?, ?, ?)", "iss", array(
                $id, $_SESSION["username"], $now
            ));

            // notify watchers
            Connection::query("INSERT INTO notifications
                                 (Triggered_By, Received_By,                    Target_One, Target_Two, Creation_Date, Type)
                          VALUES (           ?, (SELECT Username
                                                   FROM watchers
                                                 WHERE watchers.Object_ID = ?),          ?,       NULL,             ?,    ?)",
                "siisi", array(
                    $_SESSION["username"], $id, $id, $now, 4
                ));

            // start watching the bug
            try {
                Connection::query("INSERT INTO watchers (Object_ID, Username) VALUES (?, ?)", "is", array(
                    $id, $_SESSION["username"]
                ));
            } catch (Exception $ex) { }
        }

        return array("success" => true);
    }
}