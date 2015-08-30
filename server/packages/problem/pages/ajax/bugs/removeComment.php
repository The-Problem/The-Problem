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

        // get the object type to see if it is actually a bug
        $object = Connection::query("
        SELECT Object_Type FROM objects
          WHERE objects.Object_ID = ?
        ", "i", array($object_id));

        // get some info on the comment
        $comment = Connection::query("
        SELECT bugs.Section_ID AS Bug_Section_ID FROM comments
          JOIN bugs ON (comments.Bug_ID = bugs.Bug_ID)
          JOIN sections ON (bugs.Section_ID = sections.Section_ID)
        WHERE comments.Object_ID = ?
        ", "i", array($object_id));

        Library::get('objects');

        // make sure we are allowed to do this
        if (!Objects::permission($object_id, 'comment.remove', $_SESSION['username'], $comment[0]["Bug_Section_ID"]))
            return array("error" => "You do not have permission to perform this action");

        if ($object[0]["Object_Type"] === 1) {
            // delete a bug
            Library::get('objects');

            // prevent all ranks except moderator and above viewing the deleted comment
            Objects::allow_rank($object_id, "bug.view", 3);

            // set the bug to the DELETED status
            Connection::query("UPDATE bugs SET Status = 0 WHERE Object_ID = ?", "i", array($object_id));

            // get some data to redirect
            $section = Connection::query("SELECT Slug, Author, RID FROM bugs
                                            JOIN sections ON (bugs.Section_ID = sections.Section_ID)
                                          WHERE bugs.Object_ID = ?", "i", array($object_id));

            // prevent the author of the bug viewing the deleted comment
            Objects::deny_user($object_id, "bug.view", $section[0]["Author"]);

            // tell the client to redirect out of the page
            return array("success" => true, "redirect" => Path::getclientfolder("bugs", $section[0]["Slug"], $section[0]["RID"]));
        } else {
            // delete all information from the comment
            Connection::query("DELETE FROM comments WHERE Object_ID = ?", "i", array($object_id));
            Connection::query("DELETE FROM grouppermissions WHERE Object_ID = ?", "i", array($object_id));
            Connection::query("DELETE FROM userpermissions WHERE Object_ID = ?", "i", array($object_id));
            Connection::query("DELETE FROM objects WHERE Object_ID = ?", "i", array($object_id));
        }

        return array("success" => true);
    }
}