<?php
/**
 * A query class that allows OOP access a database using prepared statements
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 * @package LimeCore.connection
 */
class Query {
    private $query;
    private $types;
    private $items = array();
    
    /**
     * Constructor for Query class
     *
     * The $types and $items parameters are swappable. If $types is not specified,
     * types will automatically be inferenced from the types in $items.
     *
     * @param string $query The query
     * @param string $types The types for the items
     * @param array $items The items for the prepared statement. Default is empty array
     */
    public function __construct($query, $types = NULL, $items = NULL) {
        if (is_array($types)) {
            $a = $items;
            $items = $types;
            $types = $a;
        }
        
        if (is_null($items)) $items = array();
        
        if (is_null($types)) foreach ($items as $itm) $this->add($itm);
        else {
            $this->items = $items;
            $this->types = $types;
        }
        
        $this->query = $query;
    }
    
    /**
     * Adds an item to the query
     *
     * @param mixed $val The value of the item
     * @param string $type The type of the value. If not specified, will be inferenced based on the type of $val
     */
    public function add($val, $type = NULL) {
        if (is_null($type)) {
            if (is_integer($val)) $type = "i";
            else if (is_double($val)) $type = "d";
            else $type = "s";
        }
        $this->types .= $type;
        array_push($this->items, $val);
    }
    
    /**
     * Executes the query
     *
     * @return mixed The value from the query
     */
    public function execute() {
        $results = Connection::query($this->query, $this->types, $this->items);
        return Connection::query($this->query, $this->types, $this->items);
    }
    
    /**
     * Constructs and runs a query
     *
     * The parameters behave the same as the ones in Query::__construct.
     *
     * @param string $query The query
     * @param string $types The types for the items
     * @param array $items The items for the prepared statement. Default is empty
     * @return mixed The value from the query
     */
    static function run($query, $types = NULL, $items = NULL) {
        $query = new Query($query, $types, $items);
        return $query->execute();
    }
}