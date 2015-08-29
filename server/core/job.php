<?php
/**
 * LimePHP Job System
 *
 * Allows executing jobs through the command line
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 */

include("lime.php"); 
if (!LimePHP::inCommandLine()) throw new Exception("This file can only be run from a CLI");

LimePHP::initialize();

Library::get("jobs");
$args = $argv;
array_shift($args);
$job = array_shift($args);
Jobs::execute($job, $args);

Library::flush_cache();
l_include_flush();