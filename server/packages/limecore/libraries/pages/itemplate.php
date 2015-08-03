<?php
/**
 * Template interface
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.Pages
 */
interface ITemplate {
    /**
     * Get elements to be added to the pages head
     *
     * DO NOT echo anything from this function, it is not executed at the time of display.
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param Head &$head Information about head tag
     */
    public function head(Head &$head);
    
    /**
     * Output page code
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param Head $head Information about head tag
     * @param string $pagecode Code for the body of the page
     * @param IPage $page Page to be shown
     */
    public function showpage(Head $head, $pagecode, IPage $page);
}
