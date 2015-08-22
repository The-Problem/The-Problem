<?php
class BugsBugPage implements IPage {
    private $bug = false;

    public function __construct(PageInfo &$page) {
        $path = $page->pagelist;

        $res = Connection::query("
SELECT * FROM bugs
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
        echo "I'm bug #" . $this->bug["Bug_ID"];

        Modules::getoutput("comments", $this->bug);
    }
}