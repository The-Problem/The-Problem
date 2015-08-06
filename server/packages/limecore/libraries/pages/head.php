<?php
/**
 * Provides the ability to add code and tags to the head
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.1
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.Pages
 */
class Head {
    private $parts = array();
    private $pprocessors = array();
    private $modes = array(
        "color" => "0,0,0"
    );
    
    const PART_GLOBAL = 0;
    const PART_DOCUMENT = 2;
    const PART_AJAX = 4;
    
    /**
     * Title for the page
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @var string Document title
     */
    public $title = "mozzo";
    
    private function gprocessor($n) { return implode("\n", $n); }
    
    /**
     * Consructor for Head
     *
     * Adds some default part processors such as:
     *   stylesheet - For use with stylesheets
     *   script     - For use with javascripts
     *   global     - For adding global tags
     *   document   - For adding document-only tags
     *   ajax       - For adding AJAX-only tags
     *   packages   - For adding Javascript packages
     */
    public function __construct() {
        $GLOBALS['pagehead_title'] = $this->title;
        
        // A hidden processor that is used to add LimePHP JS API things
        $this->addprocessor(uniqid(), self::PART_DOCUMENT, function() {                        
            $code = '<title>' . $GLOBALS['pagehead_title'] . '</title>' . "\n";
            
            // If in IE <9, load jQuery 1.x and JSON2, else load jQuery 2.x
            $code .= '<!--[if lt IE 9]><script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script><script src="//cdnjs.cloudflare.com/ajax/libs/json2/20130526/json2.min.js"></script><![endif]-->' . "\n";
            $code .= '<!--[if gte IE 9]><!--><script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script><!--<![endif]-->' . "\n";
            
            // LimePHP JS API
            $code .= '<script src="' . Path::getclientfolder("res", "js", "lib") . 'limephp.js"></script><script>LimePHP.SERVER["root"]="' . Path::getclientfolder() . '";$(function(){LimePHP.load()})</script>' . "\n";
            
            // Default meta tags
            $code .= '<meta name="generator" content="LimePHP/' . LimePHP::VERSION . '" />' . "\n";
            $code .= '<meta name="application-name" content="LimePHP/' . LimePHP::VERSION . '" />' . "\n";
            $code .= '<meta charset="utf-8" />';
            
            return $code;
        });
        
        $this->addprocessor("package", self::PART_DOCUMENT, function($n) {
            return '<script>LimePHP.packages=' . json_encode($n) . '</script>';
        });
        
        $this->addprocessor("stylesheet", self::PART_GLOBAL, function($n) {
            return array_map(function($itm) {
                if (is_array($itm)) $url = $itm[0];
                else $url = $itm;
                
                return '<link rel="stylesheet" href="' . htmlentities($url) . '" />';
            }, $n);
        });
        
        $this->addprocessor("script", self::PART_GLOBAL, function($n) {
            return array_map(function($itm) {
                return '<script src="' . htmlentities($itm) . '"></script>';
            }, $n);
        });
        
        $this->addprocessor("tag", self::PART_GLOBAL, function($n) {
            return array_map(function($itm) {
                return String::createxml($itm);
            }, $n);
        });
        
        $this->addprocessor("meta", self::PART_DOCUMENT, function($n) {
            return array_map(function($itm) {
                return '<meta name="' . htmlentities($itm["name"]) . '" content="' . htmlentities($itm["content"]) . '" />' . "\n";
            }, $n);
        });
        
        $this->addprocessor("onlibrary", self::PART_GLOBAL, function($n) {
            $kv = array();
            foreach ($n as $itm) {
                if (!array_key_exists($itm["name"], $kv)) $kv[$itm["name"]] = array();
                array_push($kv[$itm["name"]], $itm["script"]);
            }
            
            $result = array();
            foreach ($kv as $library => $scripts) {
                array_push($result, '<script>LimePHP.library(' . Json::encode($library) . ',function(a){' . implode(";", $scripts) . '})</script>');
            }
            return $result;
        });
        
        $gprocessor = array($this, "gprocessor");
        $this->addprocessor("global", self::PART_GLOBAL, $gprocessor);
        $this->addprocessor("document", self::PART_DOCUMENT, $gprocessor);
        $this->addprocessor("ajax", self::PART_AJAX, $gprocessor);
        
        $this->package("lime");
    }
    
    /**
     * Gets or sets a mode
     *
     * @param string $name The name
     * @param mixed $value The value to set to
     * @return mixed The value
     */
    public function mode($name, $value = NULL) {
        if (!is_null($value)) $this->modes[$name] = $value;
        return $this->modes[$name];
    }
    
