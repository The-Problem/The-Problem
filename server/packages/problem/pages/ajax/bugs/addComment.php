<?php
class AjaxBugsAddCommentPage implements IPage {
    private $comment;

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
        $bug = Connection::query("SELECT Object_ID, Section_ID, Author FROM bugs WHERE Bug_ID = ?", "i", array($_POST['bug']));
        if (!count($bug)) return array("error" => "Invalid bug ID");

        Library::get('objects');
        //$can_comment = Objects::permission($bug["Object_ID"], "bug.comment", $_SESSION["username"], $bug["Section_ID"]);
        $can_comment = true;

        if (!$can_comment) return array("error" => "You do not have permission to perform that action");

        Connection::query("INSERT INTO objects (Object_Type) VALUES (?)", "i", array(2));
        $object_id = Connection::insertid();

        Connection::query("INSERT INTO comments (Bug_ID, Username, Object_ID, Creation_Date, Edit_Date, Comment_Text)
                                         VALUES (     ?,        ?,         ?,             ?,      NULL,            ?)",
            "isiss", array($_POST['bug'], $_SESSION["username"], $object_id, date("Y-m-d H:i:s"), $_POST['value']));
        $comment_id = Connection::insertid();

        //Library::get('objects');
        //Objects::allow_rank($object_id, "bug.comment", 1);

        Connection::query("INSERT INTO notifications
                             (Triggered_By, Received_By,                    Target_One, Target_Two, Creation_Date, Type)
                      VALUES (           ?, (SELECT Username
                                               FROM watchers
                                             WHERE watchers.Object_ID = ?),          ?,          ?,             ?,    ?)",
            "siiisi", array(
                $_SESSION["username"], $bug[0]["Object_ID"], $object_id, $bug[0]["Object_ID"], date('Y-m-d H:i:s", 3'), 3
            ));

        // start watching the bug and comment
        Connection::query("INSERT INTO watchers (Object_ID, Username) VALUES (?, ?), (?, ?)", "isis",
            array($bug[0]["Object_ID"], $_SESSION["username"],
                  $object_id, $_SESSION["username"]));

        $comments = Connection::query("SELECT *, (SELECT COUNT(*) FROM developers
                                                  WHERE developers.Section_ID = ?
                                                  AND developers.Username = comments.Username) AS Is_Developer
                                         FROM comments
                                         JOIN objects ON (comments.Object_ID = objects.Object_ID)
                                         JOIN users ON (comments.Username = users.Username)
                                         WHERE comments.Comment_ID = ?", "ii", array($bug[0]["Section_ID"], $comment_id));

        $comment = $comments[0];
        $comment["Plus_Ones"] = 0;
        $comment["Object_ID"] = $object_id;
        $comment["My_Plus_Ones"] = 0;
        $comment["Bug_Author"] = $bug[0]["Author"];
        $this->comment = $comment;

        return array(
            "html" => String::getoutput(function() {
                Modules::getoutput("comment", $this->comment);
            })
        );
    }
}