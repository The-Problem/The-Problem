<?php
/**
 * Allows packages and resources to be easily loaded
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package LimePHP.Packages
 */
class Packages {
    /**
     * Get a list of packages in the folder specified
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $folder Folder to use, default is 'packages'.
     * @return array List of packages in folder
     */
    public static function discover($folder = "packages") {
        $dirs = glob(Path::getserverfolder($folder) . "*", GLOB_ONLYDIR);
        return $dirs;
    }
    
    /**
     * Load packages in specified directories
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param mixed $dirs Folders for packages, as an array or string
     */
    public static function load($dirs) {
        if (!is_array($dirs)) $dirs = array($dirs);
        
        $res = new Resources();
        
        $res->addprocessor("Libraries", "Library::add");
        $res->addprocessor("Autoload", function($n) {
            Library::get($n);
        });
        
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $res->setarg($dir);
                self::loadat($dir, $res);
            }
        }
        
        Events::call("preprocess");
        $res->process();
    }
    
    private static function loadat($folder, &$res) {
        $libraryfile = Path::implodepath($folder, "package.php");
        
        if (!file_exists($libraryfile)) throw new Exception("Cannot find 'package.php' in '" . $folder . "'");
        l_include($libraryfile, false);
        
        $name = basename($folder);
        $classname = ucwords($name) . "Package";
        
        if (!class_exists($classname)) throw new Exception("Cannot find class called " . $classname . " for library " . $name);
        
        $package = new $classname();
        $package->initialize($res);
    }
}