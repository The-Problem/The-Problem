<?php
/**
 * Represents a cookie
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2014, Tom Barham
 */
class Cookie {
    private $info;
    private $exists = false;
    private $cache = array();
    private $name;
    private $id;
    
    /**
     * Constructor for Cookie class
     *
     * @param string $name The name of the cookie
     */
    public function __construct($name) {
        if (array_key_exists($name, $_COOKIE)) {
            $uniqid = $_COOKIE[$name];
            $items = Connection::query("SELECT * FROM `cookies` WHERE `uniqid` = ?", "s", array($uniqid));
            if (count($items) > 0) {
                $info = $items[0];
                $info["value"] = unserialize($info["value"]);
                $info["timeout"] = new DateTime($info["timeout"]);
                $this->info = $info;
                $this->exists = true;
            }
        }
                
        $this->name = $name;
        
        if ($this->exists) $this->cookiemonster();
        else {
            $this->info = array(
                "name" => $this->name,
                "uniqid" => bin2hex(openssl_random_pseudo_bytes(32)),
                "value" => array(),
                "timeout" => new DateTime("@0"),
                "type" => "session",
                "domain" => "." . (empty(Path::getdomain()) ? "localhost" : Path::getdomain()),
                "http" => false
            );
            $this->updatetimeout();
        }
    }
    
    /**
     * Finds if the cookie is past its expiry date and eates it if required
     *
     * @param DateInterval $sessiontimeout See Cookie::updatetimeout()
     */
    public function cookiemonster($sessiontimeout = NULL) {
        $timeout = $this->timeout()->format("U");
        if ($this->info["type"] === "session") {
            $timeout = $this->updatetimeout($sessiontimeout);
        }
        
        $now = new DateTime();
        if ($timeout < $now) $this->delete();
    }
    
    /**
     * Updates a session cookies timeout
     *
     * @param DateInterval $sessiontimeout The amount of time to wait after last activity before eating a session cookie. Default is Cookies::$sessiontimeout
     * @return DateTime The timeout for the cookie
     */
    public function updatetimeout($sessiontimeout = NULL) {
        if ($this->info["type"] !== "session") return;
        
        if (is_null($sessiontimeout)) $sessiontimeout = Cookies::$sessiontimeout;

        if (!array_key_exists("lpla", $_COOKIE)) return $this->timeout();
        else $lastactivity = new DateTime($_COOKIE["lpla"]);
        $timeout = $lastactivity->add($sessiontimeout);
        
        $this->timeout($timeout->format("r"), false);
        return $timeout;
    }
    
    /**
     * Gets or sets the name of the cookie
     *
     * @param string $val The name of the cookie
     * @return string The name of the cookie
     */
    public function name($val = NULL) {
        if (!is_null($val)) {
            $this->info["name"] = $val;
            $this->update("name", $val);
        }
        return $this->info["name"];
    }
    
    /**
     * Gets or sets the value of the cookie
     *
     * The value is always pulled or pushed straight from/to
     * the database to allow for multiple instances to
     * modify the value at the same time.
     *
     * @todo Implement this instant pull/push behaviour for
     * all parts of the cookie, or re-implement the cookie
     * system to work better in this case.
     *
     * @param mixed $val The value of the cookie
     * @return mixed The value of the cookie
     */
    public function value($val = NULL) {
        if (!is_null($val)) {
            $this->info["value"] = $val;
            $this->updateProperties(array("value" => $val));
        }
        
        $vals = Connection::query("SELECT value FROM `cookies` WHERE `id` = ?", "i", array($this->info["id"]));
        if (count($vals)) $this->info["value"] = unserialize($vals[0]["value"]);
        else $this->exists = false;
        
        return $this->info["value"];
    }
    
    /**
     * Gets or sets the timeout for the cookie
     *
     * @param string $val The new timeout for the cookie, formatted like an input to strtotime
     * @param bool $changetype Whether to change the cookie type to session if the timeout is 0, and vice-versa
     * @return DateTime The timeout for the cookie
     */
    public function timeout($val = NULL, $changetype = true) {
        if (!is_null($val)) {
            $newval = strtotime($val);
            
            if ($changetype) {
                if ($newval === 0 && $this->info["type"] !== "session") $this->update("type", "session");
                else if ($newval !== 0 && $this->info["type"] !== "normal") $this->update("type", "normal");
            }
            
            $this->info["timeout"] = new DateTime("@" . $newval);
            $this->update("timeout", $val);
        }
        return $this->info["timeout"];
    }
    
