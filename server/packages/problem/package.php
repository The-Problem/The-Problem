<?php

class ProblemPackage implements IPackage {
    public function initialize(Resources &$r) {
        $r->add(array(
            "Libraries" => array(
                "objects",
                "users",
                "notifications"
            ),
            "Modules" => array(
                "terminal",
                "sectionTile",
                "notification",
                "adminSection"
            ),
            "Templates" => array(
                "default"
            ),
            "Pages" => array(
                "ajax",
                "home",
                "admin",
                "sudo",
                "error",
                "signup",
                "login",
                "users"
            )
        ));

        Events::add(new Handler("libadded.pages", function() {
            Library::get("pages");
            Templates::$theme = "material";
        }));
        Events::add(new Handler("pagehead", function($head) {

        }));

        ob_start();

        Events::add(new Handler("stop", function() {
            ob_end_flush();
        }));

        date_default_timezone_set("UTC");
    }
}