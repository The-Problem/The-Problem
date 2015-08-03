<?php
/**
 * JSON utility class
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.2
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.JSON
 */
class Json {
    const CACHE_FILE = 1;
    const CACHE_DECODE = 2;
    const CACHE_BOTH = 3;
    
    private static $filecache = array();
    private static $decodecache = array();
    
    /**
     * Read JSON from a file with caching
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.1
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $path Path to file to read
     * @return array Array read from file
     */
    public static function readfile($path) {
        if (!array_key_exists($path, self::$filecache)) self::$filecache[$path] = self::decode(file_get_contents($path));
        return self::$filecache[$path];
    }
    
    /**
     * Decode JSON to an array
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.1
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $json JSON text to decode
     * @return array Array parsed from JSON
     */
    public static function decode($json) {
        /*if (!array_key_exists($json, self::$decodecache)) {
            self::$decodecache[$json] = json_decode($json, true);
            var_dump(self::$decodecache[$json]);
        }
        return self::$decodecache[$json];*/
        return json_decode($json, true);
    }
    
    /**
     * Encode JSON and write to a file
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param array $array Array to encode
     * @param string $path Path of file to write
     */
    public static function writefile($array, $path) {
        file_put_contents($path, self::encode($array));
        $filecache[$path] = $array;
    }
    
    /**
     * Encode JSON to a string
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param array $array Array to encode
     * @return string Encoded string from array
     */
    public static function encode($array) {
        return json_encode($array);
    }
    
    /**
     * Clear one or both of the internal JSON caches
     *
     * @author Tom Barham <me@mrifshie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param int $cache Cache to clear
     */
    public static function clearcache(int $cache) {
        if ($cache == Json::CACHE_BOTH) {
            self::$filecache = array();
            self::$decodecache = array();
        } else if ($cache == Json::CACHE_FILE) {
            self::$filecache = array();
        } else if ($cache == Json::CACHE_DECODE) {
            self::$decodecache = array();
        } else throw new InvalidArgumentException("Json::clearcache() expects parameter 1 to be either Json::CACHE_BOTH, Json::CACHE_FILE, or Json::CACHE_DECODE");
    }
}
