<?php
class BugsNewPage implements IPage {
    private $section;
    private $section_id;
    private $section_obj;

    public function __construct(PageInfo &$page) {
        $this->section = $page->pagelist[2];

        $section = Connection::query("SELECT Object_ID, Section_ID FROM sections WHERE Slug = ?", "s", array($this->section));
        $this->section_id = $section[0]["Section_ID"];
        $this->section_obj = $section[0]["Object_ID"];
    }

    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        Library::get("objects");
        return Objects::permission($this->section_obj, "section.create-bug", $_SESSION["username"]);
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
        if (array_key_exists("name", $_POST)) {
            // create the object
            Connection::query("INSERT INTO objects (Object_Type) VALUES (1)");
            $object_id = Connection::insertid();

            // Parse contents of the post
            Library::get("parser");
            $value = Parser::parse($_POST['description'], $_SESSION["username"], array(
                "parent_object_id" => $object_id,
                "current_object_id" => $object_id,
                "section_slug" => $this->section
            ));

            // calculate the new relative ID of the bug
            $rid = Connection::query("SELECT COUNT(*) + 1 AS New_RID FROM bugs WHERE bugs.Section_ID = ?", "i", array($this->section_id));

            // create the bug object
            Connection::query("INSERT INTO bugs (Section_ID, Object_ID, Name, Status, Description, Raw_Description, Creation_Date, Author, RID)
                                         VALUES (         ?,         ?,    ?,      1,           ?,               ?,              ?,      ?,  ?)",
                "iisssssi", array(
                    $this->section_id, $object_id, $_POST['name'], $value, $_POST['description'], date("Y-m-d H:i:s"), $_SESSION["username"], $rid[0]["New_RID"]
                ));

            // make the author of the bug follow the bug
            Connection::query("INSERT INTO watchers (Object_ID, Username) VALUES (?, ?)", "is", array($object_id, $_SESSION["username"]));

            // add a notification to followers of the section
            Connection::query("INSERT INTO notifications
                                 (Triggered_By, Received_By,                    Target_One, Target_Two, Creation_Date, Type)
                          VALUES (           ?, (SELECT Username
                                                   FROM watchers
                                                 WHERE watchers.Object_ID = ?),          ?,          ?,             ?,    1)",
                "siiis", array($_SESSION["username"], $this->section_obj, $object_id, $this->section_obj, date('Y-m-d H:i:s')));

            // get default permission values
            $default_view = Connection::query("SELECT Value FROM configuration
                                             WHERE Type = 'permissions-default-bugs'
                                             AND Name = 'view'");
            $default_status = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-bugs'
                                               AND Name = 'status'");
            $default_edit = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-bugs'
                                               AND Name = 'edit'");
            $default_delete = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-bugs'
                                               AND Name = 'delete'");
            $default_assign = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-bugs'
                                               AND Name = 'assigning'");
            $default_comment = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-comments'
                                               AND Name = 'create'");
            $default_upvote = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-comments'
                                               AND Name = 'upvote'");

            // set permission defaults for ranks
            Objects::allow_rank($object_id, "bug.view", $default_view[0]["Value"]);
            Objects::allow_rank($object_id, "bug.change-status", $default_status[0]["Value"]);
            Objects::allow_rank($object_id, "comment.edit", $default_edit[0]["Value"]);
            Objects::allow_rank($object_id, "comment.remove", $default_delete[0]["Value"]);
            Objects::allow_rank($object_id, "bug.assign", $default_assign[0]["Value"]);
            Objects::allow_rank($object_id, "bug.comment", $default_comment[0]["Value"]);
            Objects::allow_rank($object_id, "comment.upvote", $default_upvote[0]["Value"]);

            // set permissions for the author of the bug
            Objects::allow_user($object_id, "bug.view", $_SESSION['username']);
            Objects::allow_user($object_id, "bug.change-status", $_SESSION["username"]);
            Objects::allow_user($object_id, "comment.edit", $_SESSION["username"]);
            Objects::allow_user($object_id, "comment.assign", $_SESSION["username"]);
            Objects::allow_user($object_id, "bug.comment", $_SESSION["username"]);
            Objects::allow_user($object_id, "comment.upvote", $_SESSION["username"]);

            // redirect to the bug page
            Path::redirect(Path::getclientfolder("bugs", $this->section, $rid[0]["New_RID"]));
        }


        $head->script("lib/autosize.min");
    }

    public function body() {
        ?>
<h1>Create Bug</h1>
<form method="post">
    <h2><input type="text" name="name" placeholder="Bug Name" required /></h2>
    <textarea name="description" placeholder="Bug Description" required></textarea>
    <button>Create</button>
</form>
<?php
    }
}