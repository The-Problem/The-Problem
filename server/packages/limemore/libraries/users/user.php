<?php
/**
 * Provides a wrapper for a single database user
 *
 * Allows access to all information about the user
 * Also provides the ability to modify the information
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.1
 * @copyright Copyright (c) 2013, mrfishie Studios
 * @package Libraries.Users
 */
class User {
    private $info = array();
    private $id = -1;
    private $paths = NULL;
    
    /**
     * Constructor for User class
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.1
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param array $info Raw information from the database
     */
    public function __construct($info = false) {
        if ($info) {
            $this->id = $info["id"];
            $this->info["username"] = $info["username"];
            $this->info["password"] = $info["password"];
            $this->info["firstname"] = $info["firstname"];
            $this->info["lastname"] = $info["lastname"];
            $this->info["birthdate"] = new DateTime($info["birthdate"]);
            $this->info["email"] = $info["email"];
        }
    }
    
    /**
     * Gets the ID of the user in the database
     * If the user is not in the database, -1 will be returned
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2014, Tom Barham
     * @return int ID of the user in the database
     */
    public function id() {
        return $this->id;
    }
    
    /**
     * Getter and setter for username
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $new New value to set username to
     * @return string Username
     */
    public function username($new = null) {
        if (!is_null($new)) $this->updateproperty("username", $new);
        return $this->info["username"];
    }
    
    /**
     * Getter and setter for password
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $new New value to set password to
     * @return string Password
     */
    public function password($new = null) {
        Library::get("password");
        if (!is_null($new)) $this->updateproperty("password", password_hash($new, PASSWORD_DEFAULT));
        return $this->info["password"];
    }
    
    /**
     * Getter and setter for full name
     *
     * @param string $new New value to set full name to
     * @return string Full name
     */
    public function fullname($new = null) {
        if (!is_null($new)) {
            $splitPos = strpos($new, " ");
            $firstname = $new;
            $lastname = "";
            
            if ($splitPos !== false) {
                $firstname = substr($new, 0, $splitPos);
                $lastname = substr($new, $splitPos);
            }
            $this->updateProperty("firstname", $firstname);
            $this->updateProperty("lastname", $lastname);
        }
        
        $name = $this->info["firstname"];
        if ($this->info["lastname"]) $name .= " " . $this->info["lastname"];
        
        return $name;
    }
    
    /**
     * Alias for User::fullname()
     *
     * @param string $new New value to set full name to
     * @return string Full name
     * @deprecated
     */
    public function name($new = NULL) {
        return $this->fullname($new);
    }
    
    /**
     * Getter and setter for first name
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $new New value to set first name to
     * @return string First name
     */
    public function firstname($new = null) {
        if (!is_null($new)) $this->updateproperty("firstname", $new);
        return $this->info["firstname"];
    }
    
    /**
     * Getter and setter for last name
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copright Copyright (c) 2013, mrfishie Studios
     * @param string $new New value to set last name to
     * @return string Last name
     */
    public function lastname($new = null) {
        if (!is_null($new)) $this->updateproperty("lastname", $new);
        return $this->info["lastname"];
    }
    
    /**
     * Getter and setter for birthdate
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param DateTime $new New value to set birthdate to
     * @return DateTime Birthdate
     */
    public function birthdate(DateTime $new = null) {
        if (!is_null($new)) $this->updateproperty("birthdate", $new->format("Y-m-d H:i:s"));
        return $this->info["birthdate"];
    }
    
    /**
     * Getter and setter for email
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $new New value to set email to
     * @return string Email
     */
    public function email($new = null) {
        if (!is_null($new)) $this->updateproperty("email", $new);
        return $this->info["email"];
    }
    
    /**
     * Finds if the user exists
     *
     * @return bool Whether the user exists
     */
    public function exists($new = null) {
        if (!is_null($new)) $this->updateproperty("exist", (int)$new);
        return $this->info["exist"];
    }
    
    /**
     * Gets the URL for the user formatted as a link
     *
     * @return string The url
     */
    public function link() {
        $name = htmlentities($this->fullname());
        return '<a href="' . htmlentities($this->url()) . '" title="' . $name . '\'s profile page">' . $name . '</a>';
    }
    
    /**
     * Gets the URL for the user
     *
     * @return string The url
     */
    public function url() {
        return Path::getclientfolder("person", $this->username());
    }
    
    /**
     * Updates properties in the database for the current user
     *
     * Creates the user in the database if it does not already exist
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.1
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $name Name of property
     * @param string $value New value to set
     */
    public function updateproperty($name, $value) {
        if ($this->id < 0) {
            $type = is_string($value) ? "s" : "i";
            
            Connection::query("INSERT INTO `users` (`" . $name . "`) VALUES (?)", $type, array($value));
            $this->id = Connection::insertid();
        }
        else {
            Connection::query("UPDATE `users` SET " . $name . " = ? WHERE id = ?", "si", array($value, $this->id));
        }
        $this->info[$name] = $value;
    }
}