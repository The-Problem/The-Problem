<?php
/**
 * This class can be used to get the most common colors in an image. It needs one parameter: $image, which is the filename of the image you want to process.
 *
 */
class Colors
{
	/**
	 * Returns the colors of the image in an array, ordered in descending order, where the keys are the colors, and the values are the count of the color.
	 *
	 * @return array
	 */
	public static function get($passed_image, $passed_image_url)
	{
		$PREVIEW_WIDTH    = 150;  //WE HAVE TO RESIZE THE IMAGE, BECAUSE WE ONLY NEED THE MOST SIGNIFICANT COLORS.
		$PREVIEW_HEIGHT   = 150;
		$size = GetImageSize($passed_image_url);
		$scale=1;
		if ($size[0]>0)
		$scale = min($PREVIEW_WIDTH/$size[0], $PREVIEW_HEIGHT/$size[1]);
		if ($scale < 1)
		{
			$width = floor($scale*$size[0]);
			$height = floor($scale*$size[1]);
		}
		else
		{
			$width = $size[0];
			$height = $size[1];
		}
		$image_resized = imagecreatetruecolor($width, $height);
		$image_orig = $passed_image;
		imagecopyresampled($image_resized, $image_orig, 0, 0, 0, 0, $width, $height, $size[0], $size[1]); //WE NEED NEAREST NEIGHBOR RESIZING, BECAUSE IT DOESN'T ALTER THE COLORS
		$im = $image_resized;
		$imgWidth = imagesx($im);
		$imgHeight = imagesy($im);
		$result = array();
		for ($y=0; $y < $imgHeight; $y++)
		{
			for ($x=0; $x < $imgWidth; $x++)
			{
				$index = imagecolorat($im,$x,$y);
				$Colors = imagecolorsforindex($im,$index);
				$Colors['red']=intval((($Colors['red'])+15)/32)*32;    //ROUND THE COLORS, TO REDUCE THE NUMBER OF COLORS, SO THE WON'T BE ANY NEARLY DUPLICATE COLORS!
				$Colors['green']=intval((($Colors['green'])+15)/32)*32;
				$Colors['blue']=intval((($Colors['blue'])+15)/32)*32;
				if ($Colors['red']>=256)
				$Colors['red']=240;
				if ($Colors['green']>=256)
				$Colors['green']=240;
				if ($Colors['blue']>=256)
				$Colors['blue']=240;
				array_push($result, json_encode(array($Colors['red'], $Colors['green'], $Colors['blue'])));
			}
		}
		$result = array_count_values($result);
		natsort($result);
		$result = array_reverse($result, true);
		
		$return = array();
		foreach ($result as $name => $value) {
			array_push($return, json_decode($name));
		}
		
		return $return;
	}
	
	/**
	 * Converts an RGB color into HSL
	 *
	 * All of the results are between 0 and 1
	 *
	 * @param int $red The red value, from 0 to 255
	 * @param int $green The green value, from 0 to 255
	 * @param int $blue The blue value, from 0 to 255
	 * @return array An array containing the hue at 0, the saturation at 1, and the lightness at 2
	 */
	public static function rgb2hsl($red, $green, $blue) {
		$r = $red / 255;
		$g = $green / 255;
		$b = $blue / 255;
		
		$l = self::lightness($r, $g, $b);
		
		if ($dif === 0) {
			$h = 0;
			$s = 0;
		} else {
			if ($l < 0.5) $s = $diff / ($maxColor + $minColor);
			else $s = $diff / (2 - $maxColor + $minColor);
		}
		
		$halfDiff = $diff / 2;
		$delr = ((($maxColor - $r) / 6) + $halfDiff) / $diff;
		$delg = ((($maxColor - $g) / 6) + $halfDiff) / $diff;
		$delb = ((($maxColor - $b) / 6) + $halfDiff) / $diff;
		
		if ($r === $maxColor) $h = $delb - $delg;
		else if ($g === $maxColor) $h = (1 / 3) + $delr - $delb;
		else if ($b === $maxColor) $h = (2 / 3) + $delg - $delr;
		
		if ($h < 0) $h += 1;
		if ($h > 1) $h -= 1;
		
		return array(
			$h, $s, $l
		);
	}
	
