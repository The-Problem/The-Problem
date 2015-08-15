<?php
/**
 * LimeMore Package
 * Adds some more handy libraries that aren't required by the mozzo core.
 *
 * Resources:
 *      Libraries:
 *          Image
 *          Modules
 *          Password
 *          Users
 *          Validator
 *          Colors
 *      Pages:
 *          Image
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @package LimeCore
 */
class LimemorePackage implements IPackage {
    public function initialize(Resources &$r) {
        $r->add(array(
            "Libraries" => array(
                "image",
                "modules",
                "password",
//                "users",
                "validator",
                "colors"
            ),
            "Pages" => array(
                "image"
            ),
            "Autoload" => array(
                "modules"
            )
        ));
        $r->addprocessor("Modules", "Modules::add");
        
        Events::add(new Handler("pagehead", function($h) {
            // Add head processor for modules
            $h->addprocessor("module", Head::PART_GLOBAL, function($n) {
                if (count($n) > 0) {
                    return '<script>LimePHP.library("modules",function(r){r.loadall(' . json_encode($n) . ', "' . Path::webpath() . '")})</script>';
                } else return '';
            });
            // Allow cross-domain for modules
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
        }));
    }
}