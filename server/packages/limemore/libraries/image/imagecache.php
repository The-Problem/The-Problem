<?php
/**
 * Cache for different variants of images with different effects
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.1
 * @copyright Copyright (c) 2014, Tom Barham
 * @package Libraries.Image
 */
class ImageCache {
    private $map;
    private $properties;
    private $file;
    
    /**
     * Constructor for ImageCache class
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     */
    public function __construct() {
        $this->file = Path::getserverfolder("cache", "images") . "map.json";
        if (!file_exists($this->file)) $this->map = array();
        else $this->map = Json::readfile($this->file);
        
        $this->properties = array();
        foreach ($this->map as $cat => $catval) {
            foreach ($catval as $name => $variants) {
                foreach ($variants as $props => $id) {
                    $this->properties[$id] = String::assocexplode($props, "=", ",");
                }
            }
        }
    }
    
    /**
     * Save modified files JSON files
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     */
    public function save() {
        Json::writefile($this->map, $this->file);
    }
    
    /**
     * Find if a category, image, or image variant is in the cache
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $cat Category to check
     * @param string $name Name to check. If this is excluded, only the category will be checked
     * @param mixed $properties Properties for image variant, as a string or array. If this is excluded, only the image will be checked
     * @return bool Whether the category, image, or image variant exists in the cache
     */
    public function incache($cat, $name = null, $properties = null) {
        
        $in = array_key_exists($cat, $this->map);
        if ($in && !is_null($name)) {
            $in = array_key_exists($name, $this->map[$cat]);
            if ($in && !is_null($properties)) {
                $properties = $this->getproperties($properties);
                $in = array_key_exists($properties, $this->map[$cat][$name]);
            }
        }
                
        return $in;
    }
    
    /**
     * Adds the specified image variant to the cache map
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $cat Category for image
     * @param string $name Name of image
     * @param mixed $properties Properties for image variant, as a string or array
     * @return int Cache ID of the image
     */
    public function setupcache($cat, $name, $properties) {
        $properties = $this->getproperties($properties);
        $id = null;
        
        if (!$this->incache($cat, $name, $properties)) {
            if (!$this->incache($cat, $name)) {
                if (!$this->incache($cat)) $this->map[$cat] = array();
                $this->map[$cat][$name] = array();
            }
            
            $id = uniqid(uniqid("", true) . ".", true);
            $this->map[$cat][$name][$properties] = $id;
            $this->properties[$id] = String::assocexplode($properties, "=", ",");
            
        } else $id = $this->map[$cat][$name][$properties];
                
        return $id;
    }
    
    /**
     * Makes, processes, and saves the image variant from the source file
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $cat Category for image
     * @param string $name Name of image
     * @param mixed $properties Properties for image variant, as a string or array
     */
    public function makeimage($cat, $name, $properties) {
        $properties = $this->getproperties($properties);
        $path = $this->getfile($cat, $name, $properties);
        
        if (!file_exists($path["source"])) throw new Exception("Can't find source image for '" . $cat . "/" . $name . "' (path is '" . $path["source"] . "')");
        
        $processor = new ImageProcessor();
        
        $effects = $this->properties[$this->map[$cat][$name][$properties]];
        $image = $processor->batch($path["source"], $effects);
        $image->stripImage();
        
        file_put_contents($path["cache"], $image);
    }
    
    /**
     * Get file paths for the image and image variant
     *
     * Returns an associative array structured like the following:
     * array(
     *    "cache" => Location of image variant in the cache
     *    "source" => Location of the source image
     * )
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $cat Category for image
     * @param string $name Name of image
     * @param string $properies Properties for image variant, as a string or array
     * @return array Associative array containing location of image variant in cache, and location of source image
     */
    public function getfile($cat, $name, $properties) {
        $properties = $this->getproperties($properties);
        
        $id = $this->map[$cat][$name][$properties];
        
        $props = $this->properties[$id];
        
        return array(
            "cache" => Path::getserverfolder(array("cache", "images", "images")) . $id . "." . (array_key_exists("format", $props) ? $props["format"] : $props["original"]),
            "source" => Path::getserverfolder(array("cache", "images", "source", $cat)) . urlencode($name) . "." . (array_key_exists("original", $props) ? $props["original"] : $props["format"]),
        );
    }
    
    /**
     * Adds the specified image variant to the cache and returns location
     *
     * Completely automates cache operation, by setting up the cache, making the image, and then saving.
     * This is normally the function that you would use if you want to quickly add an image to the cache.
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $cat Category for image
     * @param string $name Name of image
     * @param mixed $properties Properties for image variant, as a string or array
     * @return string File path of image variant in the cache
     */
    public function get($cat, $name, $properties) {
        $properties = $this->getproperties($properties);
        
        
        if (!$this->incache($cat, $name, $properties)) {
            $this->setupcache($cat, $name, $properties);
            $this->makeimage($cat, $name, $properties);
            $this->save();
        }
        
        $files = $this->getfile($cat, $name, $properties);
        return $files["cache"];
    }
    
    /**
     * Converts the properties to a property string
     *
     * @param mixed $prop Either a string or array
     * @return string The property string
     */
    private function getproperties($prop) {
        if (is_array($prop)) $prop = String::associmplode($prop, "=", ",");
        return $prop;
    }
}
