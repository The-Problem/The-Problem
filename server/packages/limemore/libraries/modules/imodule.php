<?php
/**
 * Stores a module instance
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.1
 * @copyright Copyright (c) 2013, mrfishie Studios
 * @package Libraries.Modules
 */
interface IModule {
    /**
     * Sets the size of the spinner
     *
     * @return int The spinner size
     */
    public function spinnersize();
    
    /**
     * Get module code
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param mixed $params Parameters passed to module
     * @param Head $h A head object to add items to the pages head
     * @return string Module code
     */
    public function getcode($params = array(), Head $h);
    
    /**
     * Get surrounding code
     *
     * Used for AJAX modules to insert code before and after
     * The code to be placed in the middle is passed in
     *
     * @param string $code The code to be placed
     * @param array $params Parameters passed to module
     * @return string The code to output
     */
    public function getsurround($code, $params);
}