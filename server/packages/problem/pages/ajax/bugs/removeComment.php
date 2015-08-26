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

        Library::get('objects');
        if (!Objects::permission($object_id, 'comment.remove', $_SESSION['username']))
            return array("error" => "You do not have permission to perform this action");

        Connection::query("DELETE FROM comments WHERE Object_ID = ?", "i", array($object_id));
        Connection::query("DELETE FROM grouppermissions WHERE Object_ID = ?", "i", array($object_id));
        Connection::query("DELETE FROM userpermissions WHERE Object_ID = ?", "i", array($object_id));
        Connection::query("DELETE FROM objects WHERE Object_ID = ?", "i", array($object_id));

        return array("success" => true);
    }
}