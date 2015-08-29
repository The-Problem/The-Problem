<?php
class AdminPage implements IPage {
    private $pages = array(
        "overview" => "<i class='fa fa-cogs'></i>Overview",
        "sections" => "<i class='fa fa-database'></i>Sections",
        "users" => "<i class='fa fa-users'></i>Users",
        "permissions" => "<i class='fa fa-bolt'></i>Permissions"
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
        Pages::$head->script("lib/jquery.timeago");

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
        ?>" title="<?php echo $name; ?>"><?php echo $name; ?></a>
            <?php
        }
        ?>
        <a class="return" href="<?php echo Path::getclientfolder(); ?>" title="Back to site">Back to site</a>
    </nav>
</div>
<?php
    }
}