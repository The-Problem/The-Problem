<?php

class ProblemPackage implements IPackage {
    public function initialize(Resources &$r) {
        $r->add(array(
            "Libraries" => array(
                "objects",
                "users"
            ),
            "Modules" => array(
                "terminal",
                "sectionTile",
                "notification"
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
                "login"
            )
        ));

        Events::add(new Handler("libadded.pages", function() {
            Library::get("pages");
            Templates::$theme = "material";
        }));
        Events::add(new Handler("pagehead", function($head) {
            $title_res = Connection::query("SELECT Value FROM configuration WHERE Type = 'overview-name' AND Name = 'sitename'");
            if (!count($title_res)) $title = "The Problem";
            else $title = $title_res[0]["Value"];

            $head->title = $title;
        }));

        ob_start();

        Events::add(new Handler("stop", function() {
            ob_end_flush();
        }));

        date_default_timezone_set("UTC");
    }
}