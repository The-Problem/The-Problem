<?php
/**
 * Stores secure 'cookies' in the database
 *
 * Can be used for sessions
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 */
class Cookies {
    private static $cookies = array();
    
    /**
     * The default session cookie name
     *
     * @var string
     */
    public static $sessionname = "limesession";
    
    /**
     * The default timeout for session cookies in seconds
     *
     * @var DateInterval
     */
    public static $sessiontimeout;
    
    /**
     * Gets a cookie from a database
     *
     * @param string $name The name of the cookie
     * @return Cookie The cookie
     */
    public static function get($name) {
        if (!is_string($name)) return $name;
        if (!array_key_exists($name, self::$cookies)) self::$cookies[$name] = new Cookie($name);
        return self::$cookies[$name];
    }
    
    /**
     * Gets a cookie to use as a session
     *
     * @return Cookie The session cookie
     */
    public static function session() {
        return self::get(self::$sessionname);
    }
    
    /**
     * Gets or sets a property on the session cookie
     *
     * @param string $key The key for the property
     * @param mixed $value The value to set the property to
     * @param Cookie $cookie A cookie to do the operation on, default is session
     * @return mixed The value of the property
     */
    public static function prop($key, $value = NULL, $cookie = NULL) {
        if (is_null($cookie)) $cookie = self::session();
        else $cookie = self::get($cookie);
        
        $cookieVal = $cookie->value();
        if (!is_null($value)) {            
            $cookieVal[$key] = $value;
            $cookie->value($cookieVal);
        }
        
        return $cookieVal[$key];
    }
    
    /**
     * Finds if the session cookie has the property specified
     *
     * @param string $key The key for the property
     * @param Cookie $cookie A cookie to do the operation on, default is session
     * @return bool Whether the session cookie has the property specified
     */
    public static function exists($key, $cookie = NULL) {
        if (is_null($cookie)) $cookie = self::session();
        else $cookie = self::get($cookie);
        
        $cookieVal = $cookie->value();
        return array_key_exists($key, $cookieVal);
    }
    
    /**
     * Removes a property from a cookie
     *
     * @param string $key The key for the property
     * @param Cookie $cookie A cookie to do the operation on, default is session
     */
    public static function remove($key, $cookie = NULL) {
        if (is_null($cookie)) $cookie = self::session();
        else $cookie = self::get($cookie);
        
        $cookieVal = $cookie->value();
        unset($cookieVal[$key]);
        $cookie->value($cookieVal);
    }
    
    /**
     * Flushes the cache on all of the cookies
     *
     * @return int The amount of cookies flushed
     */
    public static function flushCache() {
        $cookieCount = count(self::$cookies);
        foreach (self::$cookies as $cookie) {
            $cookie->flushCache();
        }
        return $cookieCount;
    }
}

Cookies::$sessiontimeout = new DateInterval('PT1800S');