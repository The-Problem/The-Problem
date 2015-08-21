<?php
class AjaxModulesLoadPage implements IPage {
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
        $sname = $this->path[count($this->path) - 1];
        
        $modulelist = $_SESSION["modules"];
        if (!array_key_exists("modules", $_SESSION) || !array_key_exists($sname, $modulelist)) echo Json::encode(array("error" => "The module ID $sname is invalid or does not exist"));
        
        $in = $modulelist[$sname];
        Modules::getoutput($in["type"], $in["params"], true, false);
        $_SESSION["modules"] = array_diff_key($modulelist, array($sname => true));
    }
}
