<?php
class AdminPage implements IPage {
    private $pages = array(
        "overview" => "<span><i class='fa fa-cogs'></i></span>Overview",
        "sections" => "<span><i class='fa fa-database'></i></span>Sections",
        "users" => "<span><i class='fa fa-users'></i></span>Users",
        "permissions" => "<span><i class='fa fa-bolt'></i></span>Permissions"
    );

    private $page = "overview";

    public function __construct(PageInfo &$page) {
        $path = $page->pagelist;
        if (count($path) > 1) $this->page = $path[1];
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
        return true;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
        if (!$_SESSION['sudo']) Path::redirect(Path::getclientfolder("sudo") . "?return=" . urlencode($_SERVER['REQUEST_URI']));

        $head->title .= " - Admin";
        $head->stylesheet("pages/admin");
        $head->script("pages/admin");
    }

    public function overview() {
        $data = Connection::query("SELECT Type, Name, Value FROM configuration WHERE Type = 'overview-visibility' OR Type = 'overview-name'");

        $sitename = "The Problem";
        $visibility = "public";
        $registration = "open";

        foreach ($data as $item) {
            if ($item["Type"] === "overview-name") {
                if ($item["Name"] === "sitename") $sitename = $item["Value"];
            } else if ($item["Type"] === "overview-visibility") {
                if ($item["Name"] === "visibility") $visibility = $item["Value"];
                else if ($item["Name"] === "registration") $registration = $item["Value"];
            }
        }

        $isPublic = $visibility === "public";
        $isOpen = $registration === "open";

        ?>
        <section data-type="overview-name">
            <h2>Site Name</h2>
            <input type="text" placeholder="The Problem" name="sitename" value="<?php echo $sitename; ?>" />
            <p class="help">The site name is displayed on the homepage, title bar, and various places throughout the
            site.</p>
            <p class="tip">Set the site name to the product or company that the site is being used for.</p>
        </section><section class="site-visibility" data-type="overview-visibility">
            <h2>Site Visibility</h2>
            <div class="columns">
                <div class="column">
                    <label><input type="radio" name="visibility" value="public" <?php if ($isPublic) echo 'checked'; ?> />Public</label><br>
                    <label><input type="radio" name="visibility" value="private" <?php if (!$isPublic) echo 'checked'; ?> />Private</label>
                </div>
                <div class="column registration">
                    <label><input type="radio"
                                  name="registration"
                                  value="open"
                                  <?php if ($isOpen) echo 'checked'; ?>
                                  <?php if (!$isPublic) echo 'disabled'; ?> />Open registration</label><br>
                    <label><input type="radio"
                                  name="registration"
                                  value="closed"
                                  <?php if (!$isOpen) echo 'checked'; ?>
                                  <?php if (!$isPublic) echo 'disabled'; ?> />Closed registration</label>
                </div>
            </div>
            <p class="help">Site visibility allows you to control who can view your website. By setting the visibility
            to private, registrations will require confirmation from an administrator or moderator, and sections will
            only be viewable by registered users. Public allows anyone to view or register for the site.</p>
            <p class="tip">If the site is being used as an internal bug tracker but will be exposed to the Internet,
            set this option to Private.</p>
        </section>
        <?php
    }
    public function sections() {
        Pages::$head->script("lib/autosize.min");

        $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status != 1) AS Closed_Bugs,
          (SELECT COUNT(*) FROM developers
           WHERE developers.Section_ID = sections.Section_ID) AS Developers
FROM sections
  ORDER BY Open_Bugs DESC, Open_Bugs + Closed_Bugs DESC");

        ?>

<table class="username-selector"></table>
<div class="section-list list-table">
    <div class="table-search">
        <input type="text" placeholder="Search sections..." />
    </div>
    <div class="table-header">
        <p class="name">Name</p><!--
        --><p class="description">Description</p><!--
        --><p class="developers">Devs</p><!--
        --><p class="bugs">Bugs</p>
    </div>

    <?php
    $last_highlighted = false;

