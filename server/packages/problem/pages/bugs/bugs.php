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
    }
}