<?php
define('LIME_ENV_DEV', 1);
define('LIME_ENV_PROD', 2);

define('LIME_TERMINAL_ENABLED', 1);
define('LIME_TERMINAL_DISABLED', 0);

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
    public static $config = array();
    
    /**
     * The version of LimePHP
     */
    const VERSION = "0.2.0";
    
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

            l_include_flush();
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
            self::$root = realpath(dirname(__DIR__));
            include('include.php');
            include(__DIR__ . '/../profile.php');

            if (function_exists('xdebug_disable')) {
                xdebug_disable();
            }
            
            set_error_handler(function($errno, $errstr, $errfile, $errline) {
                if ($errno > E_USER_NOTICE) throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });

            self::loadrequiredfiles();
            Events::call("loaded");
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
        l_include("core/events/events.php");
        l_include("core/events/handler.php");
        l_include("core/packages/packages.php");
        l_include("core/packages/ipackage.php");
        l_include("core/packages/resources.php");
        l_include("core/default/path.php");
        l_include("core/default/response.php");
        l_include("core/default/library.php");
        l_include("core/default/logger.php");
    }
}