	/**
	 * Finds the lightness of a color
	 *
	 * @param int $red The red value, from 0 to 255
	 * @param int $green The green value, from 0 to 255
	 * @param int $blue The blue value, from 0 to 255
	 * @return float $lightness The lightness value, from 0 to 255
	 */
	public static function lightness($red, $green, $blue) {
		$minColor = min($red, $green, $blue);
		$maxColor = max($red, $green, $blue);
		$diff = $maxColor - $minColor;
		return ($maxColor + $minColor) / 2;
	}
	
	/**
	 * Converts an HSL color to RGB
	 *
	 * Inputs should all be between 0 and 1, outputs will be between 0 and 255
	 *
	 * @param float $hue The hue value
	 * @param float $saturation The saturation value
	 * @param float $lightness The lightness value
	 * @return array An array containing the red at 0, green at 1, and blue at 2
	 */
	public static function hsl2rgb($hue, $saturation, $lightness) {
		$hue *= 6;
		$i = floor($hue);
		$f = $hue - $i;
		
		$m = $lightness * (1 - $saturation);
		$n = $lightness * (1 - $saturation * $f);
		$k = $lightness * (1 - $saturation * (1 - $f));
		
		$sort = array(
			array($lightness, $k, $m),
			array($n, $lightness, $m),
			array($m, $lightness, $k),
			array($m, $n, $lightness),
			array($k, $m, $lightness),
			array($lightness, $m, $n),
		);
		
		if ($i >= count($sort)) $i = count($sort) - 1;
		$values = $sort[$i];
		return array(
			floor($values[0] * 255),
			floor($values[1] * 255),
			floor($values[2] * 255)
		);
	}
	
	/**
	 * Finds a contrasting color based on the passed one
	 *
	 * @param int $red The red value, from 0 to 255
	 * @param int $green The green value, from 0 to 255
	 * @param int $blue The blue value, from 0 to 255
	 * @return array An array containing the new red at 0, the new green at 1, and the new blue at 2
	 */
	public static function contrasting($red, $green, $blue) {
		$hsl = self::rgb2hsl($red, $green, $blue);
		$newHue = $hsl[0] + 0.5;
		if ($newHue > 1) $newHue -= 1;
		return self::hsl2rgb($newHue, $hsl[1], $hsl[2]);
	}
	
	/**
	 * Uses a contrasting color if the color is above or below the thresholds
	 *
	 * @param array $color An array containing red at 0, green at 1, blue at 2
	 * @param float $lower The lower threshold, default is 50
	 * @param float $higher The higher threshold, default is 205
	 * @param float $llower The lower threshold for HSL 'lightness'
	 * @param float $lhigher The higher threshold for HSL 'lightness'
	 * @return array An array formatted the same as the input
	 */
	public static function autocontrast($color, $lower = 50, $higher = 205, $llower = 100, $lhigher = 150) {
		$rlower = $llower / 255;
		$rhigher = $lhigher / 255;
		
		$hsl = self::rgb2hsl($color[0], $color[1], $color[2]);
		if ($hsl[2] < $rlower) $hsl[2] = $rlower;
		if ($hsl[2] > $rhigher) $hsl[2] = $rhigher;
		
		$rgb = self::hsl2rgb($hsl[0], $hsl[1], $hsl[2]);
		$lightness = max($rgb[0], $rgb[1], $rgb[2]);
		
		if ($lightness < $lower || $lightness > $higher) {
			$hsl[0] = $hsl[0] + 0.5;
			if ($hsl[0] > 1) $hsl[0] -= 1;
		}
		return self::hsl2rgb($hsl[0], $hsl[1], $hsl[2]);
	}
}
