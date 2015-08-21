<?php
/**
 * Provides a wrapper for database users
 *
 * Allows access to users from the database by id, username, or email.
 * Also provides the ability to log in, or get the current logged in user
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.2
 * @copyright Copyright (c) 2013, mrfishie Studios
 */
class Users {
    private static $u = array(
        "username" => array(),
        "id" => array(),
        "email" => array()
    );
    
    /**
     * Sets the name of the extension ('Remember Me') cookie
     *
     * @var string The name of the extension cookie
     */
    public static $extensioncookie = "ext";
    
    const TYPE_ID = 1;
    const TYPE_USERNAME = 2;
    const TYPE_EMAIL = 4;
    const TYPE_DEFAULT = 7;
    
    /**
     * Get a user by id, username, or email
     *
     * Returns a user from the database
     *
     * To intelligently figure out the type based on the passed
     * parameter, you can use bitmasking.
     *
     * E.g. Users::getuser("me@me.com", Users::TYPE_USERNAME | Users::TYPE_EMAIL)
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param mixed $i Id, username, or email to use
     * @param int $paramtype The type of the first parameter. Either Users::TYPE_ID, Users::TYPE_USERNAME, Users::TYPE_EMAIL, or Users::TYPE_DEFAULT
     * @param bool $mustexist Whether the user must exist
     * @return mixed The user object, or false on failure
     */
    public static function getuser($i = 0, $paramtype = self::TYPE_DEFAULT, $mustexist = false) {
        $queryItems = array(
            // Constant                  Cache name  Is value valid?                        Query insert    Type  Insert value
            self::TYPE_ID =>       array("id",       is_numeric($i),                        "id = ?",       "i",  intval($i)),
            self::TYPE_USERNAME => array("username", true,                                  "username = ?", "s",  $i),
            self::TYPE_EMAIL =>    array("email",    filter_var($i, FILTER_VALIDATE_EMAIL), "email = ?",    "s",  $i)
        );
        
        $query = "SELECT * FROM `users` WHERE ";
        $queryInsert = array();
        $possibleTypes = array();
        $types = "";
        $values = array();
        
        foreach ($queryItems as $id => $info) {
            if (!($paramtype & $id) || !$info[1]) continue;
            $possibleTypes[$info[0]] = $info[4];
            array_push($queryInsert, $info[2]);
            $types .= $info[3];
            array_push($values, $info[4]);
        }
        
        foreach ($possibleTypes as $type => $value) {
            if (array_key_exists($value, self::$u[$type])) return self::$u[$type][$i];
        }
        
        if (!count($queryInsert)) return false;
        $query .= implode(" OR ", $queryInsert);
        
        if ($mustexist) $query .= " AND `exist` = 1";
        
        $items = Connection::query($query, $types, $values);
        if (!count($items)) return false;
        
        Library::file("users", "user");
        $user = new User($items[0]);
        self::$u["username"][$user->username()] = $user;
        self::$u["id"][$user->id()] = $user;
        self::$u["email"][$user->email()] = $user;
        
        return $user;
    }
    
    /**
     * Finds if a user exists
     *
     * The parameter can either be an id, username, or email
     *
     * @param mixed $i Id, username, or email to check
     * @param int $paramtype See Users::getuser()
     * @return bool Whether the user exists
     */
    public static function exists($i = 0, $paramtype = self::TYPE_DEFAULT) {
        return (bool)self::getuser($i, $paramtype);
    }
    
    /**
     * Login using the specified username and password
     *
     * Allows a username, or email for the user. Sets the session for the user
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param mixed $username Username, email, or user id for user
     * @param string $password Password for user
     * @param boolean $extend Whether to extend the login, default is false
     * @return boolean Whether we have successfully logged in
     */
    public static function login($username, $password, $extend = false) {
        Library::get("password");
        
        $user = self::getuser($username, self::TYPE_USERNAME | self::TYPE_EMAIL, true);
        if ($user && password_verify($password, $user->password())) {
            $_SESSION['userid'] = $user->id;
            
            if ($extend) {
                $extendCookie = Cookies::get(self::$extensioncookie);
                $extendCookie->value($user->id);
                $extendCookie->timeout("+3 months");
                $extendCookie->http(true);
            }
            return true;
        } else return false;
    }
    
    /**
     * Logout
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     */
    public static function logout() {
        $_SESSION['userid'] = false;
        $_SESSION[self::$extensioncookie] = false;
    }
    
    /**
     * Check if currently logged in
     *
     * If we are logged in, gets the user that we are logged in as
     * 
     * If an extension cookie exists with a valid user ID, the user will be
     * logged in.
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, mrfishie Studios
     * @param string $allowExtension Whether to allow cookie extensions ('Remember me'). Default is true
     * @return mixed If logged in, returns the current user, otherwise returns false
     */
    public static function loggedin($allowExtension = true) {
        if ($allowExtension) {
            $extensionCookie = $_SESSION[self::$extensioncookie];
            $extensionId = $extensionCookie->value();
            if (!is_array($extensionId)) {
                $_SESSION['userid'] = $extensionId;
            }
        }
        
        if ($_SESSION['userid']) {
            $userid = $_SESSION['userid'];
            return self::getuser($userid, self::TYPE_ID, true);
        } else return false;
    }
}