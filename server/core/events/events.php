<?php
/**
 * Events manager
 *
 * Allows systems to add/remove events that other systems can process
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package LimePHP.Packages.Events
 */
class Events {
    private static $events = array();
    
    const PRIORITY_LOW = 0;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 4;
    
    /**
     * Add an event handler
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param Handler $handler Handler to add
     */
    public static function add(Handler $handler) {
        if (!array_key_exists($handler->eventname, self::$events)) self::$events[$handler->eventname] = array();
        array_push(self::$events[$handler->eventname], $handler);
    }
    
    /**
     * Remove an event handler
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param mixed $handler Name of event or handler to remove
     */
    public static function remove($handler) {
        foreach (self::$events as $name => $event) {
            if (is_string($handler) && $name == $handler) unset($event);
            foreach ($event as $item) {
                if ($item == $handler) {
                    unset($item);
                    $event = array_values($event);
                }
            }
        }
    }
    
    /**
     * Call an event
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of event to call
     * @param array Arguments to pass to callback (optional)
     */
    public static function call($name, $args = array()) {
        //echo microtime() . " calling $name\n";

        if (!array_key_exists($name, self::$events)) return;
        
        usort(self::$events[$name], function($a, $b) {
            return $a->priority - $b->priority;
        });
        
        
        $ret = array();
        foreach (self::$events[$name] as $handler) {
            $r = call_user_func_array($handler->callback, $args);
            if (!is_null($r)) array_push($ret, $r);
        }
        return $ret;
    }
}