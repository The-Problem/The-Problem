<?php
class Parser {
    static $mentions = "/@([^@ ]+)/";
    static $hashtags = "/([\\w-]*)#(\\d+)/";

    static function parse($value, $user_from, $properties) {
        /**
         * Properties are:
         *
         * array(
         *     "parent_object_id" => Object ID of the parent
         *     "current_object_id" => Object ID of the thing with the $value
         *     "section_slug" => Slug of the parent section
         * )
         */

        $value = self::convert_mentions($value, $user_from, $properties);
        $value = self::convert_hashtags($value, $user_from, $properties);
        return self::convert_markdown($value);
    }

    static function convert_mentions($value, $user_from, $properties) {
        /**
         * Properties are:
         *
         * array(
         *     "parent_object_id" => Object ID of the parent
         *     "current_object_id" => Object ID of the thing with the $value
         * )
         */

        $mentions = array();
        preg_match_all(self::$mentions, $value, $mentions);

        $items = array_unique($mentions[1]);
        foreach ($items as $name) {
            // get the user
            $user = Connection::query("SELECT Username FROM users WHERE Username = ?", "s", array($name));

            if (count($user)) {
                // trigger a notification for the user
                Connection::query("INSERT INTO notifications
                                     (Triggered_By, Received_By, Target_One, Target_Two, Creation_Date, Type)
                              VALUES (           ?,           ?,          ?,          ?,             ?,    3)", "ssiis", array(
                    $user_from, $name, $properties["parent_object_id"], $properties["current_object_id"], date('Y-m-d H:i:s')
                ));

                // replace instances in the text
                $value = str_ireplace("@$name", "[@$name](" . Path::getclientfolder("~$name") . ")", $value);
            }
        }

        return $value;
    }

    static function convert_hashtags($value, $user_from, $properties) {
        /**
         * Properties are:
         *
         * array(
         *     "section_slug" => Slug of the parent section
         * )
         */

        $hashtags = array();
        preg_match_all(self::$hashtags, $value, $hashtags);

        $section_names = $hashtags[1];
        $bug_ids = $hashtags[2];

        $bugs = array();
        foreach ($bug_ids as $k => $id) {
            $section_name = $section_names[$k];

            if (!strlen($section_name)) {
                $section_name = $properties["section_slug"];
                array_push($bugs, array(strtolower($section_name), $id, "#$id"));
            }

            array_push($bugs, array(strtolower($section_name), $id, "$section_name#$id"));
        }

        $unique_bugs = array_unique($bugs);
        foreach ($unique_bugs as $b) {
            $value = str_ireplace($b[2], "[" . $b[2] . "](" . Path::getclientfolder("bugs", $b[0], $b[1]) . ")", $value);
        }

        return $value;
    }

    static function convert_markdown($value) {
        Library::get('parsedown');
        $parsedown = new Parsedown();
        return $parsedown->text($value);
    }
}