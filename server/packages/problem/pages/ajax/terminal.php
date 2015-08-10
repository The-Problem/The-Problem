<?php

class AjaxTerminalPage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        return Templates::findtemplate("ajax");
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return true;
    }

    public function head(Head &$head) {
    }

    public function body() {
        return array("error" => true);
    }
}