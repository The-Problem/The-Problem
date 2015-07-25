<?php
/**
 * Simple validator class
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2013, mrfishie Studios
 * @package Libraries.Validator
 */
class Validator {
    
    const COMPLEX_PASSWORD = 1;
    const COMPLEX_USERNAME = 2;
    
    private static $types = array(
        "string" => array(__CLASS__, "type_string"),
        "number" => array(__CLASS__, "type_number"),
        "email" => array(__CLASS__, "type_email"),
        "same" => array(__CLASS__, "type_same"),
        "length" => array(__CLASS__, "type_length"),
        "complex" => array(__CLASS__, "type_complex")
    );
    
    private static function type_string($n) { return !empty($n); }
    private static function type_number($n) { return is_numeric($n); }
    private static function type_email($n) { return filter_var($n, FILTER_VALIDATE_EMAIL); }
    private static function type_username($n) { return preg_match("/^\w{5,}$/", $n); }
    private static function type_same($n, $p) { return $n === $p["other"]; }
    private static function type_length($n, $p) {
        $len = strlen($n);
        $isvalid = true;
        if (array_key_exists("minimum", $p) && $len < $p["minimum"]) $isvalid = false;
        else if (array_key_exists("maximum", $p) && $len > $p["maximum"]) $isvalid = false;
        return $isvalid;
    }
    
    private static $complexConstants = array(
        self::COMPLEX_PASSWORD => array(
            "length" => array(
                "min" => 6,
                "max" => 20
            ),
            "allow" => array(
                array(
                    "find" => array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"),
                    "min" => 1,
                ),
                array(
                    "find" => array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"),
                    "min" => 1,
                ),
                array(
                    "find" => array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9"),
                    "min" => 1,
                )
            ),
            "disallow" => array(
                " "
            )
        ),
        self::COMPLEX_USERNAME => array(
            "length" => array(
                "min" => 5
            ),
            "disallow" => array(
                " "
            )
        )
    );
    private static function type_complex($n, $p) {
        $between = function($num, $arr) {
            $defaults = array("min" => 0, "max" => 0);
            $arr = array_merge($defaults, $arr);
            if ($arr["min"] !== 0 && $num < String::percentageval($arr["min"], $num)) return false;
            if ($arr["max"] !== 0 && $num > String::percentageval($arr["max"], $num)) return false;
            return true;
        };
        
        if (is_int($p)) $p = self::$complexConstants[$p];
        
        // Check if the length matches
        if (array_key_exists("length", $p)) {
            $strlen = strlen($n);
            $isarray = is_array($p["length"]);
            
            if (!$isarray && $strlen !== String::percentageval($p["length"], $strlen)) return false;
            if ($isarray && !$between($strlen, $p["length"])) return false;
        }
        
        // Process allow constraints
        if (array_key_exists("allow", $p)) {
            foreach ($p["allow"] as $val) {
                if (!is_array($val)) $val = array("find" => array($val));
                else if (!is_array($val["find"])) $val["find"] = array($val["find"]);
                
                $findcount = 0;
                foreach ($val["find"] as $finditm) {
                    $findcount += substr_count($n, $finditm);
                }
                
                if (!$between($findcount, $val)) return false;
            }
        }
        
        // Process disallow constraints
        if (array_key_exists("disallow", $p)) {
            foreach ($p["disallow"] as $val) {
                if (!is_array($val)) $val = array("find" => array($val));
                else if (!is_array($val["find"])) $val["find"] = array($val["find"]);
                $val = array_merge(array("min" => 1), $val);
                
                $findcount = 0;
                foreach ($val["find"] as $finditm) {
                    if (strpos($finditm, $n) !== false) $findcount++;
                }
                if ($between($findcount, $val)) return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate a string from the specified type
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $text Text to validate
     * @param string $type Type to validate from
     * @param array $options Options to pass to the function
     * @return boolean Whether $text passed the validation
     *
     * @throws InvalidArgumentException when parameter 2 is not an existing type
     */
    public static function validate($text, $type = "string", $options = false) {
        if (!array_key_exists($type, self::$types)) throw new InvalidArgumentException("Validator::validate() parameter 2 must be an existing type.");
        
        $func = self::$types[$type];
        
        $params = array($text);
        if ($options) array_push($params, $options);
        
        return !call_user_func_array($func, $params);
    }
    
    /**
     * Batch validate a series of inputs
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param array $values Values and types to validate
     * @return array Returned messages
     */
    public static function batchvalidate(array $values) {
        $endval = array();
        foreach ($values as $options) {
            $value = $options["value"];
            $type = $options["type"];
            $message = $options["message"];
            
            if (array_key_exists("parameters", $options)) $parameters = $options["parameters"];
            else $parameters = false;
            
            if (self::validate($value, $type, $parameters)) array_push($endval, $message);
        }
        return $endval;
    }
    
    /**
     * Add a validation type
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $name Name of the type
     * @param function $function Validation function
     */
    public static function addtype($name, $function) {
        $types[$name] = $function;
    }
}