    /**
     * Gets or sets the base domain for the cookie
     *
     * @param string $val The new base domain for the cookie
     * @return string The base domain for the cookie
     */
    public function domain($val = NULL) {
        if (!is_null($val)) {
            $this->info["domain"] = $val;
            $this->update("domain", $val);
        }
        return $this->info["domain"];
    }
    
    /**
     * Gets or sets whether to use HTTPOnly on the cookie
     *
     * @param bool $val The new value for HTTPOnly
     * @return bool Whether to use HTTPOnly on the cookie
     */
    public function http($val = NULL) {
        if (!is_null($val)) {
            $this->info["http"] = $val;
            $this->update("http", $val);
        }
        return $this->info["http"];
    }
    
    /**
     * Finds if the cookie exists
     *
     * @return bool Whether the cookie exists
     */
    public function exists() {
        return $this->exists;
    }
    
    /**
     * Creates the cookie if it doesn't exist
     */
    public function create() {
        $this->exists = true;

        Connection::query("INSERT INTO `cookies` (name, uniqid, value, timeout, type, domain, http) VALUES (?, ?, ?, ?, ?, ?, ?)", "ssssssi", array(
            $this->info["name"],
            $this->info["uniqid"],
            serialize($this->info["value"]),
            $this->info["timeout"]->format("Y-m-d H:i:s"),
            $this->info["type"],
            $this->info["domain"],
            $this->info["http"]
        ));
        
        $this->info["id"] = Connection::insertid();
    }
    
    /**
     * Deletes a cookie
     */
    public function delete() {
        setcookie($this->name(), null, -1, '/');
        Connection::query("DELETE FROM `cookies` WHERE `id` = ?", "i", array($this->info["id"]));
        unset($_COOKIE[$this->name()]);
        $this->exists = false;
    }
    
    /**
     * Updates a set of properties on the cookie
     *
     * *Warning!* Unlike Cookie::update(), this function DOES NOT add
     * updates to a cache. This means that ALL modifications will be visible
     * in the HTTP cookie section.
     *
     * @param array $properties A key/value array of properties
     */
    public function updateProperties($properties) {
        $didntExist = false;

        // Create cookie if it doesnt exist
        if (!$this->exists) {
            $didntExist = true;
            $this->create();
        }
        
        $dbChanges = array();
        
        $query = "UPDATE `cookies` SET ";
        $types = "";
        $params = array();
        
        $queryitems = array();
        foreach ($properties as $name => $value) {
            $infoValue = $value;
            $dbValue = $value;
            
            // Special handling for different types
            switch ($name) {
                case "timeout":
                    $newval = strtotime($value);
                    $infoValue = new DateTime("@" . $newval);
                    $dbValue = date("Y-m-d H:i:s", $newval);
                    break;
                case "value":
                    $dbValue = serialize($value);
                    break;
            }
            
            
            array_push($queryitems, "`$name` = ?");
            if (is_string($dbValue)) $types .= "s";
            else $types .= "i";
            array_push($params, $dbValue);
            
            $this->info[$name] = $infoValue;
        }
        
        $query .= implode(", ", $queryitems);
        
        // Update cookie
        $names = array_keys($properties);
        if ($didntExist || in_array("name", $names) || in_array("uniqid", $names) || in_array("timeout", $names) || in_array("domain", $names) || in_array("http", $names)) $this->updateCookie();
        
        // Update database
        if (count($params)) {
            $query .= " WHERE `id` = ?";
            $types .= "i";
            array_push($params, $this->info["id"]);

            Connection::query($query, $types, $params);
        }
    }
    
    /**
     * Updates a property on the cookie
     *
     * To prevent multiple versions of one cookie from appearing
     * in the HTTP cookie header, this function adds the
     * modification to the cache. When LimePHP finishes, the
     * cookie cache will be cleared and all changes will be made.
     *
     * @param string $name The name of the property
     * @param mixed $value The new value for the property
     */
    public function update($name = NULL, $value = NULL) {
        $this->cache[$name] = $value;
    }
    
    /**
     * Clears the update cache and saves the cookies
     *
     * @return int The previous size of the cache
     */
    public function flushCache() {
        $size = count($this->cache);
        if ($size) $this->updateProperties($this->cache);
        $this->cache = array();
        return $size;
    }
    
    /**
     * Updates the actual client-side cookie
     */
    private function updateCookie() {
        $isSecure = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");
        setcookie($this->info["name"], $this->info["uniqid"], $this->info["timeout"]->format("U"), '/', $this->info["domain"], $isSecure, $this->info["http"]);
    }
}
