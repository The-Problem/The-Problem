<?php
/**
 * Defines default functions for Packages
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package LimePHP.Packages
 */
interface IPackage {    
    public function initialize(Resources &$r);
}