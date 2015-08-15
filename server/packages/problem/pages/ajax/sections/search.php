<?php
class AjaxSectionsSearch implements IPage {
    public function __construct(PageInfo &$page) {
    }

    public function template() {
        return Templates::findtemplate("ajax");
    }

    public function subpages() {
        return false;
    }
    public function permission() {
        return true;
    }
    public function head(Head &$head) { }

    public function body() {
        Library::get('modules');

    }
}