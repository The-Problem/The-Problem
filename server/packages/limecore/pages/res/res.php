<?php
class ResPage implements IPage {
    private $path;
    
    public function __construct(PageInfo &$info) {
        $this->path = Path::implodepatharray(array_slice($info->pagelist, 1));
    }
    public function template() { return Templates::findtemplate("blank"); }
    public function permission() { return true; }
    public function subpages() { return false; }
    public function head(Head &$head) { }
    public function body() {
        Library::get("string");        
        $filepath = Path::getserverfolder("res") . Path::realpath($this->path);
        
        if (!is_file($filepath)) {
            Library::get("json");
            echo Json::encode(array("error" => "Access denied"));
        }
        
        $contents = file_get_contents($filepath);
        
        $extension = pathinfo($filepath, PATHINFO_EXTENSION);
        if ($extension === "js") $mimetype = "text/javascript";
        else if ($extension === "css") {
            $mimetype = "text/css";
            foreach ($_GET as $old => $new) {
                $contents = str_replace($old, $new, $contents);
            }
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $filepath);
            finfo_close($finfo);
        }
        
        header("Content-type: $mimetype");
        
        echo $contents;
    }
}