<?php
/**
 * Jobs Library
 *
 * Allows execution of 'Jobs'
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 */
class Jobs {
    private static $jobs = array();
    private static $loaded = array();
    
    public static function add($name, $dir) {
        if (array_key_exists($name, self::$jobs)) throw new Exception("Job called " . $name . " already exists in " . self::$jobs[$name] . " while processing library " . basename($dir));
        
        self::$jobs[$name] = $dir;
        
        Events::call("jobadded", array($name));
    }
    
    public static function execute($job, $args) {
        $class = self::load($job);
        
        ob_start();
        $class->startexecute($args, $options);
        Events::call("job.$job");
        Events::call("job");
        $class->endexecute();
        
        Library::get("string");
        $c = array_filter(explode("\n", ob_get_clean()));
        echo String::implode($c, "[" . ucwords($job) . "] ", "\n");
    }
    
    private static function load($job) {
        $classname = ucwords($job) . "Job";
        if (!array_key_exists($job, self::$loaded)) {
            if (!array_key_exists($job, self::$jobs)) throw new Exception("The job called $job does not exist");
            
            $path = self::$jobs[$job];
            l_include(Path::implodepath($path, "jobs", $job . ".php"), false);
            if (!class_exists($classname)) throw new Exception("Cannot find class called $classname for job $job");
            if (!in_array("IJob", class_implements($classname))) throw new Exception("Class $classname does not implement IJob for job $job");
            
            self::$loaded[$job] = new $classname();
        }
        return self::$loaded[$job];
    }
}
