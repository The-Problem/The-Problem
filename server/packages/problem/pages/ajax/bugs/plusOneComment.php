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
        $id = $_POST['id'];
        $action = $_POST['action'];

        Library::get("objects");

        // fetch information on the bug and section to check permissions
        // the query needs to be able to work if the object is actually a bug or comment, so we use a
        // left join
        $section = Connection::query("SELECT bugs.Section_ID AS Section_ID FROM bugs
                                        LEFT JOIN comments ON (comments.Bug_ID = bugs.Bug_ID)
                                      WHERE comments.Object_ID = ?
                                         OR bugs.Object_ID = ?", "ii", array($id, $id));

        // make sure the user has the permission
        if (!Objects::permission($id, 'comment.upvote', $_SESSION["username"], $section[0]["Section_ID"])) return array("error" => "You do not have permission to perform that action");

        if ($action === 'downvote') {
            // if the user is downvoting, simply delete the entry from the database
            Connection::query("DELETE FROM plusones WHERE Object_ID = ? AND Username = ?", "is", array(
                $id, $_SESSION["username"]
            ));
        } else {
            $now = date("Y-m-d H:i:s");

            // insert the new entry into the database
            Connection::query("INSERT INTO plusones (Object_ID, Username, Time) VALUES (?, ?, ?)", "iss", array(
                $id, $_SESSION["username"], $now
            ));

            // notify watchers of the comment/bug with a notification
            Connection::query("INSERT INTO notifications
                                 (Triggered_By, Received_By,                    Target_One, Target_Two, Creation_Date, Type)
                          VALUES (           ?, (SELECT Username
                                                   FROM watchers
                                                 WHERE watchers.Object_ID = ?),          ?,       NULL,             ?,    4)",
                "siis", array(
                    $_SESSION["username"], $id, $id, $now
                ));

            // start watching the bug or comment
            try {
                Connection::query("INSERT INTO watchers (Object_ID, Username) VALUES (?, ?)", "is", array(
                    $id, $_SESSION["username"]
                ));
            } catch (Exception $ex) { }
        }

        return array("success" => true);
    }
}