    foreach ($sections as $section) {
        $total = $section["Closed_Bugs"] + $section["Open_Bugs"];
        if ($total > 0) $percentage = $section["Closed_Bugs"] / $total;
        else $percentage = 1.1;

        $is_highlight = !$section["Developers"] || $percentage < 0.1;

        ?>
        <div data-id="<?php echo $section["Section_ID"]; ?>" data-search="<?php echo htmlentities(strtolower($section["Name"])); ?>"
             class="table-row<?php if ($is_highlight) echo ' highlight'; if($last_highlighted) echo ' top-highlight'; ?>">
            <div class="overview">
                <p class="name"><?php echo htmlentities($section["Name"]); ?></p><!--
                --><p class="description"><?php echo htmlentities($section["Description"]); ?></p><!--
                --><p class="developers<?php if (!$section["Developers"]) echo ' highlight'; ?>"><?php echo $section["Developers"]; ?></p><!--
                --><p class="bugs">
                    <?php if ($total === 0) { ?><em class="none">No bugs</em><?php } else { ?>
                        <?php echo $section["Open_Bugs"]; ?> <i class="fa fa-check"></i> - <?php echo $section["Closed_Bugs"]; ?> <i class="fa fa-times"></i> (<?php echo floor($percentage * 100); ?>%)
                    <?php } ?>
                </p>
            </div><div class="options" style="display:none"></div>
        </div>
        <?php

        $last_highlighted = false;
        if ($is_highlight) $last_highlighted = true;
    }
    ?>
</div>
<button class="green add-section"><i class="fa fa-plus"></i> Add Section</button>
        <?php
    }
    public function users() {
        $ranks = array(
            0 => "Unverified",
            1 => "Standard",
            3 => "Moderator",
            4 => "Administrator"
        );

        $users = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Author = users.Username
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Author = users.Username
           AND bugs.Status != 1) AS Closed_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Assigned = users.Username
           AND bugs.Status = 1) AS Open_Assigned_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Assigned = users.Username
           AND bugs.Status != 1) AS Closed_Assigned_Bugs,
          (SELECT GROUP_CONCAT(DISTINCT sections.Name SEPARATOR ', ') FROM developers
             JOIN sections ON (sections.Section_ID = developers.Section_ID)
           WHERE developers.Username = users.Username
           GROUP BY users.Username) AS Developing
FROM users
  ORDER BY Username ASC");
        ?>
<div class="user-list list-table">
    <div class="table-search">
        <input type="text" placeholder="Search users..." />
    </div>
    <div class="table-header">
        <p class="username">Username</p><!--
        --><p class="rank">Rank</p><!--
        --><p class="last-logon">Last Login</p><!--
        --><p class="my-bugs">My Bugs</p><!--
        --><p class="assigned-bugs">Assigned to</p><!--
        --><p class="developing">Develops</p>
    </div>
    <?php
        foreach ($users as $user) {
            $total_bugs = $user["Closed_Bugs"] + $user["Open_Bugs"];
            if ($total_bugs > 0) $bug_percentage = $user["Closed_Bugs"] / $total_bugs;
            else $bug_percentage = 1.1;

            $total_assigned = $user["Closed_Assigned_Bugs"] + $user["Open_Assigned_Bugs"];
            if ($total_assigned > 0) $assigned_percentage = $user["Closed_Assigned_Bugs"] / $total_assigned;
            else $assigned_percentage = 1.1;

            $lastlogon = new DateTime($user["Last_Logon_Time"]);

            $username = htmlentities($user["Username"]);

            ?>
            <div data-username="<?php echo $user["Username"]; ?>" data-search="<?php echo htmlentities(strtolower($user["Username"])); ?>" class="table-row">
                <div class="overview">
                    <p class="username"><a href="<?php echo Path::getclientfolder("~$username"); ?>" title="View <?php echo $username; ?>'s profile"><?php echo $username; ?></a></p><!--
                    --><p class="rank">
                        <label><select class="<?php echo strtolower($ranks[$user["Rank"]]); ?>">
                            <?php foreach ($ranks as $number => $rank) {
                                echo "<option value='$number'" . ($number === $user["Rank"] ? "selected" : "") . " class='" . strtolower($rank) . "'>$rank</option>";
                            } ?>
                        </select></label>
                    </p><!--
                    --><p class="last-logon">
                        <?php if ($user["Last_Logon_Time"]) { ?><span class="timeago" title="<?php echo $lastlogon->format("c"); ?>"></span><?php }
                        else { ?><em class="none">Never</em><?php } ?>
                    </p><!--
                    --><p class="my-bugs">
                        <?php if ($total_bugs === 0) { ?><em class="none">No bugs</em><?php } else {
                            echo $user["Open_Bugs"]; ?> <i class="fa fa-check"></i> - <?php echo $user["Closed_Bugs"]; ?> <i class="fa fa-times"></i> (<?php echo floor($bug_percentage * 100); ?>%)
                        <?php } ?>
                    </p><!--
                    --><p class="assigned-bugs">
                        <?php if ($total_assigned === 0) { ?><em class="none">No bugs</em><?php } else {
                            echo $user["Open_Assigned_Bugs"]; ?> <i class="fa fa-check"></i> - <?php echo $user["Closed_Assigned_Bugs"]; ?> <i class="fa fa-times"></i> (<?php echo floor($assigned_percentage * 100); ?>%)
                        <?php } ?>
                    </p><!--
                    --><p class="developing">
                        <?php if (strlen($user["Developing"])) echo htmlentities($user["Developing"]);
                              else echo "<em class='none'>None</em>"; ?>
                    </p>
                </div>
            </div>
            <?php
        }
    ?>
