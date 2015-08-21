<?php
/**
 * LimeCore Package
 * Provides core libraries that are required for LimePHP to run. Don't delete it.
 *
 * Resources:
 *      Libraries:
 *          Error
 *          String
 *          Path
 *          Connection
 *          Pages
 *          Timer
 *          JSON
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @package LimeCore
 */
class LimecorePackage implements IPackage {
    public function initialize(Resources &$r) {
        
        $r->add(array(
            "Libraries" => array(
                "error",
                "string",
                "path",
                "connection",
                "pages",
                "timer",
                "json",
                "jobs",
                "http_build_url",
                "cookies"
            ),
            "Templates" => array(
                "ajax",
                "barebones",
                "blank"
            ),
            "Autoload" => array(
                "connection",
                "pages",
                "timer",
                "jobs",
                "cookies"
            )
        ));
        $r->addprocessor("Pages", "Pages::add");
        $r->addprocessor("Templates", "Templates::add");
        $r->addprocessor("Jobs", "Jobs::add");
        
        Events::add(new Handler("start", function() {            
            // Setup some custom headers
            header('X-Powered-By: LimePHP/' . LimePHP::VERSION);
            
            Events::call("presession");
            session_start();
            
            Timer::start();
            Connection::connect();
            Pages::showpagefrompath(Path::getpage());

            $now = new DateTime();
            setcookie("lpla", $now->format(DateTime::W3C), 0, "/", "." . Path::getdomain(), false, true);
            
            Events::call("stop");
            
            Connection::close();
        }));
    }
}
