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
        SELECT bugs.Object_ID AS Bug_Object_ID, bugs.Section_ID AS Bug_Section_ID, sections.Slug AS Section_Slug, comments.Username AS Comment_Author FROM comments
          JOIN bugs ON (comments.Bug_ID = bugs.Bug_ID)
          JOIN sections ON (bugs.Section_ID = sections.Section_ID)
        WHERE comments.Object_ID = ?
        ", "i", array($object_id));

        if (!count($comment)) return array("error" => "Invalid comment ID");

        if (!Objects::permission($object_id, "comment.edit", $_SESSION["username"], $comment[0]["Bug_Section_ID"]))
            return array("error" => "You do not have permission to perform that action");

        // Parse contents of the comment
        Library::get("parser");
        $value = Parser::parse($value, $comment[0]["Comment_Author"], array(
            "parent_object_id" => $comment[0]["Bug_Object_ID"],
            "current_object_id" => $object_id,
            "section_slug" => $comment[0]["Section_Slug"]
        ));

        Connection::query("UPDATE comments SET Comment_Text = ?, Raw_Text = ? WHERE comments.Object_ID = ?", "ssi",
            array($value, $_POST['value'], $object_id));


        return array("value" => $value);
    }
}