</div>
        <?php
    }


    public function permissions() {
        $data = Connection::query("SELECT Type, Name, Value FROM configuration
                                    WHERE Type = 'permissions-default-section'
                                       OR Type = 'permissions-default-bugs'
                                       OR Type = 'permissions-default-comments'");

        $defaults = array();
        foreach ($data as $item) {
            $type = $item["Type"];
            $name = $item["Name"];
            $value = $item["Value"];

            if (!array_key_exists($type, $defaults)) $defaults[$type] = array();
            $defaults[$type][$name] = $value;
        }
        function options($defaults, $type, $name, $guest = true) {
            $value = $defaults[$type][$name];

            $arr = array(
                "<option value='1'" . ($value == 1 ? ' selected' : '') . ">Normal user</option>",
                "<option value='2'" . ($value == 2 ? ' selected' : '') . ">Section developer</option>",
                "<option value='3'" . ($value == 3 ? ' selected' : '') . ">Moderator</option>",
                "<option value='4'" . ($value == 4 ? ' selected' : '') . ">Administrator</option>"
            );
            if ($guest) {
                array_unshift($arr, "<option value='0'" . ($value == 0 ? ' selected' : '') . ">Guest/unverified</option>");
            }

            return implode("\n", $arr);
        }

        ?>
        <div class="permission-defaults">
        <section data-type="permissions-default-section">
            <h2>Section Defaults</h2>
            <table>
                <tr>
                    <th><label for="permissions-default-sections-view">Viewing sections:</label></th>
                    <td><select name="view" id="permissions-default-sections-view"><?php echo options($defaults, "permissions-default-section", "view"); ?></select></td>
                </tr>
            </table>
            <p class="help">Changing the default section permissions will change the permissions on all sections
            that use the default values.</p>
            <p class="tip">You can change permissions for individual sections below.</p>
        </section>
        <section data-type="permissions-default-bugs">
            <h2>Bug Defaults</h2>
            <table>
                <tr>
                    <th><label for="permissions-default-bugs-view">Viewing bugs:</label></th>
                    <td><select name="view" id="permissions-default-bugs-view"><?php echo options($defaults, "permissions-default-bugs", "view"); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-bugs-create">Creating bugs:</label></th>
                    <td><select name="create" id="permissions-default-bugs-create"><?php echo options($defaults, "permissions-default-bugs", "create", false); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-bugs-edit">Editing bugs:</label></th>
                    <td><select name="edit" id="permissions-default-bugs-edit"><?php echo options($defaults, "permissions-default-bugs", "edit"); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-bugs-delete">Deleting bugs:</label></th>
                    <td><select name="delete" id="permissions-default-bugs-delete"><?php echo options($defaults, "permissions-default-bugs", "delete"); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-bugs-status">Changing bugs status:</label></th>
                    <td><select name="status" id="permissions-default-bugs-status"><?php echo options($defaults, "permissions-default-bugs", "status"); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-bugs-assigning">Assigning a user:</label></th>
                    <td><select name="assigning" id="permissions-default-bugs-assigning"><?php echo options($defaults, "permissions-default-bugs", "assigning"); ?></select></td>
                </tr>
            </table>
            <p class="help">Changing the default bug permissions will change the permissions on all bugs
                that use the default values.</p>
            <p class="tip">You can change permissions for individual bugs below.</p>
        </section>
        <section data-type="permissions-default-comments">
            <h2>Comment Defaults</h2>
            <table>
                <tr>
                    <th><label for="permissions-default-comments-create">Creating comments:</label></th>
                    <td><select name="create" id="permissions-default-comments-create"><?php echo options($defaults, "permissions-default-comments", "create", false); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-comments-upvote">Upvoting comments:</label></th>
                    <td><select name="upvote" id="permissions-default-comments-upvote"><?php echo options($defaults, "permissions-default-comments", "upvote", false); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-comments-edit">Editing comments:</label></th>
                    <td><select name="edit" id="permissions-default-comments-edit"><?php echo options($defaults, "permissions-default-comments", "edit"); ?></select></td>
                </tr>
                <tr>
                    <th><label for="permissions-default-comments-delete">Deleting comments:</label></th>
                    <td><select name="delete" id="permissions-default-comments-delete"><?php echo options($defaults, "permissions-default-comments", "delete"); ?></select></td>
                </tr>
            </table>
            <p class="help">Changing the default comment permissions will change the permissions on all comments
                that use the default values.</p>
            <p class="tip">You can change permissions for individual comments below.</p>
        </section>
        </div>
        <div class="section-permissions">
            <?php
            $section_permissions = array(
                "section.view" => "Viewing the section"
            );
            $first_section = "section.view";

            $bug_permissions = array(
                "bug.view" => "Viewing the bug",
                "bug.comment" => "Commenting on the bug",
                "comment.edit" => "Editing the bug",
                "comment.remove" => "Removing the bug",
                "comment.upvote" => "Upvoting the bug",
                "bug.assign" => "Changing the assigned user",
                "bug.change-status" => "Changing the status"
            );
            $first_bug = "bug.view";

            $comment_permissions = array(
                "comment.edit" => "Editing the comment",
                "comment.remove" => "Removing the comment",
                "comment.upvote" => "Upvoting the comment"
            );
            $first_comment = "comment.edit";

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

            $sections = Connection::query("SELECT Name, Slug, Object_ID FROM sections");
            $bugs = Connection::query("SELECT bugs.Name AS Name, bugs.Object_ID AS Object_ID, sections.Slug AS Section_Slug, RID FROM bugs
                                         JOIN sections ON (sections.Section_ID = bugs.Section_ID)");
            $comments = Connection::query("SELECT comments.Comment_Text AS Name, comments.Object_ID AS Object_ID, sections.Slug AS Section_Slug, RID FROM comments
                                             JOIN bugs ON (comments.Bug_ID = bugs.Bug_ID)
                                             JOIN sections ON (sections.Section_ID = bugs.Section_ID)");

            ?>
            <h2>Section Permissions</h2>
            <div class="permission-list section-permission-list list-table">
                <div class="table-search">
                    <input type="text" placeholder="Search sections..." />
                </div>
                <div class="table-header">
                    <p class="name">Name</p><!--
                    --><p class="permission">Permission</p><!--
                    --><p class="rank">Rank</p>
                </div>
                <?php
                    foreach ($sections as $section) {
                        $permissions = Connection::query("SELECT Permission_Name, Rank FROM grouppermissions
                                                            WHERE Object_ID = ?", "i", array($section["Object_ID"]));

                        $assoc_permissions = array();
                        foreach ($permissions as $p) {
                            $assoc_permissions[$p["Permission_Name"]] = $p["Rank"];
                        }

                        foreach ($section_permissions as $name => $desc) {
                            $rank = 5;
                            if (array_key_exists($name, $assoc_permissions)) $rank = $assoc_permissions[$name];

                            ?>
                            <div class="table-row<?php if ($name == $first_section) echo ' first-item'; ?>"
                                 data-search="<?php echo htmlentities(strtolower($section["Name"] . " " . $desc)); ?>"
                                 data-object="<?php echo $section["Object_ID"]; ?>"
                                 data-permission="<?php echo $name; ?>">
                                <div class="overview">
                                    <p class="name">
                                        <a href="<?php echo Path::getclientfolder('bugs', $section["Slug"]); ?>"><?php echo htmlentities($section["Name"]); ?></a>
                                    </p><!--
                                    --><p class="permission"><?php echo $desc; ?></p><!--
                                    --><p class="rank">
                                        <label><select>
                                            <?php echo get_options($ranks, $rank); ?>
                                        </select></label>
                                    </p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                ?>
            </div>



            <h2>Bug Permissions</h2>
            <div class="permission-list bug-permission-list list-table">
                <div class="table-search">
                    <input type="text" placeholder="Search bugs..." />
                </div>
                <div class="table-header">
                    <p class="name">Name</p><!--
                    --><p class="permission">Permission</p><!--
                    --><p class="rank">Rank</p>
                </div>
                <?php
                foreach ($bugs as $comment) {
                    $permissions = Connection::query("SELECT Permission_Name, Rank FROM grouppermissions
                                                            WHERE Object_ID = ?", "i", array($comment["Object_ID"]));

                    $assoc_permissions = array();
                    foreach ($permissions as $p) {
                        $assoc_permissions[$p["Permission_Name"]] = $p["Rank"];
                    }

                    foreach ($bug_permissions as $name => $desc) {
                        $rank = 5;
                        if (array_key_exists($name, $assoc_permissions)) $rank = $assoc_permissions[$name];

                        ?>
                        <div class="table-row<?php if ($name == $first_bug) echo ' first-item'; ?>"
                             data-search="<?php echo htmlentities(strtolower($comment["Name"] . " " . $desc)); ?>"
                             data-object="<?php echo $comment["Object_ID"]; ?>"
                             data-permission="<?php echo $name; ?>"
                            >
                            <div class="overview">
                                <p class="name">
                                    <a href="<?php echo Path::getclientfolder('bugs', $comment["Section_Slug"], $comment["RID"]); ?>"><?php echo htmlentities($comment["Name"]); ?></a>
                                </p><!--
                                    --><p class="permission"><?php echo $desc; ?></p><!--
                                    --><p class="rank">
                                    <label><select>
                                            <?php echo get_options($ranks, $rank); ?>
                                        </select></label>
                                </p>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>



            <h2>Comment Permissions</h2>
            <div class="permission-list comment-permission-list list-table">
                <div class="table-search">
                    <input type="text" placeholder="Search bugs..." />
                </div>
                <div class="table-header">
                    <p class="name">Name</p><!--
                    --><p class="permission">Permission</p><!--
                    --><p class="rank">Rank</p>
                </div>
                <?php
                foreach ($comments as $comment) {
                    $permissions = Connection::query("SELECT Permission_Name, Rank FROM grouppermissions
                                                            WHERE Object_ID = ?", "i", array($comment["Object_ID"]));

                    $assoc_permissions = array();
                    foreach ($permissions as $p) {
                        $assoc_permissions[$p["Permission_Name"]] = $p["Rank"];
                    }

                    foreach ($comment_permissions as $name => $desc) {
                        $rank = 5;
                        if (array_key_exists($name, $assoc_permissions)) $rank = $assoc_permissions[$name];

                        ?>
                        <div class="table-row<?php if ($name == $first_comment) echo ' first-item'; ?>"
                             data-search="<?php echo htmlentities(strtolower($comment["Name"] . " " . $desc)); ?>"
                             data-object="<?php echo $comment["Object_ID"]; ?>"
                             data-permission="<?php echo $name; ?>"
                            >
                            <div class="overview">
                                <p class="name">
                                    <a href="<?php echo Path::getclientfolder('bugs', $comment["Section_Slug"], $comment["RID"]); ?>"><?php echo htmlentities(strip_tags($comment["Name"])); ?></a>
                                </p><!--
                                    --><p class="permission"><?php echo $desc; ?></p><!--
                                    --><p class="rank">
                                    <label><select>
                                            <?php echo get_options($ranks, $rank); ?>
                                        </select></label>
                                </p>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function body() {
        ?>
<h1>Admin Control Panel</h1>
<div class="container">
    <div class="panel">
        <?php
        switch ($this->page) {
            case 'sections': $this->sections(); break;
            case 'permissions': $this->permissions(); break;
            case 'users': $this->users(); break;
            default: $this->overview(); break;
        }
        ?>
    </div><nav>
        <?php
        end($this->pages);
        $last = key($this->pages);

        foreach ($this->pages as $path => $name) {
            ?>
        <a class="item<?php if ($path === $last) echo ' last';
                            if ($path === $this->page) echo ' selected'; ?>" href="<?php echo Path::getclientfolder('admin', $path);
        ?>" title="<?php echo trim(strip_tags($name)); ?>"><?php echo $name; ?></a>
            <?php
        }
        ?>
        <a class="return" href="<?php echo Path::getclientfolder(); ?>" title="Back to site">Back to site</a>
    </nav>
</div>
<?php
    }
}