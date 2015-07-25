<?php
/**
 * Represents an event handler
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package LimePHP.Packages.Events
 */
class Handler {
    public $eventname;
    public $callback;
    public $priority;
    
    /**
     * Constructor for Handler
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $eventname Name for event to add to
     * @param callable $callback Callback for when handler is fired
     * @param int $priority Priority for 
     */
    public function __construct($eventname, $callback, $priority = 0) {
        $this->eventname = $eventname;
        $this->callback = $callback;
        $this->priority = $priority;
    }
}