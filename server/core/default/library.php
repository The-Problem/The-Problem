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

    private static $cache_version = "0.1";
    private static $cache_changed = false;

    public static $lcache_version = false;
    public static $lcache_mode = LIME_LIBCACHE_DISABLED;

    public static $library_cache = array();
    
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
            if (self::loaded($lib)) continue;
            self::getlib($lib);

            Events::call("libloaded.*", array($lib));
            Events::call("libloaded.$lib");
        }
    }

    private static function getlib($name) {
        if (LIME_LIBCACHE_MODE === LIME_LIBCACHE_DISABLED) self::fetch($name);
        else {
            self::load_cache();

            if (array_key_exists($name, self::$library_cache)) {
                $cache = self::$library_cache[$name];
                if (LIME_LIBCACHE_MODE === LIME_LIBCACHE_VALIDATE) {
                    $mtime = filemtime(Path::implodepath($cache["root"], 'library.json'));
                    if ($mtime === FALSE || $mtime > $cache["changed"]) {
                        self::fetch($name);
                        return;
                    }
                }

                array_push(self::$loadedlibs, $name);
                call_user_func($cache["load"]);
            } else self::fetch($name);
        }
    }

    private static function fetch($name) {
        if (!array_key_exists($name, self::$libraries)) throw new Exception("No library called $lib");

        $folder = self::$libraries[$name];
        array_push(self::$loadedlibs, $name);

        if (!is_dir($folder)) throw new Exception("Cannot find any library in '$folder'");

        $json = Path::implodepath($folder, "library.json");
        if (!file_exists($json)) throw new Exception("Cannot find 'library.json' in '$folder'");
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
            $path = Path::implodepath($folder, "$l.php");
            l_include($path, false);
        }

        if (LIME_LIBCACHE_MODE !== LIME_LIBCACHE_DISABLED) {
            self::$cache_changed = true;
            self::$library_cache[$name] = array(
                "root" => $folder,
                "data" => $lib_data
            );
        }
    }

    private static function load_cache() {
        static $has_included = false;

        if (!$has_included) {
            if (file_exists(LIME_CACHE_ROOT . '/library.php')) include(LIME_CACHE_ROOT . '/library.php');

            $has_included = true;
        }
    }

    public static function flush_cache() {
        if (LIME_LIBCACHE_MODE === LIME_LIBCACHE_DISABLED || !self::$cache_changed) return;

        $value = "<?php\n";
        $value .= 'Library::$lcache_version = "' . self::$lcache_version . '";' . "\n";
        $value .= 'Library::$lcache_mode = ' . LIME_LIBCACHE_MODE . ";\n\n";

        $items = array();

        foreach (self::$library_cache as $name => $cache) {
            $mtime = filemtime(Path::implodepath($cache["root"], 'library.json'));

            $item  = "    '" . self::string_safe($name) . "' => array(\n";
            $item .= "        'changed' => $mtime,\n";
            $item .= "        'root' => '" . self::string_safe($cache["root"]) . "',\n";
            $item .= "        'data' => " . var_export($cache["data"], true) . ",\n";
            $item .= "        'load' => function() {\n";

            if (array_key_exists("requires", $cache["data"])) {
                $requires = $cache["data"]["requires"];
                $item .= "            // REQUIRES: " . implode(", ", $requires) . "\n";
                foreach ($requires as $require) {
                    $item .= "            Library::get('" . self::string_safe($require) . "');\n";
                }
            }

            $load = $cache["data"]["load"];
            if (gettype($load) !== "array") $load = array($load);
            $item .= "            // LOADS: " . implode(", ", $load) . "\n";
            foreach ($load as $l) {
                $path = Path::implodepath($cache["root"], "$l.php");
                $item .= "            l_include('" . self::string_safe($path) . "', false);\n";
            }

            $item .= "        }\n";
            $item .= "    )";
            array_push($items, $item);
        }

        $value .= 'Library::$library_cache = array(' . "\n";
        $value .= implode(",\n", $items);
        $value .= ');';

        file_put_contents(LIME_CACHE_ROOT . '/library.php', $value);
    }

    private static function string_safe($val) {
        return str_replace("'", "\\'", str_replace("\\", "\\\\", $val));
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