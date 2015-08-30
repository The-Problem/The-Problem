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

            // PROCESS BUG CONTENTS
            $value = $_POST['description'];

            // find @mentions
            $mentions = array();
            preg_match_all("/@([^@ ]+)/", $value, $mentions);

            $items = array_unique($mentions[1]);
            foreach ($items as $name) {
                $user = Connection::query("SELECT Username FROM users WHERE Username = ?", "s", array($name));
                if (count($user)) {
                    Connection::query("INSERT INTO notifications
                                     (Triggered_By, Received_By, Target_One, Target_Two, Creation_Date, Type)
                              VALUES (           ?,           ?,          ?,          ?,             ?,    ?)", "ssiisi", array(
                        $_SESSION["username"], $name, $this->section_obj, $object_id, date('Y-m-d H:i:s'), 3
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
                    $section_name = $this->section;
                    array_push($bugs, array(strtolower($section_name), $id, "#$id"));
                }

                array_push($bugs, array(strtolower($section_name), $id, "$section_name#$id"));
            }
            $unique_bugs = array_unique($bugs);
            foreach ($unique_bugs as $b) {
                $value = str_replace($b[2], "[" . $b[2] . "](" . Path::getclientfolder("bugs", $b[0], $b[1])  . ")", $value);
            }

            Library::get('parsedown');
            $parsedown = new Parsedown();
            $value = $parsedown->text($value);

            $rid = Connection::query("SELECT COUNT(*) + 1 AS New_RID FROM bugs WHERE bugs.Section_ID = ?", "i", array($this->section_id));

            Connection::query("INSERT INTO bugs (Section_ID, Object_ID, Name, Status, Description, Creation_Date, Author, RID)
                                         VALUES (         ?,         ?,    ?,      1,           ?,             ?,      ?, ?)",
                "iissssi", array(
                    $this->section_obj, $object_id, $_POST['name'], $value, date("Y-m-d H:i:s"), $_SESSION["username"], $rid[0]["New_RID"]
                ));

            Connection::query("INSERT INTO watchers (Object_ID, Username) VALUES (?, ?)", "is", array($object_id, $_SESSION["username"]));

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

            Objects::allow_rank($object_id, "bug.view", $default_view[0]["Value"]);
            Objects::allow_rank($object_id, "bug.change-status", $default_status[0]["Value"]);
            Objects::allow_rank($object_id, "comment.edit", $default_edit[0]["Value"]);
            Objects::allow_rank($object_id, "comment.delete", $default_delete[0]["Value"]);
            Objects::allow_rank($object_id, "bug.assign", $default_assign[0]["Value"]);
            Objects::allow_rank($object_id, "bug.comment", $default_comment[0]["Value"]);
            Objects::allow_rank($object_id, "comment.upvote", $default_upvote[0]["Value"]);

            Objects::allow_user($object_id, "bug.view", $_SESSION['username']);
            Objects::allow_user($object_id, "bug.change-status", $_SESSION["username"]);
            Objects::allow_user($object_id, "comment.edit", $_SESSION["username"]);
            Objects::allow_user($object_id, "comment.assign", $_SESSION["username"]);
            Objects::allow_user($object_id, "bug.comment", $_SESSION["username"]);
            Objects::allow_user($object_id, "comment.upvote", $_SESSION["username"]);

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