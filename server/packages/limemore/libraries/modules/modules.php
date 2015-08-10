<?php
/**
 * Module management code
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.1
 * @copyright Copyright (c) 2013, mrfishie Studios
 * @package Libraries.Modules
 */
class Modules {
    private static $list = array();
    private static $modules = array();
    
    const SPINNER_LARGE = 1;
    const SPINNER_SMALL = 2;
    
    /**
     * Register a module in a certain location
     * Used by the resource manager
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of module
     * @param string $dir Directory of package containing module
     *
     * @throws Exception when module already exists
     */
    public static function add($name, $dir) {
        if (array_key_exists($name, self::$modules)) throw new Exception("Module called " . $name . " already exists in " . self::$modules[$name] . " while processing library " . basename($dir));
        self::$modules[$name] = $dir;
        
        Events::call("moduleadded", array($name));
    }
    
    /**
     * Get a module by name
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $name Name of module to get
     * @return mixed Module from name if exists, otherwise false
     *
     * @throws Exception
     */
    public static function &getmodule($name) {
        if (!array_key_exists($name, self::$modules)) throw new Exception("No module called " . $name);
        
        if (!array_key_exists($name, self::$list)) {
            $path = Path::implodepath(self::$modules[$name], "modules", urlencode($name)) . ".php";
            if (!file_exists($path)) throw new Exception("Cannot find module in " . $path);
            
            l_include($path, false);
            
            $classname = ucwords($name) . "Module";
            if (!class_exists($classname)) throw new Exception("Cannot find module class " . $classname . " in " . $path);
            
            $module = new $classname();
            self::$list[$name] = $module;
        }
        return self::$list[$name];
    }
    
    /**
     * Outputs the modules code
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $name Name of module to output
     * @param mixed $params Parameters to pass to the module
     * @param bool $json Whether to output JSON
     * @param bool $includeOutside Whether to include the 'outside' component of the module
     */
    public static function getoutput($name, $params = array(), $json = false, $includeOutside = true) {
        $module = self::getmodule($name);
        
        $r = Events::call("modules." . strtolower($name));
        $params = array_merge((array)$r, (array)$params);
        
        Library::get("pages");

        echo "getting code";
        ob_start();
        $body = $module->getcode($params, Pages::$head);
        $extra = ob_get_clean();
        
        if (!is_string($body)) $body = $extra;
        else if (is_string($extra)) $body .= $extra;
        
        if ($includeOutside) $body = $module->getsurround($body, $params);
        
        if ($json) {
            Pages::$head->title = null;
            $head = Pages::$head->getcodearray();
            echo json_encode(array(
                "body" => $body,
                "head" => $head
            ));
        } else {
            echo $body;
        }
    }
    
    /**
     * Loads a module on the client-side
     *
     * Creates some Javascript that will load a module once the page has been loaded
     * The code is *outputted in this function*.
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of module to output
     * @param mixed $params Parameters to pass to the module
     * @return string Code for the module
     */
    public static function output($name, $params = array()) {
        $key = uniqid();
        $cookieVal = array(
            "type" => $name,
            "params" => $params
        );
        
        $modulelist = Cookies::prop("modules");
        $modulelist[$key] = $cookieVal;
        Cookies::prop("modules", $modulelist);
        
        Pages::$head->additem("module", $key);
        
        $module = self::getmodule($name);
        $spinnersize = $module->spinnersize();
        $class = $spinnersize === self::SPINNER_SMALL ? "spinner-small" : "spinner";
        $code = "<div class='" . $key . " module'><div class='" . $class . "'><div class='circle circle-1'></div><div class='circle circle-2'></div><div class='circle circle-3'></div><div class='circle circle-4'></div><div class='circle circle-5'></div><div class='circle circle-6'></div><div class='circle circle-7'></div><div class='circle circle-8'></div></div></div>";
        echo $module->getsurround($code, $params);
        
        return $key;
    }
}
