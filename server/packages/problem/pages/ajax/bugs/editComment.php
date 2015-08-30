<?php
class AjaxBugsEditCommentPage implements IPage {
    public function __construct(pageInfo &$page) {
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
        $object_id = $_POST['id'];
        $value = $_POST['value'];

        Library::get('objects');

        $comment = Connection::query("
        SELECT bugs.Object_ID AS Bug_Object_ID, bugs.Section_ID AS Bug_Section_ID, sections.Slug AS Section_Slug FROM comments
          JOIN bugs ON (comments.Bug_ID = bugs.Bug_ID)
          JOIN sections ON (bugs.Section_ID = sections.Section_ID)
        WHERE comments.Object_ID = ?
        ", "i", array($object_id));

        if (!count($comment)) return array("error" => "Invalid comment ID");

        if (!Objects::permission($object_id, "comment.edit", $_SESSION["username"], $comment[0]["Bug_Section_ID"]))
            return array("error" => "You do not have permission to perform that action");

        $mentions = array();
        preg_match_all("/@([^@ ]+)/", $value, $mentions);

        $items = array_unique($mentions[1]);
        foreach ($items as $name) {
            $user = Connection::query("SELECT Username FROM users WHERE Username = ?", "s", array($name));
            if (count($user)) {
                Connection::query("INSERT INTO notifications
                                     (Triggered_By, Received_By, Target_One, Target_Two, Creation_Date, Type)
                              VALUES (           ?,           ?,          ?,          ?,             ?,    ?)", "ssiisi", array(
                    $_SESSION["username"], $name, $comment[0]["Object_ID"], $object_id, date('Y-m-d H:i:s'), 3
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
                $section_name = $comment[0]["Section_Slug"];
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

        Connection::query("UPDATE comments SET Comment_Text = ?, Raw_Text = ? WHERE comments.Object_ID = ?", "ssi",
            array($value, $_POST['value'], $object_id));


        return array("value" => $value);
    }
}