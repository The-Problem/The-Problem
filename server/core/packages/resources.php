<?php
/**
 * Allows packages to register resources that can be handled by other packages
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package LimePHP.Packages
 */
class Resources {
    private $resources = array();
    private $processors = array();
    private $arg;
    
    public function add($items) {
        foreach ($items as $type => $list) {
            if (!array_key_exists($type, $this->resources)) $this->resources[$type] = array();
            if (!array_key_exists($this->arg, $this->resources[$type])) $this->resources[$type][$this->arg] = array();
            
            $this->resources[$type][$this->arg] = array_merge($this->resources[$type][$this->arg], $list);
        }
    }
    
    public function addprocessor($type, $function) {        
        if (!array_key_exists($type, $this->processors)) $this->processors[$type] = array();
        array_push($this->processors[$type], $function);
    }
    
    public function process() {
        foreach ($this->processors as $type => $processors) {
            if (array_key_exists($type, $this->resources)) {
                foreach ($this->resources[$type] as $arg => $resources) {
                    foreach ($resources as $resource) {
                        foreach ($processors as $processor) {
                            call_user_func($processor, $resource, $arg);
                        }
                    }
                }
            }
        }
    }
    
    public function setarg($arg) {
        $this->arg = $arg;
    }
}