<?php
class AdminPage implements IPage {
    private $pages = array(
        "overview" => "Overview",
        "sections" => "Sections",
        "permissions" => "Permissions"
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
        Library::get("cookies");
        $username = Cookies::prop("username");
        if (!$username) return false;

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];

        if ($rank < 4) return false;
        if (!Cookies::prop("sudo")) Path::redirect(Path::getclientfolder("sudo") . "?return=" . urlencode($_SERVER['REQUEST_URI']));
        return true;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
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
        ?>
<table class="section-list">
    <tr><th class="name">Name</th><th class="description">Description</th><th class="bugs-open">Open Bugs</th><th class="bugs-closed">Closed Bugs</th></tr>
    <tr><td>Users</td><td>The user management system in The Problem.</td><td>60</td><td>70</td></tr>
    <tr><td>Users</td><td>The user management system in The Problem.</td><td>60</td><td>70</td></tr>
    <tr><td>Users</td><td>The user management system in The Problem.</td><td>60</td><td>70</td></tr>
    <tr><td>Users</td><td>The user management system in The Problem.</td><td>60</td><td>70</td></tr>
    <tr><td>Users</td><td>The user management system in The Problem.</td><td>60</td><td>70</td></tr>
    <tr><td>Users</td><td>The user management system in The Problem.</td><td>60</td><td>70</td></tr>
    <tr><td>Users</td><td>The user management system in The Problem.</td><td>60</td><td>70</td></tr>
</table>
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