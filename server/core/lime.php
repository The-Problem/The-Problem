<?php
/**
 * Core LimePHP manager
 *
 * Automagically loads required files and packages, and then runs everything.
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, mrfishie Studios
 * @package LimePHP
 */
class LimePHP {
    public static $root;
    
    private static $requiredfiles = array(
        "core/events/events.php",
        "core/events/handler.php",
        "core/packages/packages.php",
        "core/packages/ipackage.php",
        "core/packages/resources.php",
        "core/default/path.php",
        "core/default/response.php",
        "core/default/library.php",
        "core/default/logger.php",
    );
    
    /**
     * The version of LimePHP
     */
    const VERSION = "0.1";
    
    /**
     * Start everything
     *
     * If $packages is not set, all packages will be used instead
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, mrfishie Studios
     */
    public static function start($packages = null) {
        try {
            self::initialize($packages);
            
            Events::call("start");
        } catch (Exception $ex) {
            self::error($ex);
        }
    }
    
    /**
     * Initializes LimePHP
     *
     * @param array $packages The packages to load, default is all.
     */
    public static function initialize($packages = null) {
        try {
            if (function_exists('xdebug_disable')) {
                xdebug_disable();
            }
            
            set_error_handler(function($errno, $errstr, $errfile, $errline) {
                if ($errno > E_USER_NOTICE) throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });
            
            set_include_path(self::$root = realpath(dirname(dirname(__FILE__))));
            
            self::loadrequiredfiles();
            Logger::start();
            
            if (is_null($packages)) $packages = Packages::discover();
            Packages::load($packages);
            
            spl_autoload_register(function($class) {
                trigger_error("The class $class was not found and is being autoloaded as a package", E_USER_NOTICE);
                Library::get(strtolower($class));
            });
        } catch (Exception $ex) {
            self::error($ex);
        }
    }
    
    /**
     * Finds if we are running in a CLI
     *
     * @return boolean Whether the code is running in a CLI
     */
    public static function inCommandLine() {
        return (php_sapi_name() === 'cli');
    }
    
    private static function error($ex) {
        Events::call("error");
        
        if (Library::exists("error")) {
            Library::get("error");
            Error::report(E_ERROR, $ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        } else throw $ex;
    }
    
    private static function loadrequiredfiles() {
        foreach (self::$requiredfiles as $file) {
            include($file);
        }
    }
}