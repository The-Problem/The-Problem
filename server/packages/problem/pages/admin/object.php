<?php
class AdminObjectPage implements IPage {
    private $object_id;
    private $object_type;

    public function __construct(PageInfo &$page) {
        $this->object_id = $page->pagelist[2];
    }

    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        $username = $_SESSION["username"];
        if (!$username) return false;

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];

        if ($rank < 4) return false;

        $object = Connection::query("SELECT Object_Type FROM objects WHERE Object_ID = ?", "i", array($this->object_id));
        if (count($object)) {
            $this->object_type = $object[0]["Object_Type"];
            return true;
        }
        return false;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
        $head->title .= " - Admin";
        $head->stylesheet("pages/adminObject");
        $head->script("pages/adminObject");
    }

    public function body() {
        $type_str = array(
            0 => "Section",
            1 => "Bug",
            2 => "Comment"
        );

        $ranks = array(
            0 => "Guest/unverified",
            1 => "Normal user",
            2 => "Section developer",
            3 => "Moderator",
            4 => "Administrator"
        );

        function get_options($ranks, $rank) {
            $result = "";

            foreach ($ranks as $r => $text) {
                $result .= '<option value="' . $r . '"' . ($r === $rank ? " selected" : "") . '>' . $text . '</option>';
            }

            return $result;
        }

        $permissions = array(
            0 => array(
                "section.view" => "view the section",
                "section.create-bug" => "create a bug in the section"
            ),
            1 => array(
                "bug.view" => "view the bug",
                "bug.comment" => "comment on the bug",
                "comment.edit" => "edit the bug",
                "comment.remove" => "remove the bug",
                "comment.upvote" => "upvote the bug",
                "bug.assign" => "assign a user to the bug",
                "bug.change-status" => "change the status of the bug"
            ),
            2 => array(
                "comment.edit" => "edit the comment",
                "comment.remove" => "remove the comment",
                "comment.upvote" => "upvote the comment"
            )
        );

        echo "<h1>" . $type_str[$this->object_type] . " Permissions</h1>";

        $permission_names = $permissions[$this->object_type];
        $group_permissions = Connection::query("SELECT Permission_Name, Rank FROM grouppermissions WHERE Object_ID = ?",
            "i", array($this->object_id));

        ?><table class="username-selector"></table><?php

        foreach ($group_permissions as $permission) {
            $perm_name = $permission["Permission_Name"];

            $perm_rank = $permission["Rank"];
            $description = $permission_names[$perm_name];

            $users = Connection::query("SELECT users.Username AS Username, Email FROM userpermissions
                                          JOIN users ON (userpermissions.Username = users.Username)
                                          WHERE Object_ID = ?
                                            AND Permission_Name = ?", "is", array($this->object_id, $perm_name));

            ?>
<section data-object="<?php echo $this->object_id; ?>" data-permission="<?php echo $perm_name; ?>">
    <h3>Who can <?php echo $description; ?>?</h3>
    <div class="columns">
        <div class="left-column">
            <p><label><em>Users ranked this or above:</em><br>
            <select>
                <?php echo get_options($ranks, $perm_rank); ?>
            </select></label></p>
        </div><div class="right-column">
            <p><em>And the following users:</em></p>
            <table class="user-list">
                <?php
                foreach ($users as $user) {
                    $gravatar_id = md5(strtolower(trim($user["Email"])));
                    $gravatar = "http://www.gravatar.com/avatar/$gravatar_id?d=identicon&s=30";

                    ?>
                    <tr data-username="<?php echo htmlentities($user["Username"]); ?>">
                        <td class="user-image" style='background-image:url("<?php echo $gravatar; ?>");'></td>
                        <td class="user-name"><?php echo htmlentities($user["Username"]); ?></td>
                        <td class="user-remove"><a href="javascript:void(0)" title="Remove developer"><i class="fa fa-times"></i></a></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <input class="add-user" placeholder="Add a user...">
        </div>
    </div>
</section>
<?php
        }
    }
}