<?php
class BugsBugPage implements IPage {
    private $bug = false;

    public function __construct(PageInfo &$page) {
        $path = $page->pagelist;

        $res = Connection::query("
SELECT *, bugs.Description AS Bug_Description, bugs.Object_ID AS Bug_ObjectID, bugs.Name AS Bug_Name FROM bugs
    JOIN sections ON (sections.Section_ID = bugs.Section_ID)
    WHERE sections.Slug = ?
    AND bugs.RID = ?", "si", array($path[2], $path[3]));
        $this->bug = $res[0];

        if (!$this->bug) Pages::showpagefrompath(array("error"));
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

    }

    public function body() {
        echo "<h2 style='width:625px;margin:0 auto;padding:30px 0 30px 75px'>" . htmlentities($this->bug["Bug_Name"]) . "</h2>";

        Modules::getoutput("comments", $this->bug);
    }
}