<?php
class ImagePage implements IPage {
    private $path = array();
    private $img;
    
    public function __construct(PageInfo &$info) {
        $this->path = $info->pagelist;
    }
    public function template() {
        return Templates::findtemplate("blank");
    }
    public function permission() {
        return true;
    }
    public function subpages() { return false; }
    
    public function head(Head &$head) { }
    public function body() {
        header('Cache-Control: max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', strtotime('+1 year')));

        Library::get("image");
                
        $name = $this->path[2];
        if ($this->path[1] === "url") $name = $_GET[$this->path[2]];

        try {
            $img = new Image($this->path[1], $name, $this->path[3]);
            $img->load();
            $img->process();

            $info = getimagesize($img->serverpath);
            header('Content-type: ' . $info["mime"]);

            readfile($img->serverpath);
        } catch (Exception $ex) {
            header('Location: ' . Path::getclientfolder("res", "image") . "empty.png");
        }
    }
}