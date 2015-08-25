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
        $bug = Connection::query("SELECT bugs.Object_ID AS Object_ID, bugs.Section_ID AS Section_ID, sections.Slug AS Section_Slug, Author FROM bugs
                                    JOIN sections ON (sections.Section_ID = bugs.Section_ID)
                                    WHERE Bug_ID = ?", "i", array($_POST['bug']));

        if (!count($bug)) return array("error" => "Invalid bug ID");

        Library::get('objects');
        //$can_comment = Objects::permission($bug["Object_ID"], "bug.comment", $_SESSION["username"], $bug["Section_ID"]);
        $can_comment = true;

        if (!$can_comment) return array("error" => "You do not have permission to perform that action");

        Connection::query("INSERT INTO objects (Object_Type) VALUES (?)", "i", array(2));
        $object_id = Connection::insertid();

        // find @mentions
        $value = $_POST['value'];

        $mentions = array();
        preg_match_all("/@([^@ ]+)/", $value, $mentions);

        $items = array_unique($mentions[1]);
        foreach ($items as $name) {
            $user = Connection::query("SELECT Username FROM users WHERE Username = ?", "s", array($name));
            if (count($user)) {
                Connection::query("INSERT INTO notifications
                                     (Triggered_By, Received_By, Target_One, Target_Two, Creation_Date, Type)
                              VALUES (           ?,           ?,          ?,          ?,             ?,    ?)", "ssiisi", array(
                    $_SESSION["username"], $name, $bug[0]["Object_ID"], $object_id, date('Y-m-d H:i:s'), 3
                ));

                $value = str_replace("@$name", "[@$name](" . Path::getclientfolder("~$name") . ")", $value);
            }
        }

        // find #bugs
        $bug_refs = array();
        preg_match_all("/([\\w-]*)#(\\d+)/", $value, $bug_refs);

        $section_names = $bug_refs[1];
        $bug_ids = $bug_refs[2];

        $bugs = array();
        foreach ($bug_ids as $k => $id) {
            $section_name = $section_names[$k];
            if (!strlen($section_name)) {
                $section_name = $bug[0]["Section_Slug"];
                array_push($bugs, array(strtolower($section_name), $id, "#$id"));
            }

            array_push($bugs, array(strtolower($section_name), $id, "$section_name#$id"));
        }
        $unique_bugs = array_unique($bugs);
        foreach ($unique_bugs as $bug) {
            $value = str_replace($bug[2], "[" . $bug[2] . "](" . Path::getclientfolder("bugs", $bug[0], $bug[1])  . ")", $value);
        }

        Library::get('parsedown');
        $parsedown = new Parsedown();
        $value = $parsedown->text($value);

        Connection::query("INSERT INTO comments (Bug_ID, Username, Object_ID, Creation_Date, Edit_Date, Comment_Text)
                                         VALUES (     ?,        ?,         ?,             ?,      NULL,            ?)",
            "isiss", array($_POST['bug'], $_SESSION["username"], $object_id, date("Y-m-d H:i:s"), $value));
        $comment_id = Connection::insertid();

        Objects::allow_user($object_id, "comment.edit", $_SESSION["username"]);
        Objects::allow_user($object_id, "comment.remove", $_SESSION["username"]);
        Objects::allow_rank($object_id, "comment.edit", 2);
        Objects::allow_rank($object_id, "comment.remove", 3);

        Connection::query("INSERT INTO notifications
                             (Triggered_By, Received_By,                    Target_One, Target_Two, Creation_Date, Type)
                      VALUES (           ?, (SELECT Username
                                               FROM watchers
                                             WHERE watchers.Object_ID = ?),          ?,          ?,             ?,    ?)",
            "siiisi", array(
                $_SESSION["username"], $bug[0]["Object_ID"], $object_id, $bug[0]["Object_ID"], date('Y-m-d H:i:s'), 3
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