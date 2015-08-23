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
        if (!$_SESSION["username"]) return array("error" => "Please login again");

        Connection::query("INSERT INTO objects (Object_Type) VALUES (?)", "i", array(2));
        $object_id = Connection::insertid();

        $bug = Connection::query("SELECT Section_ID, Author FROM bugs WHERE Bug_ID = ?", "i", array($_POST['bug']));
        if (!count($bug)) return array("error" => "Invalid bug ID");

        Connection::query("INSERT INTO comments (Bug_ID, Username, Object_ID, Creation_Date, Edit_Date, Comment_Text)
                                         VALUES (     ?,        ?,         ?,         NOW(),      NULL,            ?)",
            "isis", array($_POST['bug'], $_SESSION["username"], $object_id, $_POST['value']));
        $comment_id = Connection::insertid();

        $comments = Connection::query("SELECT *, (SELECT COUNT(*) FROM plusones
                                                  WHERE plusones.Object_ID = objects.Object_ID) AS Plus_Ones,
                                                 (SELECT COUNT(*) FROM developers
                                                  WHERE developers.Section_ID = ?
                                                  AND developers.Username = comments.Username) AS Is_Developer
                                         FROM comments
                                         JOIN objects ON (comments.Object_ID = objects.Object_ID)
                                         JOIN users ON (comments.Username = users.Username)
                                         WHERE comments.Comment_ID = ?", "ii", array($bug[0]["Section_ID"], $comment_id));

        $comment = $comments[0];
        $comment["Bug_Author"] = $bug[0]["Author"];
        $this->comment = $comment;

        return array(
            "html" => String::getoutput(function() {
                Modules::getoutput("comment", $this->comment);
            })
        );
    }
}