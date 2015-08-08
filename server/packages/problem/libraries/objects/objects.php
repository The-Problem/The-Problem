<?php

class Objects {
    private static $cache = array();

    public static function query($query, $types = false, $args = false) {
        Library::get("connection");
        $items = Connection::query($query, $types, $args);
        if ($items) return self::to_object_list($items);
        return array();
    }

    public static function to_object_list($items) {
        return array_map(function($item) {
            if (array_key_exists($item['Object_ID'], self::$cache)) return self::$cache[$item['Object_ID']];

            $new_obj = new Object($item);
            self::$cache[$item['Object_ID']] = $new_obj;
            return $new_obj;
        }, $items);
    }
}