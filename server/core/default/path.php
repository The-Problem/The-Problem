<?php
/**
 * Utilities to create and find paths
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.1
 * @copyright Copyright (c) 2013, mrfishie Studios
 * @package Core
 */
class Path {
    private static $p = false;
    private static $webpath = false;
    private static $domain = false;
    
    /**
     * A list of subdomains to strip from the path.
     * This allows for resource subdomains to be removed
     * from any links.
     *
     * This can prevent some scripts from working due
     * to the same-origin policy, however.
     *
     * @var array The list of subdomains
     */
    public static $subdomains = array();
    
    /**
     * Get currently viewed page as an array
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @return array Current page URL as an array
     */
    public static function getpage() {
        if (!self::$p) {
            $basepath = String::trimstart($_SERVER['REQUEST_URI'], dirname($_SERVER['SCRIPT_NAME']));
            $params = self::explodepath(strtok($basepath, "?"));
            $params = array_map(function($n) {
                if (strpos($n, "?") === false && trim($n) !== "") return trim(urldecode($n));
                return NULL;
            }, $params);
            $params = array_values(array_filter($params));
            
            if (!count($params)) $params = array("home");
            else if ($params[0] == "" || $params[0] == basename($_SERVER['SCRIPT_NAME'])) $params[0] = "home";
            self::$p = $params;
        }
        return self::$p;
    }
    
    /**
     * Explodes current path to an array
     *
     * Uses platform independant directory separator to split path
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $path Path to convert
     * @return array Exploded path
     */
    public static function explodepath($path) {
        return explode("\\", str_replace("/", "\\", $path));
    }
    
    /**
     * Implodes a path to a string
     *
     * Uses platform independant directory separator to implode path
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param array $path Path to implode
     * @return string Imploded path
     */
    public static function implodepath($path) {
        $path = func_get_args();
        
        return self::implodepatharray($path);
    }
    
    /**
     * Implodes a path as an array
     *
     * @param array $path Path to implode
     * @return string Imploded path
     */
    public static function implodepatharray($path) {
        return implode(DIRECTORY_SEPARATOR, array_map(function($n) {
            return trim($n, trim("\\", trim("/", $n)));
        }, $path));
    }
    
