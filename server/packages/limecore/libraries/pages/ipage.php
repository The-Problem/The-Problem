<?php
/**
 * Page interface
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2013, mrfishie Studios
 * @package Libraries.Pages
 */
interface IPage {
    /**
     * Constructor for page
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param PageInfo &$info Information about the current page
     */
    public function __construct(PageInfo &$info);
    
    /**
     * Get the template to use for the page
     * 
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @return ITemplate Template to use for page
     */
    public function template();
    
    /**
     * Get the current logged in users permission to view this page
     * 
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @return boolean Can the current user view the page or not
     */
    public function permission();
    
    /**
     * Get elements to be added to the pages head
     *
     * DO NOT echo anything from this function, it is not executed at the time of display.
     * 
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param Head &$head Information about head tag
     */
    public function head(Head &$head);
    
    /**
     * Called when it is time to output body code
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @return mixed Optional, value to output (can also be echo'd)
     */
    public function body();
    
        /**
     * Should page support subpages
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @return boolean Does the page support subpages
     */
    public function subpages();
}