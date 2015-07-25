<?php
class QueryBuilder {
    public static $DefaultQueryBuilderOptions = array(
        "autoQuoteTableNames" => false,
        "autoQuoteFieldNames" => false,
        "autoQuoteAliasNames" => false,
        "nameQuoteCharacter" => "`",
        "tableAliasQuoteCharacter" => "`",
        "fieldAliasQuoteCharacter" => '"',
        "customValueHandlers" => array(),
        "numberedParameters" => false,
        "replaceSingleQuotes" => false,
        "singleQuoteReplacement" => '"',
        "separator" => " "
    );
    
    public static function select($options = null) {
        if (is_null($options)) $options = self::$DefaultQueryBuilderOptions;
        return new QueryBuilderQuery("select");
    }
}
class QueryBuilderQuery {
    private $query = array();
    private $options = array();
    
    private function addItem($name, $item) {
        if (!array_key_exists($name, $this->query)) $this->query[$name] = array();
        array_push($this->query[$name], $item);
    }
    private function getItem($name) {
        if (!array_key_exists($name, $this->query)) $this->query[$name] = array();
        return $this->query[$name];
    }
    private function getFormattedItem($name, $delimiter = ", ", $nameKey = "name", $aliasKey = "alias", $name = "", $aliasSeparator = " ", $alias = "`") {
        $items = $this->getItem($name);
        return implode($delimiter, array_map(function($n) {
            $str = $name . $n[$nameKey] . $name;
            if (!is_null($n[$aliasKey])) $str .= $aliasSeparator . $alias . $n[$aliasKey] . $alias;
        }, $items));
    }
    
    private function setOption($name, $val) {
        $this->options[$name] = $val;
    }
    private function getOption($name) {
        if (!array_key_exists($name, $this->options)) $this->options[$name] = 0;
        return $this->options[$name];
    }
    
    public function __construct($intro) {
        $this->setOption("intro", strtolower($intro));
    }
    
    public function from($item, $alias = null) {
        $this->addItem("from", array(
            "name" => $item,
            "alias" => $alias
        ));
        return $this;
    }
    
    public function field($item, $name = null) {
        $this->addItem("field", array(
            "name" => $item,
            "alias" => $name
        ));
        return $this;
    }
    
    public function fields($fields) {
        foreach ($fields as $name => $alias) {
            $this->field($name, $alias);
        }
    }
    
    public function distinct() {
        $this->setOption("distinct", true);
        return $this;
    }
    
    private function addJoin($name, $alias, $condition, $type) {
        $this->addItem("join", array(
            "type" => $type,
            "name" => $name,
            "alias" => $alias,
            "condition" => $condition
        ));
        return $this;
    }
    
    public function join($name, $alias = null, $condition = null) {
        return $this->addJoin($name, $alias, $condition, "INNER");
    }
    
    public function outer_join($name, $alias = null, $condition = null) {
        return $this->addJoin($name, $alias, $condition, "OUTER");
    }
    
    public function left_join($name, $alias = null, $condition = null) {
        return $this->addJoin($name, $alias, $condition, "LEFT");
    }
    
    public function right_join($name, $alias = null, $condition = null) {
        return $this->addJoin($name, $alias, $condition, "RIGHT");
    }
    
    public function where($query) {
        $args = func_get_args();
        array_shift($args);
        
        $this->addItem("where", $query);
        foreach ($args as $arg) {
            $this->addItem("parameters", $arg);
        }
        
        return $this;
    }
    
    public function order($name, $sort = true) {
        $this->addItem("order", array(
            "name" => $name,
            "alias" => $sort ? "ASC" : "DESC"
        ));
        return $this;
    }
    
    public function group($column) {
        $this->addItem("group", $column);
        return $this;
    }
    
    public function limit($count = 0) {
        $this->setOption("limit", $count);
        return $this;
    }
    
    public function offset($offset = 0) {
        $this->setOption("offset", $offset);
        return $this;
    }
    
    private function selectToString() {
        $type = $this->getOption("intro") . " ";
        
        $distinct = $this->getOption("distinct") ? "DISTINCT " : "";
        
        $fields = $this->getFormattedItem("field", ", ", "name", "alias", "", " AS ", '"');
        if (strlen($fields) === 0) $fields = "*";
        
        $from = " FROM " . $this->getFormattedItem("from");
        
        $joins = "";
        $joinItems = $this->getItem("join");
        foreach ($joinItems as $itm) {
            $str = $itm["type"] . " JOIN " . $itm["name"];
            if (!is_null($itm["alias"])) $str .= " `" . $itm["alias"] . "`";
            if (!is_null($itm["condition"])) $str .= " ON (" . $itm["condition"] . ")";
            $joins .= $str . " ";
        }
        
        $where = "";
        if (count($this->getItem("where"))) {
            $where .= " WHERE ";
            $where .= implode(" AND ", array_map(function($n) {
                return "(" . $n . ")";
            }, $this->getItem("where")));
        }
        
        $sort = "";
        if (count($this->getItem("order"))) {
            $sort .= " ORDER BY ";
            $sort .= $this->getFormattedItem("order", null, null, null, null, null, "");
        }
        
        $group = "";
        if (count($this->getItem("group"))) {
            $group .= " GROUP BY ";
            $group .= implode(", ", $this->getItem("group"));
        }
        
        $limit = "";
        if ($this->getOption("limit")) {
            $limit .= " LIMIT " . $this->getOption("limit");
        }
        
        $offset = "";
        if ($this->getOption("offset")) {
            $offset .= " OFFSET " . $this->getOption("offset");
        }
        
        $query = "SELECT " . $distinct . $fields . $from . $joins . $where . $sort . $group . $limit . $offset;
        return $query;
    }
    
    public function __toString() {
        switch ($this->getOption("intro")) {
            case "select": return $this->selectToString();
            default: throw new Exception("Invalid type " . $this->getOption("intro") . " for query");
        }
    }
    
    public function toParam() {
        return array(
            "text" => $this->__toString(),
            "values" => $this->getItem("parameters")
        );
    }
    
    public function toString() {
        return $this->__toString();
    }
}