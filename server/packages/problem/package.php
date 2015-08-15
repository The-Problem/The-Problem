<?php

class ProblemPackage implements IPackage {
    public function initialize(Resources &$r) {
        $r->add(array(
            "Libraries" => array(
                "objects"
            ),
            "Modules" => array(
                "terminal"
            ),
            "Templates" => array(
                "default"
            ),
            "Pages" => array(
                "ajax",
                "home",
                "error",
                "signup",
                "signupsuccess"
            )
        ));

        Events::add(new Handler("libadded.pages", function() {
            Library::get("pages");
            Templates::$theme = "material";
        }));

        ob_start();

        Events::add(new Handler("stop", function() {
            ob_end_flush();
        }));

        date_default_timezone_set("UTC");
    }
}