<?php
class HomePage implements IPage {
    public function __construct(PageInfo &$page) {
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
        return "It works!";
    }
}