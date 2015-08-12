<?php
/**
 * Processes effects on Imagick images
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package Libraries.Image
 */
class ImageProcessor {
    private $effects = array();
    private $modes = array(
        "crop" => false
    );
    
    /**
     * Constructor for ImageProcessor class
     *
     * Adds a bunch of default effects
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     */
    public function __construct() {
        // Allow cropping of image
        // Accepted inputs:
        // true - enables cropping
        // false - disables cropping
        // ?x?x?x? - crop x, y, width and height
        $this->addeffect("crop", function(&$image, $param) {
            if ($param === "true") $this->modes["crop"] = true;
            else if ($param === "false") $this->modes["crop"] = false;
            else {
                $split = explode("x", $param);
                
                $splitcount = count($split);
                if ($splitcount) {
                    $geom = $image->getImageGeometry();
                    $geomWidth = $geom["width"];
                    $geomHeight = $geom["height"];
                    
                    if ($splitcount >= 4) {
                        $geomWidth -= $splitcount[3];
                        $geomHeight -= $splitcount[4];
                    }
                    
                    $defaults = array($geomWidth, $geomHeight, 0, 0);
                    foreach ($split as $key => $itm) {
                        if ($itm === "_" && array_key_exists($key, $defaults)) $itm = $defaults[$key];
                        $split[$key] = intval($itm);
                    }
                    
                    if ($splitcount >= 4) $image->cropImage($split[0], $split[1], $split[2], $split[3]);
                    //else if ($splitcount >= 2) $image->cropThumbnailImage($split[0], $split[1]);
                    else $this->modes["crop"] = filter_var($param, FILTER_VALIDATE_BOOLEAN);
                } else $this->modes["crop"] = filter_var($param, FILTER_VALIDATE_BOOLEAN);
            }
        });

        // Tints an image with a color
        // Accepted inputs:
        // ?-?-?x? - r, g, b, a
        $this->addeffect("tint", function(&$image, $param) {
            $split = explode("x", $param);

            $splitcount = count($split);
            if ($splitcount < 2) throw new InvalidArgumentException("Bad tint argument");

            $colors = explode("-", $split[0]);

            $draw = new ImagickDraw();
            $draw->setFillColor(new ImagickPixel("rgb($colors[0], $colors[1], $colors[2])"));
            $draw->setFillOpacity(floatval($split[1]));

            $geom = $image->getImageGeometry();
            $width = $geom['width'];
            $height = $geom['height'];
            $draw->rectangle(0, 0, $width, $height);
            $image->drawImage($draw);

            //$image->colorizeImage("rgb($colors[0], $colors[1], $colors[2])", floatval($split[1]));
        });
        
        // Change the compression quality of the image
        $this->addeffect("quality", function(&$image, $new) {
            $image->setImageCompressionQuality(intval($new));
        });
        
        // Change the compression type for the image
        $this->addeffect("compression", function(&$image, $new) {
            $map = array(
                "none" => Imagick::COMPRESSION_NO,
                "bzip" => Imagick::COMPRESSION_BZIP,
                "fax" => Imagick::COMPRESSION_FAX,
                "group4" => Imagick::COMPRESSION_GROUP4,
                "jpeg" => Imagick::COMPRESSION_JPEG,
                "jpeg2000" => Imagick::COMPRESSION_JPEG2000,
                "losslessjpeg" => Imagick::COMPRESSION_LOSSLESSJPEG,
                "lzw" => Imagick::COMPRESSION_LZW,
                "rle" => Imagick::COMPRESSION_RLE,
                "zip" => Imagick::COMPRESSION_ZIP,
                "dxt1" => Imagick::COMPRESSION_DXT1,
                "dxt3" => Imagick::COMPRESSION_DXT3,
                "dxt5" => Imagick::COMPRESSION_DXT5
            );
            if (!in_array($new, $map)) return;
            $image->setCompression($map[$new]);
        });
        
        // Change width of image
        $this->addeffect("width", function(&$image, $new) {
            $geom = $image->getImageGeometry();
            
            if ($this->modes["crop"]) $image->cropThumbnailImage($new, $geom["height"]);
            else $image->thumbnailImage($new, $geom["height"]);
        });
        
        // Change height of image
        $this->addeffect("height", function(&$image, $new) {
            $geom = $image->getImageGeometry();
            
            if ($this->modes["crop"]) $image->cropThumbnailImage($geom["width"], $new);
            else $image->thumbnailImage($geom["width"], $new);
        });
        
        // If the image is bigger than the size, resizes it
        $this->addeffect("maxsize", function(&$image, $new) {
            $geom = $image->getImageGeometry();
            
            $split = explode("x", $new);
            list($width, $height) = array_map('intval', $split);
            
            $newWidth = $geom["width"];
            $newHeight = $geom["height"];
            if ($geom["width"] > $width) $newWidth = $width;
            if ($geom["height"] > $height) $newHeight = $height;
            
            $image->thumbnailImage($newWidth, $newHeight, true);
        });
        
        // If the image is smaller than the size, resizes it
        $this->addeffect("minsize", function(&$image, $new) {
            $geom = $image->getImageGeometry();
            
            $split = explode("x", $new);
            list($width, $height) = array_map('intval', $split);
            
            $newWidth = $geom["width"];
            $newHeight = $geom["height"];
            if ($geom["width"] < $width) $newWidth = $width;
            if ($geom["height"] < $height) $newHeight = $height;
            
            $image->thumbnailImage($newWidth, $newHeight, true);
        });
        
        // Change border radius
        $this->addeffect("radius", function(&$image, $new) {
            $image->roundCorners($new, $new);
        });
        
        // Blurs an image
        $this->addeffect("blur", function(&$image, $new) {
            $image->blurImage(floatval($new), floatval($new));
        });

        // Sets an images brightness
        $this->addeffect("brightness", function(&$image, $new) {
            $image->modulateImage(intval($new), 100, 100);
        });

        // Sets an images saturation
        $this->addeffect("saturation", function(&$image, $new) {
            $image->modulateImage(100, intval($new), 100);
        });

        // Sets an images hue
        $this->addeffect("hue", function(&$image, $new) {
            $image->modulateImage(100, 100, intval($new));
        });
        
        // Change image format
        $this->addeffect("format", function(&$image, $new) {
            $image->setImageFormat($new);
        });
    }
    
