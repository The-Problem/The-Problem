<?php
/**
 * Class that allows access to templates
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.Pages
 */
class Templates {
    private static $templates = array();

    public static $theme = "";
    
    /**
     * Choose which folders to look in for template file
     *
     @author Tom Barham <me@mrfishie.com>
     @version 1.0
     @copyright Copyright (c) 2014, Tom Barham
     @var array Folders to look in
     */
    public static $folders = array(
        "_general"
    );
    
    /**
     * Register a template in a certain location
     * Used by the resource manager
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of template
     * @param string $dir Directory of package containing template
     *
     * @throws Exception when template already exists
     */
    public static function add($name, $dir) {        
        if (array_key_exists($name, self::$templates)) throw new Exception("Template called " . $name . " already exists in " . self::$templates[$name] . " while processing library " . basename($dir));
        self::$templates[$name] = $dir;
        
        Events::call("templateadded", array($name));
    }
    
    /**
     * Get a template by name
     * Returns false if it cannot find the template
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name The name of the template
     * @return mixed The template, or false if it cannot be found
     *
     * @throws Exception when the template is not found
     * @throws Exception when the template class is not found
     */
    public static function findtemplate($name) {
        if (!array_key_exists($name, self::$templates)) throw new Exception("No template called " . $name);
        
        $checkfolders = array_merge(array(self::$theme), self::$folders);
        
        $url = null;
        foreach ($checkfolders as $folder) {
            $u = Path::implodepath(self::$templates[$name], "templates", $folder, $name) . ".php";
            if (file_exists($u)) {
                $url = $u;
                break;
            }
        }
        if (is_null($url)) throw new Exception("Can't find template called '" . $name . "' in theme '" . self::$theme . "'");
        
        l_include($url, false);
        $classname = ucwords($name) . "Template";
        if (!class_exists($classname)) throw new Exception("Cannot find class called " . $classname . " for template " . $name);
        return new $classname();
    }
}
