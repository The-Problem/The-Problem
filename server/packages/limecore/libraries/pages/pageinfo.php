<?php
/**
 * Contains information on the current page
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.Pages
 */
class PageInfo {
    /**
     * Path list for current page
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @var array Current path list
     */
    public $pagelist = array();
    
    /**
     * Constructor for PageInfo
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @param array $pl Current path list
     */
    public function __construct(array $pl) {
        $this->pagelist = $pl;
    }
}