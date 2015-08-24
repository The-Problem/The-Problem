<?php
class AjaxUserLogoutPage implements IPage {
    public function __construct(PageInfo &$info) {
    }

    public function template() {
        return Templates::findtemplate("blank");
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
        $_SESSION['username'] = NULL;
        $_SESSION['sudo'] = false;
        if (array_key_exists('return', $_GET)) Path::redirect($_GET['return']);
        Path::redirect(Path::getclientfolder());
    }
}