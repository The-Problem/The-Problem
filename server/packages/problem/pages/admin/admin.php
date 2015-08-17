<?php
class AdminPage implements IPage {
    public function __construct(PageInfo &$page) {
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
    }

    public function body() {
        ?>
<h1>Admin Control Panel</h1>
<div class="container">
    <div class="panel"></div>
    <nav>
        <a class="item" href="<?php echo Path::getclientfolder('admin', 'overview'); ?>" title="Overview">Overview</a>
        <a class="item" href="<?php echo Path::getclientfolder('admin', 'sections'); ?>" title="Sections">Sections</a>
        <a class="item" href="<?php echo Path::getclientfolder('admin', 'permissions'); ?>" title="Permissions">Permissions</a>
        <a class="return" href="<?php echo Path::getclientfolder(); ?>" title="Back to site">Back to site</a>
    </nav>
</div>
<?php
    }
}