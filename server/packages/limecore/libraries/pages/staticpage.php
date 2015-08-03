<?php
/**
 * Provides the ability to load plain HTML pages
 *
 * Specify the plain HTML files in the /config/staticpages.json file
 * Currently does not support permissions or subpages
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.Pages
 */
class StaticPage implements IPage {
    private $p;
    private $path;
    
    public function __construct(PageInfo &$info) {
        Library::loadlib("json");
        
        $config = Json::readfile(Path::getserverfolder("config") . "pages.json");
        $staticpages = $config["static"];
        if (count($staticpages) > 0) {
            $pagelist = $info->pagelist;
            if (array_key_exists($pagelist[0], $staticpages)) {
                $this->p = $staticpages[$pagelist[0]];
                $this->path = $pagelist;
            } else $this->p = false;
        } else $this->p = false;
    }
    
    public function template() {
        if ($this->p) {
            if (array_key_exists("template", $this->p)) return Templates::findtemplate($this->p["template"]);
            else return Templates::findtemplate("default");
        } else return false;
    }
    
    public function permission() {
        if ($this->p) return true;
        else return false;
    }
    
    public function subpages() {
        return false;
    }
    
    public function head(Head &$head) {
        if ($this->p && array_key_exists("head", $this->p)) {
            if (array_key_exists("head", $this->p) && is_array($this->p["head"])) {
                $h = $this->p["head"];
                foreach ($h as $tag) {
                    if (!is_array($tag)) $head->addcode($tag);
                    else {
                        if ($tag[0] == "stylesheet") $head->stylesheet($tag[1]);
                        else if ($tag[0] == "script") $head->script($tag[1]);
                    }
                }
            } else {
                $path = Path::getserverfolder(array("pages", "static")) . urlencode($this->path[0]) . ".head.html";
                if (file_exists($path)) {
                    $fg = file_get_contents($path);
                    $head->addcode($fg);
                }
            }
        }
    }
    
    public function body() {
        if ($this->p) {
            $fg = file_get_contents(Path::getserverfolder(array("pages", "static")) . urlencode($this->path[0]) . ".body.html");
            echo $fg;
        }
    }
}
