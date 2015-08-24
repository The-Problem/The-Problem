<?php
/**
 * String utility functions
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.3
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.String
 */
class String {
    /**
     * Trim string from start of string
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $str String to trim
     * @param string $prefix String to remove from start
     * @return string Original string with prefix removed
     */
    public static function trimstart($str, $prefix) {
        if (substr($str, 0, strlen($prefix)) == $prefix) return substr($str, strlen($prefix));
        else return $str;
    }
    
    /**
     * Trim string from end of string
     *
     * @param string $str String to trim
     * @param string $suffix String to remove from end
     * @return string Original string with suffix removed
     */
    public static function trimend($str, $suffix) {
        if (substr($str, -strlen($suffix)) === $suffix) return substr($str, 0, -strlen($suffix));
        else return $str;
    }
    
    /**
     * Replace bracket variables with specified values
     *
     * E.g. if string is "my {SOMETHING} string", and
     *      array( "something" => "awesome" )
     *      is passed into the function, the returned value will be
     *      "my awesome string"
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $text Text to process
     * @param array $items Items to replace
     * @return string Original string with items replaced
     */
    public static function brackets($text, array $items) {
        foreach ($items as $name => $replace) {
            $text = str_replace("{" . strtoupper($name) . "}", $replace, $text);
        }
        return $text;
    }
    
    /**
     * Implodes an array with before/after strings
     *
     * @param array $array The array to process
     * @param string $prepend Text to prepend. Default is empty
     * @param string $append Text to append. Default is empty
     */
    public static function implode($array, $prepend, $append) {
        $out = "";
        foreach ($array as $itm) {
            $out .= $prepend . $itm . $append;
        }
        return $out;
    }
    
    /**
     * Implodes an array with a different end string
     *
     * E.g. $arr = array( "one", "two", "three" );
     *      String::fancyimplode($arr, ", ", " and ");
     *      Results in:
     *      'one, two and three'
     *
     * @param array $array The array to process
     * @param string $first The separator for all but the last elements
     * @param string $last The separator for the last elements
     * @return string Imploded string
     */
    public static function fancyimplode($array, $first = ", ", $last = " and ") {
        array_push($array, implode($last, array_splice($array, -2)));
        return implode($first, $array);
    }
    
    /**
     * Version of implode() function for associative arrays
     *
     * E.g. if the array array( "name" => "LimePHP", "version" => "1.0" ),
     *      the string "=" is passed in for $inside,
     *      and the string "," is passed in for $outside,
     *      the return value will be
     *      "name=LimePHP,version=1.0"
     * 
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param array $array Associative array to process
     * @param string $inside Value to be placed between key and value
     * @param string $outside Value to be placed around each item
     * @return string Imploded string
     */
    public static function associmplode($array, $inside, $outside) {
        $out = array();
        foreach ($array as $key => $value) {
            array_push($out, $key . $inside . $value);
        }
        $out = implode($outside, $out);
        return $out;
    }
    
    /**
     * Version of explode() function for associative arrays
     *
     * Works like associmplode, in reverse.
     * E.g. assocexplode("author=Tom Barham,email=me@mrfishie.com", "=", ",")
     *      will output
     *      array( "author" => "Tom Barham", "email" => "me@mrfishie.com" )
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $string String to process
     * @param string $inside Value inbetween array keys and values
     * @param string $outside Value between each item
     * @return array Exploded associative array
     */
    public static function assocexplode($string, $inside, $outside) {
        $items = explode($outside, $string);
        $ret = array();
        foreach ($items as $itm) {
            $prop = explode($inside, $itm);
            $ret[$prop[0]] = $prop[1];
        }
        return $ret;
    }
    
    /**
     * Captures output from a function
     *
     * @param callable $func The function to call
     * @return string The output from the function
     */
    public static function getoutput($func) {
        ob_start();
        $func();
        return ob_get_clean();
    }
    
