<?php
/**
 * LimePHP page loader
 *
 * Starts the LimePHP system
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, mrfishie Studios
 */

/* CONFIG */
define('SERVER_ROOT', "../server");
/* END CONFIG */

include(realpath(SERVER_ROOT . "/core/lime.php"));
LimePHP::start();