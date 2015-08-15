<?php
/**
 * Allows libraries and library files to be easily loaded
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 2.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package LimePHP.Default
 */
class Library {
    private static $libraries = array();
    public static $loadedlibs = array();
    
    /**
     * Register a library in a certain location.
     * Used by the resource manager
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of library
     * @param string $dir Directory of package containing library
     *
     * @throws Exception when library already exists
     */
    public static function add($name, $dir) {
        if (array_key_exists($name, self::$libraries)) throw new Exception("Library called " . $name . " already exists in " . self::$libraries[$name] . " while processing library " . basename($dir));
        self::$libraries[$name] = Path::implodepath($dir, "libraries", $name);
        
        Events::call("libadded", array($name));
        Events::call("libadded.$name");
    }
    
    /**
     * Load a library into memory
     * Multiple libraries can be specified by passing in multiple parameters, one for each library
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of library
     * 
     * @throws Exception when no library with name can be found
     * @throws Exception when 'library.json' cannot be found
     * @throws Exception when no library can be found in folder
     */
    public static function get($name) {
        $libs = func_get_args();
        foreach ($libs as $lib) {
            if (!self::loaded($lib)) {
                if (!array_key_exists($lib, self::$libraries)) {
                    throw new Exception("No library called " . $lib);
                }
                
                $folder = self::$libraries[$lib];
                array_push(self::$loadedlibs, $lib);
                
                if (is_dir($folder)) {
                    $json = Path::implodepath($folder, "library.json");
                    
                    if (!file_exists($json)) throw new Exception("Cannot find 'library.json' in '" . $folder . "'");
                    $lib_data = self::json_wrap($json);
                    
                    if (array_key_exists("requires", $lib_data)) {
                        $libstoload = $lib_data["requires"];
                        foreach ($libstoload as $l) {
                            self::get($l);
                        }
                    }
                    $load_d = $lib_data["load"];
                    if (gettype($load_d) !== "array") $load_d = array($load_d);
                    foreach ($load_d as $l) {
                        $path = Path::implodepath($folder, $l . ".php");
                        
                        l_include($path, false);
                    }
                } else if (file_exists($folder . ".php")) {
                    l_include($folder . ".php", false);
                } else throw new Exception("Cannot find any library in '" . $folder . "'");
                Events::call("libloaded.*", array($name));
                Events::call("libloaded." . $name);
            }
        }
    }
    
    /**
     * Finds if a library exists
     *
     * @param string $name Name of library
     * @return boolean Whether the library exists
     */
    public static function exists($name) {
        return array_key_exists($name, self::$libraries);
    }
    
    /**
     * Load a single file from a library
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $base_lib Name of library to load from
     * @param string $file Name of file (without the .php) to load
     *
     * @throws Exception when no library called $base_lib can be found
     */
    public static function file($base_lib, $file) {
        if (!is_array($file)) $file = array($file);
        if (!self::loaded($base_lib)) self::get($base_lib);
        
        if (!array_key_exists($base_lib, self::$libraries)) throw new Exception("No library called " . $base_lib);
        $folder = self::$libraries[$base_lib];
        foreach ($file as $f) {
            $filepath = Path::implodepath($folder, $f . ".php");
            if (file_exists($filepath)) l_include($filepath, false);
        }
    }
    
    /**
     * Find if a library has been loaded already
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $lib Name of library to test
     * @return bool Has the library been loaded?
     */
    public static function loaded($lib) {
        return in_array($lib, self::$loadedlibs);
    }
    
    private static function json_wrap($url) {
        $contents = file_get_contents($url);
        $json = json_decode($contents, true);
        return $json;
    }
}