    /**
     * Tags a string by surrounding certain segments with links
     *
     * The segments should be formatted like this:
     *
     * array(
     *     "offset" => 0,            // start position
     *     "length" => 10,           // length of text
     *     "text" => "<a href ... >" // text to place
     * )
     *
     * If an 'items' property is supplied instead of 'text', the items
     * will be concatenated with String::fancyimplode and inserted at
     * the location.
     *
     * @param string $text The original text
     * @param array $tags The segments
     * @return string The final text
     */
    public static function tag($text, $tags) {
        $textOffset = 0;
        foreach ($tags as $tag) {
            $offset = $tag["offset"];
            $length = $tag["length"];
            if (array_key_exists("items", $tag)) $innerText = self::fancyimplode($tag["items"]);
            else $innerText = $tag["text"];
            
            $text = substr_replace($text, $innerText, $offset + $textOffset, $length);
            $textOffset += strlen($innerText) - $length;
        }
        return $text;
    }
    
    /**
     * Autolinks a piece of text
     *
     * The protocol, query, and hash sections of the URL are removed.
     *
     * Based off http://code.seebz.net/p/autolink-php/ and http://css-tricks.com/snippets/php/find-urls-in-text-make-links/#comment-82453
     *
     * @param string $str The URL
     * @param array $attributes Attributes to add to the link
     */
    public static function autolink($str, $attributes = NULL) {
        if (is_null($attributes)) $attributes = array(
            "rel" => "nofollow"
        );
        
        $attrs = '';
	    foreach ($attributes as $attribute => $value) {
	        $attrs .= " {$attribute}=\"{$value}\"";
	    }
	 
	    $str = ' ' . $str;
	    $str = preg_replace(
	        '`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i',
	        '$1<a href="$2"'.$attrs.'>$2</a>',
	        $str
	    );
	    $str = substr($str, 1);
	     
	    return $str;
    }
    
    /**
     * Creates XML code out of an array.
     *
     * The array should be formatted like this:
     * array(
     *     "name" => "div",
     *     "attributes" => array(
     *         "style" => "color:#FFF;",
     *         "width" => "50",
     *         "height" => "50"
     *     ),
     *     "contents" => array(
     *         "Hello, ",
     *         array(
     *             "name" => "span",
     *             "contents" => "Bob"
     *         ),
     *         array(
     *             "tag" => "span class='exclamation'",
     *             "contents" => "!"
     *         )
     *     )
     * )
     *
     * The 'contents' property can either be a string or array. If it is
     * a string, the contents of it will be placed inside the element.
     * If it is an array, each item will be placed inside in order. Any
     * strings will be html-encoded. If one of the elements is another array,
     * it will be inserted like another tag.
     *
     * If the 'tag' property is supplied on a tag, but no 'name' property,
     * the function will try to figure out the tag name for the ending tag.
     * 
     * The above example will output something similar to the following code:
     *
     * <div style="color:#FFF;" width="50" height="50">
     *     Hello, <span>Bob</span><span class='exclamation'>!</span>
     * </div>
     *
     * Note: the actual result will be minified (no extra newlines)
     *
     * If a tag contents no contents property, it will bbe outputted as a
     * 'dual' tag. For example, the following array:
     *
     * array(
     *     "name" => "img",
     *     "attributes" => array(
     *         "src" => "path/to/image.png",
     *         "width" => "500",
     *         "height" => "100"
     *     )
     * )
     *
     * Will output:
     *
     * <img src="path/to/image.png" width="500" height="500" />
     *
     * If contents is supplied but the string is empty, the tag will still
     * be left with a closing tag.
     *
     * @param mixed $tag The tag array, or a string, which will be html-encoded
     * @return string The final tag
     */
    public static function createxml($tag) {
        if (is_string($tag)) return htmlentities($tag);
        if (!is_array($tag)) throw new InvalidArgumentException("String::createtag() expects parameter 1 to be an array, " . gettype($tag) . " given");
        
        $text = "<";
        if (array_key_exists("name", $tag)) $text .= htmlentities($tag["name"]);
        if (array_key_exists("attributes", $tag) && is_array($tag["attributes"]) && count($tag["attributes"])) {
            $text .= " ";
            $attributelist = array();
            foreach ($tag["attributes"] as $name => $value) {
                array_push($attributelist, htmlentities($name) . '="' . htmlentities($value) . '"');
            }
            $text .= implode(" ", $attributelist);
        }
        if (array_key_exists("tag", $tag)) {
            if (count($text) > 1) $text .= " ";
            $text .= $tag["tag"];
        }
        
        if (array_key_exists("contents", $tag)) {
            $text .= ">";
            
            if (!is_array($tag["contents"])) $text .= $tag["contents"];
            else {
                foreach ($tag["contents"] as $itm) {
                    $text .= self::createxml($itm);
                }
            }
            
            $text .= "<";
            if (array_key_exists("name", $tag)) $text .= htmlentities($tag["name"]);
            else if (array_key_exists("tag", $tag)) {
                $exploded = explode(" ", $tag["tag"]);
                if (count($exploded)) $text .= $exploded[0];
            }
            $text .= ">";
        } else $text .= " />";
        
        return $text;
    }
    