    /**
     * Add an effect that can be processed
     *
     * The callback function is executed with two parameters: the image, and the value of the option. Use it like this:
     * $this->addeffect("myeffect", function(&$image, $val)) {
     *    // Do something with the image
     * });
     * Don't forget the & (ampersand) before $image!
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $name Name of effect to be created
     * @param callable $call Function to be called (see above)
     */
    public function addeffect($name, $call) {
        if (!is_callable($call)) throw new InvalidArgumentException("ImageProcessor::addeffect() expects parameter 2 to be a valid callable");
        
        $this->effects[$name] = $call;
    }
    
    /**
     * Batch process a set of effects on an image
     *
     * Effects should be structured like so:
     * array(
     *    "width" => 50,
     *    "radius" => 20,
     *    "myeffect" => "coolbeans"
     * )
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param string $image Path to image to process
     * @param array $effects Effects to apply
     * @return Imagick Image from processing
     */
    public function batch($image, $effects) {
        $img = new Imagick();
        $img->readImage($image);
        foreach ($effects as $effect => $params) {
            $img = $this->process($img, $effect, $params);
        }
        return $img;
    }
    
    /**
     * Process an effect on an image
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @param mixed $image Path to image, or Imagick object for image
     * @param string $effect Name of effect
     * @param mixed $params Parameter to pass into effect
     * @return Imagick Image from processing
     */
    public function process($image, $effect, $params) {        
        if (!($image instanceOf Imagick)) {
            $image = new Imagick();
            $image->readImage($image);
        }
        if (!array_key_exists($effect, $this->effects)) return $image;
        
        $this->effects[$effect]($image, $params);
        
        return $image;
    }
}