    /**
     * Adds an item to a part
     *
     * @param string $part The name of the part
     * @param mixed $item The item to add
     */
    public function additem($part, $item) {        
        if (!array_key_exists($part, $this->parts)) $this->parts[$part] = array();
        
        array_push($this->parts[$part], $item);
    }
    
    /**
     * Adds a part processor
     *
     * @param string $part The name of the part
     * @param int $mode The mode for the part
     * @param callable $function The function to be called
     */
    public function addprocessor($part, $mode, $function) {
        $this->pprocessors[$part] =  array(
            "mode" => $mode,
            "function" => $function
        );
    }
    
    /**
     * Finds if a processor exists
     *
     * @param string $part The name of the part
     * @return bool Whether the processor exists
     */
    public function hasprocessor($part) {
        return array_key_exists($part, $this->pprocessors);
    }
    
    /**
     * Gets the code for a specific part using the specified mode
     *
     * @param string $part The name of the part
     * @param int $mode The mode to use
     * @return array The values from the part
     */
    public function getpart($part, $mode) {        
        if (!array_key_exists($part, $this->pprocessors)) throw new InvalidArgumentException("No processor has been added called " . $part);
        if ($this->pprocessors[$part]["mode"] !== $mode && $this->pprocessors[$part]["mode"] !== self::PART_GLOBAL && $mode !== self::PART_GLOBAL) return array();
        if (!array_key_exists($part, $this->parts)) $this->parts[$part] = array();
        
        $func = $this->pprocessors[$part]["function"];
        $result = call_user_func($func, $this->parts[$part]);
        if (!is_array($result)) $result = array($result);
        
        return $result;
    }
    
    /**
     * Gets all parts using the specified mode
     *
     * @param int $mode The mode to use
     * @return array The values from the parts
     */
    public function getparts($mode) {
        $code = array();
        
        foreach ($this->pprocessors as $name => $p) {
            $code = array_merge($code, $this->getpart($name, $mode));
        }
        return $code;
    }
    
    /**
     * Add a stylesheet to the head
     *
     * @param mixed $urls URL(s) to the file(s)
     * @param boolean $absolute Are the URLs absolute?
     * @param array $replacetags Find/replace tags for the file (only works with relative)
     */
    public function stylesheet($urls, $absolute = false, $replacetags = array()) {
        if (gettype($urls) !== "array") $urls = array($urls);
        
        foreach ($urls as $url) {
            if ($absolute) $u = $url;
            else {
                $u = Path::getclientfolder("res", "css") . $url . ".css";
                //$u = Path::addget($u . "/", $replacetags);
            }
            
            $this->additem("stylesheet", array($u, !$absolute));
        }
    }
    
    /**
     * Add a Javascript file to the head
     *
     * @param mixed $urls URL(s) to the file(s)
     * @param boolean $absolute Are the URLs absolute?
     */
    public function script($urls, $absolute = false) {
        if (gettype($urls) !== "array") $urls = array($urls);
        
        foreach ($urls as $url) {
            if ($absolute) $u = $url;
            else $u = Path::getclientfolder("res", "js") . $url . ".js";
            
            $this->additem("script", $u);
        }
    }
    
    /**
     * Add a Javascript package to the document
     *
     * @param string $names The name of the package
     */
    public function package($names) {
        if (gettype($names) !== "array") $names = array($names);
        
        foreach ($names as $name) {
            $this->additem("package", $name);
        }
    }
    
    /**
     * Runs Javascript code when a library has been loaded
     *
     * @param string $library The name of the library
     * @param string $script The script code
     */
    public function onlibrary($library, $script) {
        $this->additem("onlibrary", array(
            "name" => $library,
            "script" => $script
        ));
    }
    
    /**
     * Add a tag directly into the head
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $tag Tag name
     * @param string $text Text inside the tag
     */
    public function tag($tag, $text) {
        $entitytag = htmlentities($tag);
        $this->addcode(sprintf("<%s>%s</%s>", $entitytag, $text, $entitytag));
    }
    
    /**
     * Add code directly into the head
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param string $code Code to be added
     */
    public function addcode($code) {
        $this->additem("global", $code);
    }
    
    /**
     * Get code that has been added
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @return string Code that has been added
     */
    public function getcode() {
        return implode($this->getparts(self::PART_DOCUMENT), "\n");
    }
    
    /**
     * Gets the code that has been added as an array
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @return array Code that has been added as an array
     */
    public function getcodearray() {
        return array_values(array_filter($this->getparts(self::PART_AJAX)));
    }
}
