<?php
class BugsPage implements IPage {
    private $section;
    private $showing = false;
    private $path;

    public function __construct(PageInfo &$page) {
        $this->path = $page->pagelist;
    }

    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return false;
    }
    public function head(Head &$head) {
        $count = count($this->path);

        if ($count === 2) {
            $this->section = $this->path[1];
            $this->showing = true;
        } else if ($count > 2) {
            Pages::showpagefrompath(array("bugs", "bug", $this->path[1], $this->path[2]), false);
        }
    }

    public function body() {
        echo "Showing section: $this->section";
		
		?>
<h2>Bug list</h2>
<ul>
<?php
$bugs = Connection::query("SELECT RID, bugs.Name FROM bugs
                     JOIN sections ON (sections.Section_ID = bugs.Section_ID)
				   WHERE sections.Slug = ?", "s", array($this->section));

foreach ($bugs as $bug) {
	echo "<li><a href='" . Path::getclientfolder("bugs", $this->section, $bug["RID"]) . "'>" . htmlentities($bug["Name"]) . "</a></li>";
}
?>
</ul>
        <?php
    }
}