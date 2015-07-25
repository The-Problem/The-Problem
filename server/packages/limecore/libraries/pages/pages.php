<?php
/**
 * Class to load pages
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.1
 * @copyright Copyright (c) 2013, mrfishie Studios
 * @package LimeCore.Pages
 */
class Pages {
    /**
     * Stores the current page header.
     * Will be null before/after a page has been displayed
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @var Head The page head
     */
    public static $head;
    
    private static $pages = array();
    
    private static $showing = false;
    
    /**
     * Register a page in a certain location
     * Used by the resource manager
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of page
     * @param string $dir Directory of package containing page
     *
     * @throws Exception when page already exists
     */
    public static function add($name, $dir) {        
        if (array_key_exists($name, self::$pages)) throw new Exception("Page called " . $name . " already exists in " . self::$pages[$name] . " while processing library " . basename($dir));
        
        if (!array_key_exists($name, self::$pages)) self::$pages[$name] = array();
        array_push(self::$pages[$name], $dir);
        
        
        Events::call("pageadded", array($name));
    }
    
    /**
     * Get a page by name
     *
     * Returns false if it cannot find the page
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 2.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param mixed $name The name of the page
     * @param array $fp Current page path
     * @return mixed The page, or false if page cannot be found
     */
    public static function getpage($name, array $fp) {
        if (!is_array($name)) $name = array($name);
        
        Library::file("pages", "pageinfo");
        
        $finalpage = false;
        
        if (!array_key_exists($name[0], self::$pages)) return false;
        
        foreach (self::$pages[$name[0]] as $dir) {
            $fullpath = array();
            foreach ($name as $i => $p) {
                array_push($fullpath, $p);
                $semipath = $fullpath;
                if (count($semipath) == 1) $semipath[1] = $semipath[0];
                
                $semipath = array_map(function($n) {
                    return urlencode($n);
                }, $semipath);
                
                $url = implode(DIRECTORY_SEPARATOR, $semipath) . ".php";
                $fullurl = Path::implodepath($dir, "pages", $url);
                
                if (!file_exists($fullurl)) continue;
                
                $info = new PageInfo($fp);
                include_once($fullurl);
                
                $classname = implode(array_map(function($n) {
                    return ucwords($n);
                }, $fullpath));
                $class = $classname . "Page";
                if (!class_exists($class)) throw new Exception("Cannot find class called " . $class . " for page " . Path::implodepath($fullpath));
                
                $finalpage = new $class($info);
                
                Events::call("pagefind", array($finalpage, $name));
                
                if (!$finalpage->subpages()) break;
            }
            if ($finalpage) break;
        }
        return $finalpage;
    }
    
    /**
     * Output a page
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param IPage $page Page to display
     * @param array $path The path for the page
     * @param bool $allowcancel Whether to allow cancelling (requires some setup)
     * @return boolean Whether the page was successfully displayed
     */
    public static function showpage(IPage $page, array $path, $allowcancel = false) {
        Library::file("pages", "head");
        Library::get("modules");
        
        $head = new Head();
        self::$head = $head;
        
        if (!$page->permission()) return false;
        
        $template = $page->template();
        if (!$template) return false;
        $template->head($head);
        $page->head($head);
        Events::call("pagehead", array(self::$head));
        
        // Find if the page has been cancelled
        if ($allowcancel && self::$showing !== $path) return true;
        
        // Capture the body output in a variable
        ob_start();
        $pagecode = $page->body();
        if (!$pagecode) $pagecode = ob_get_clean();
        else ob_end_clean();
        
        Events::call("pagebody", $pagecode);
        
        // Find if the page has been cancelled
        if ($allowcancel && self::$showing !== $path) return true;
        
        $template->showpage(self::$head, $pagecode, $page);
        
        return true;
    }
    
    /**
     * Shows a page from page path
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param array $path Current page path
     * @return boolean Whether the page was successfully displayed
     */
    public static function showpagefrompath(array $path) {
        self::$showing = $path;
        
        $startpath = $path;
        array_push($path, "");
        $page = false;
        while (!$page) {
            array_pop($path);
            if (count($path) == 0) break;
            $page = self::getpage($path, $startpath);
            if (!$page || !$page->permission()) $page = false;
        }
        if (!$page) $page = self::getpage(array("error"), $startpath);
        if ($page) return self::showpage($page, $startpath, true);
    }
    
    /**
     * 'Redirects' to another page without actually changing the URL
     *
     * @param array $path The new path
     * @return boolean Whether the page was successfully displayed
     */
    public static function redirect(array $path) {
        return self::showpagefrompath($path);
    }
}