    /**
     * Converts a number to a human-readable size
     *
     * The $stages parameter should be formatted like this:
     *
     * array(
     *     " million" => array(    // The name to add after the number (notice the space)
     *         "size" => 1000000, // The size of the number
     *         "precision" => 1   // The amount of precision
     *     )
     * )
     *
     * @param int $num The number to convert
     * @param array $stages The stages to convert. Default has million and billion
     * @return string The final number
     */
    public static function readablenumber($num, $stages = NULL) {
        if (is_null($stages)) $stages = array(
            "k" => array(
                "size" => 1000,
                "precision" => 1
            ),
            "m" => array(
                "size" => 1000000,
                "precision" => 2
            ),
            "b" => array(
                "size" => 1000000000,
                "precision" => 3
            )
        );
        
        $names = array_keys($stages);
        $values = array_values($stages);
        
        if (!count($values) || $num < $values[0]["size"]) return number_format($num, 0);
        else {
            foreach ($values as $i => $stage) {
                if ($num >= $stage["size"] && (count($values) === $i + 1) || $num < $values[$i + 1]["size"]) {
                    return number_format($num / $stage["size"], $stage["precision"]) .  $names[$i];
                }
            }
        }
    }
    
    /**
     * Converts a number formatted as a percentage (e.g. '50%') to a real number.
     * Normal numbers are ignored and returned
     *
     * @param mixed $percentage The percentage, or a plain number
     * @param mixed $base The base number to find the percentage of
     * @return float The final number
     */
    public static function percentageval($percentage, $base) {
        if (is_numeric($percentage)) return floatval($percentage);
        if (!is_string($percentage) || $percentage[strlen($percentage) - 1] !== "%") return 0;
        
        $number = floatval(substr($percentage, 0, -1));
        $multiplier = $number / 100;
        return $base * $multiplier;
    }
    
    /**
     * Formats a DateTime as a nice string
     *
     * Results look like this: "Wednesday 6 of August, 2014 at 10:26:09 AM"
     *
     * @param DateTime $date The datetime to format
     * @return string The formatted datetime
     */
    public static function dateformat(DateTime $date) {
        $readableTime = $date->format("l j F, Y");
        $readableTime .= " at ";
        $readableTime .= $date->format("g:i A");
        return $readableTime;
    }

    public static function timeago($pastTime){
        $second = 1;
        $minute = 60;
        $hour = $minute * 60;
        $day = $hour * 24;
        $week = $day * 7;
        $month = $week * 4;
        $year = $month * 12;

        $timeDifference = time() - strtotime($pastTime);

        if ($timeDifference < $minute){
            $output = "Just now";
        }else if ($timeDifference < $hour){
            $output = round($timeDifference/$minute) . " minute";
        }else if ($timeDifference < $day){
            $output = round($timeDifference/$hour) . " hour";
        }else if ($timeDifference < $week){
            $output = round($timeDifference/$day) . " day";
        }else if ($timeDifference < $month){
            $output = round($timeDifference / $week) . " week";
        }else if ($timeDifference < $year){
            $output = round($timeDifference / $month) . " month";
        }else{
            $output = round($timeDifference / $year) . " year";
        }

        if (substr($output, 0, 2) != "1 " && $output != "Just now"){
            $output .= "s ago";
        }else if (substr($output, 0, 2) == "1 " && $output != "Just now"){
            $output .= " ago";
        }

        return $output;
    }
}
