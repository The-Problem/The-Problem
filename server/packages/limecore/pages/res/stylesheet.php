<?php
class ResPage implements IPage {
    private $replaceitems;
    private $path;
    
    public function __construct(PageInfo &$info) {
        if (count($info->pagelist) > 1) $this->path = $info->pagelist[1];
        else $this->path = array();
        
        if (count($info->pagelist) > 2) $this->replaceitems = $info->pagelist[2];
        else $this->replaceitems = "";
    }
    public function template() { return Templates::findtemplate("blank"); }
    public function permission() { return true; }
    public function subpages() { return false; }
    public function head(Head &$head) { }
    public function body() {
        Library::get("string");
        $replaceitems = String::assocexplode($this->replaceitems, "=", ",");
        
        $filepath = Path::getserverfolder("res") . $this->path;
    }
}