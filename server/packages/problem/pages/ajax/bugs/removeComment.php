<?php
class AjaxBugsRemoveCommentPage implements IPage {
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
        $object_id = $_GET['id'];

        $object = Connection::query("
        SELECT Object_Type FROM objects
          WHERE objects.Object_ID = ?
        ", "i", array($object_id));

        if ($object[0]["Object_Type"] === 1) {
            // delete a bug
            Library::get('objects');
            Objects::allow_rank($object_id, "bug.view", 3);

            $section = Connection::query("SELECT Slug FROM bugs
                                            JOIN sections ON (bugs.Section_ID = sections.Section_ID)
                                          WHERE bugs.Object_ID = ?", "i", array($object_id));

            return array("success" => true, "redirect" => Path::getclientfolder("bugs", $section[0]["Slug"]));
        } else {
            $comment = Connection::query("
        SELECT bugs.Section_ID AS Bug_Section_ID FROM comments
          JOIN bugs ON (comments.Bug_ID = bugs.Bug_ID)
          JOIN sections ON (bugs.Section_ID = sections.Section_ID)
        WHERE comments.Object_ID = ?
        ", "i", array($object_id));

            Library::get('objects');
            if (!Objects::permission($object_id, 'comment.remove', $_SESSION['username'], $comment[0]["Bug_Section_ID"]))
                return array("error" => "You do not have permission to perform this action");

            Connection::query("DELETE FROM comments WHERE Object_ID = ?", "i", array($object_id));
            Connection::query("DELETE FROM grouppermissions WHERE Object_ID = ?", "i", array($object_id));
            Connection::query("DELETE FROM userpermissions WHERE Object_ID = ?", "i", array($object_id));
            Connection::query("DELETE FROM objects WHERE Object_ID = ?", "i", array($object_id));
        }

        return array("success" => true);
    }
}