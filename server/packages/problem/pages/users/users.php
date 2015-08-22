<?php
class UsersPage implements IPage {
    private $user = "";

    public function __construct(PageInfo &$page) {
        $this->user = $page->pagelist[1];
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
        echo "Viewing profile for $this->user";
    }
}