    /**
     * Get the URL of a folder in the server folder directory
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.1
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param mixed $folders Path of folders to find as an array, or single folder to find as a string
     * @return string Server path of specified folders
     */
    public static function getserverfolder($folders) {        
        $folders = array();
        foreach (func_get_args() as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $itm) {
                    array_push($folders, urlencode($itm));
                }
            } else array_push($folders, urlencode($arg));
        }
        
        array_unshift($folders, LimePHP::$root);
        
        $implodedPath = implode(DIRECTORY_SEPARATOR, $folders);
        
        if ($implodedPath[strlen($implodedPath) - 1] === DIRECTORY_SEPARATOR) return $implodedPath;
        else return $implodedPath . DIRECTORY_SEPARATOR;
    }
    
    /**
     * Equivalent of realpath() but works with files that
     * don't exist
     *
     * @param string $path The path
     * @return string The converted absolute path
     */
    public static function realpath($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ($part === '.') continue;
            if ($part === '..') array_pop($absolutes);
            else array_push($absolutes, $part);
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }
    
    /**
     * Get the URL of a folder in the client folder directory
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.1
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param mixed $folders Path of folders to find as an array, or single folder to find as a string
     * @return string Client path of specified folders
     */
    public static function getclientfolder($folders = array()) {
        $folders = func_get_args();
        return self::getclient($folders);
    }
    
    /**
     * Gets the URL of a folder in a specific URL
     *
     * @param mixed $f Path of folders to find as an array, or single folder to find as a string
     * @param mixed $p The base path to start at
     * @return string Client path of specific folders
     */
    public static function getclient($f = array(), $p = NULL) {
        // Flatten array
        $folders = new RecursiveIteratorIterator(new RecursiveArrayIterator($f));
        
        if (is_null($p)) $p = self::webpath();
        else {
            Library::get("string");
            $p = String::trimend($p, "/");
        }
        
        if (is_array($folders)) $folders = array($folders);
        foreach ($folders as $folder) {
            $p .= "/" . urlencode($folder);
        }
        return $p . "/";
    }
    
    /**
     * URL of current page, without path (e.g. http://google.com/)
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @return string URL of current page without path
     */
    public static function webpath() {
        if (!self::$webpath) {
            $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == "on") ? "s" : "";
            $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
            $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
            //$port = ($_SERVER['SERVER_PORT'] == '80') ? "" : (":" . $_SERVER['SERVER_PORT']);
            $web_path = dirname($protocol. "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
            
            $components = parse_url($web_path);
            
            $host = $components["host"];
            Library::get("string", "http_build_url");
            foreach (self::$subdomains as $sd) {
                $subdomain = String::trimend($sd, ".") . ".";
                $host = String::trimstart($host, $subdomain);
            }
            $components["host"] = $host;
            
            self::$webpath = http_build_url($components);
        }
        return self::$webpath;
    }
    
    /**
     * Base domain of the current page
     * Useful for getting the domain for a cookie
     *
     * E.g. 'http://somedomain.co.uk' outputs 'somedomain.co.uk'
     *
     * @return mixed The base domain for the current page, or false on failure
     */
    public static function getdomain() {
        if (!self::$domain) {
            $host = parse_url('http://' . $_SERVER['SERVER_NAME'], PHP_URL_HOST);
            if(preg_match('/([a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host, $m)) {
                self::$domain = $m[1];
            }
            if (empty(self::$domain)) self::$domain = "localhost";
        }
        return self::$domain;
    }
    
    /**
     * Redirect to a different page
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $url URL to redirect to
     */
    public static function redirect($url) {
        header("Location: $url");
    }
    
    /**
     * Add GET parameters to a URL
     *
     * If the specified parameter already exists, it is overwritten
     *
     * @param string $url The URL to add
     * @param mixed $items The GET parameters to add, or the name of the parameter to add
     * @param string $value The value of the parameter
     * @return string The final URL
     */
    public static function addget($url, $items, $value = null) {
        if (!is_array($items)) {
            $itms = array();
            $itms[$items] = $value;
            $items = $itms;
        }
        
        $components = parse_url($url);
        $parsed = array();
        if (array_key_exists("query", $components)) parse_str($components["query"], $parsed);
        
        foreach ($items as $name => $v) {
            if (!is_null($v)) $parsed[$name] = $v;
            else unset($parsed[$name]);
        }
        
        $components["query"] = http_build_query($parsed);
        Library::get("http_build_url");
        return http_build_url($components);
    }
    
    /**
     * Gets a specific GET parameter from the URL
     *
     * @param string $url The URL to use
     * @param string $name The name of the GET parameter
     * @return string The value of the GET parameter
     */
    public static function getget($url, $name) {
        $components = parse_url($url);
        $parsed = array();
        parse_str($components["query"], $parsed);
        
        return $parsed[$name];
    }
    
    /**
     * Adds a subdomain to a URL
     *
     * @param string $url Original URL
     * @param string $subdomain Subdomain to add
     * @return string Final URL
     */
    public static function addsubdomain($url, $subdomain) {
        $components = parse_url($url);
        $components["host"] = $subdomain . "." . $components["host"];
        Library::get("http_build_url");
        return http_build_url($components);
    }
    
    /**
     * Request a page from a server via HTTP
     *
     * If the method is not GET, any GET parameters in the URL will be copied into the POST parameters, but also kept in the URL.
     *
     * @param string $url The URL to request
     * @param string $method The method for the request (GET, POST, PUT, DELETE). Default is GET
     * @param array Parameters for the request
     * @return Response The response from the server
     */
    public static function request($url, $method = "GET", $parameters = array()) {
        $method = strtoupper($method);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $query = http_build_query($parameters);
        if ($method !== 'GET') {
            if ($method !== 'POST') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            else curl_setopt($ch, CURLOPT_POST, true);
            
            $pos = strpos($url, "?");
            if ($pos < 0) {
                $end = substr($url, $pos + 1);
                
                $query .= (strlen($query) > 0 ? "&" : "") . $end;
            }
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        } else $url = self::addget($url, $parameters);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        
        curl_close($ch);
        
        return new Response($response, $info);
    }
    
    /**
     * Downloads a file to a certain location
     *
     * @param string $url The url to the file
     * @param string $path The path to download the file to
     * @return mixed True for success, otherwise the result of curl_error
     */
    public static function download($url, $path) {
        $ch = curl_init($url);
        $fp = fopen($path, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        if (curl_error($ch)) return curl_error($ch);
        curl_close($ch);
        fclose($fp);
        return true;
    }
    
    /**
     * Finds if a remote file exists
     *
     * @param string $file The url to the file
     * @return boolean Whether the file exists
     */
    public static function exists($file) {
        $ch = curl_init($file);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $retcode === 200;
    }
}