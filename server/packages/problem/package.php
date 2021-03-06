<?php

class ProblemPackage implements IPackage {
    public function initialize(Resources &$r) {
        $r->add(array(
            "Libraries" => array(
                "objects",
                "users",
                "notifications",
                "parsedown",
                "parser"
            ),
            "Modules" => array(
                "terminal",
                "sectionTile",
                "notification",
                "adminSection",
                "comments",
                "comment",
                "headerBar",
                "loggedInHome",
                "userDetails"
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
                "users",
                "bugs"
            ),
            "Jobs" => array(
                "importDB",
                "pullFromRepo"
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