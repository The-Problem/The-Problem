<?php
class AjaxModulesGetPage implements IPage {
    private $path;
    public function __construct(PageInfo &$page) {
        $this->path = $page->pagelist;
    }
    
    public function template() {
        return Templates::findtemplate("blank");
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
        
        $name = $_POST['name'];
        $params = json_decode($_POST['params']);
        
        Modules::getoutput($name, $params, true, false);
    }
}
