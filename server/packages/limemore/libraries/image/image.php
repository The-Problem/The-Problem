<?php
/**
 * Cached image handling with support for Imagick
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 2.5
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.Image
 */

class Image {
    public $clientpath = "";
    public $serverpath = null;
    public $sourcepath = null;
    
    private $cat;
    private $name;
    private $originalName;
    
    private $properties;
    private $cache;
    
    private $sourceexists = NULL;
    
    /**
     * Constructor for Image class
     *
     * If $cat is 'url', $name will be used as a URL and will be copied
     * to the source folder in the image cache.
     *
     * The filename will be trimmed to be the length specified as $maxlen,
     * to avoid names that are too long for the filesystem to handle.
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $cat Category for image
     * @param string $n Name of image
     * @param mixed $properties Properties for image variant, as a string or array
     * @param int $maxlen The maximum length for the name, if the image is a URL. Default is 100
     */
    public function __construct($cat, $n, $properties, $maxlen = 100) {
        $originalName = $n;
        $name = substr($n, max(strlen($n) - $maxlen, 0), $maxlen);
        
        if (!is_array($properties)) $properties = String::assocexplode($properties, "=", ",");
        
        if (!array_key_exists("format", $properties)) $properties["format"] = $properties["original"];
        
        $encodedName = urlencode($name);
        
        if ($cat === "url") $properties["original"] = "uim";
        $this->sourcepath = Path::getserverfolder("cache", "images", "source", $cat) . $encodedName . "." . (array_key_exists("original", $properties) ? $properties["original"] : $properties["format"]);
        
        $this->properties = $properties;
        
        $this->cat = $cat;
        $this->name = $name;
        $this->originalName = $originalName;
        
        $url = Path::webpath();
        
        $urlName = urlencode($originalName);
        $append = "img." . (array_key_exists('format', $properties) ? $properties['format'] : $properties['original']);
        
        if ($cat === "url") {
            $append = "?path=" . $urlName;
            $urlName = "path";
        }
        
        $this->clientpath = Path::getclient(array("image", $cat, $urlName, String::associmplode($properties, "=", ",")), $url) . $append;
    }
    
    /**
     * Finds if the image source exists
     *
     * @return bool Whether the source exists
     */
    public function exists() {
        if (is_null($this->sourceexists)) {
            $this->sourceexists = file_exists($this->sourcepath);
            if ($this->cat === "url" && !$this->sourceexists) $this->sourceexists = Path::exists($this->name);
        }
        return $this->sourceexists;
    }
    
    /**
     * Load an image if it is from a URL
     */
    public function load() {
        if ($this->cat === "url" && !file_exists($this->sourcepath)) Path::download($this->originalName, $this->sourcepath);
    }
    
    /**
     * Create variant image if not in the cache
     *
     * Also sets Image::serverpath to the cached image location
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @return string Server path to cached image
     */
    public function process() {
        if (is_null($this->serverpath)) {
            if (is_null($this->cache)) $this->cache = new ImageCache();
            $this->serverpath = $this->cache->get($this->cat, $this->name, $this->properties);
        }
        return $this->serverpath;
    }
    
    /**
     * Get an image property
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of property
     * @return string Value of property
     */
    public function property($name) {
        return $this->properties[$name];
    }
    
    /**
     * Creates a GD image from any popular image type
     *
     * @param string $path The path to the image
     * @return Image The image object
     */
    public static function createfrom($path) {
        $type = exif_imagetype($path);
        
        $allowedTypes = array(
            1, // gif
            2, // jpg
            3, // png
        );
        if (!in_array($type, $allowedTypes)) return false;
        switch ($type) {
            case 1: $im = imagecreatefromgif($path); break;
            case 2: $im = imagecreatefromjpeg($path); break;
            case 3: $im = imagecreatefrompng($path); break;
        }
        return $im;
    }
}
