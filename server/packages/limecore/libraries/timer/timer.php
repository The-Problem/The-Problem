<?php
/**
 * Simple timer class to time scripts
 *
 * @author David Walsh <davidwalsh.name>
 * @version 1.0
 * @package Timer
 */
class Timer {
    private static $start;
    private static $pausetime;
    
    /**
     * Start the timer
     *
     * @author David Walsh <davidwalsh.name>
     * @version 1.0
     */
    public static function start() {
        self::$start = self::gettime();
        self::$pausetime = 0;
    }
    
    /**
     * Pause the timer
     *
     * @author David Walsh <davidwalsh.name>
     * @version 1.0
     */
    public static function pause() {
        self::$pausetime = self::gettime();
    }
    
    /**
     * Unpause the timer
     *
     * @author David Walsh <davidwalsh.name>
     * @version 1.0
     */
    public static function unpause() {
        self::$start += (self::gettime() - self::$pausetime);
        self::$pausetime = 0;
    }
    
    /**
     * Get the time from the timer
     *
     * @author David Walsh <davidwalsh.name>
     * @version 1.0
     * @param int $decimals Decimals for the returned value
     * @return float Amount of time timer has been running for
     */
    public static function get($decimals = 8) {
        return round((self::gettime() - self::$start), $decimals);
    }
    
    /**
     * Get the current time in seconds
     *
     * @author David Walsh <davidwalsh.name>
     * @version 1.0
     * @return float Current time in seconds
     */
    public static function gettime() {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}