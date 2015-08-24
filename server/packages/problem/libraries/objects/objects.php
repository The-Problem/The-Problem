<?php

class Objects {
    private static $permissions = array();

    public static function permission($object_id, $type, $username) {
        $keyname = "0$object_id.$username.$type";
        if (array_key_exists($keyname, self::$permissions)) return self::$permissions[$keyname];

        $results = Connection::query("
SELECT COUNT(users.Username) AS Has_Permission FROM users
  LEFT JOIN userpermissions ON (userpermissions.Username = users.Username
                                AND userpermissions.Object_ID = ?
                                AND userpermissions.Permission_Name = ?)
  LEFT JOIN grouppermissions ON (users.Rank >= grouppermissions.Rank
                                 AND grouppermissions.Object_ID = ?
                                 AND grouppermissions.Permission_Name = ?)
WHERE users.Username = ?
      AND (userpermissions.Username = users.Username
           OR users.Rank >= grouppermissions.Rank)", "isiss", array(
            $object_id, $type, $object_id, $type, $username
        ));

        $value = $results[0]["Has_Permission"] > 0;
        self::$permissions[$keyname] = $value;
        return $value;
    }

    public static function user_permissions($type, $username) {
        $keyname = "1$username.$type";
        if (array_key_exists($keyname, self::$permissions) && self::$permissions[$keyname] !== FALSE) return self::$permissions[$keyname];

        $results = Connection::query("
SELECT userpermissions.Object_ID AS Object_ID1, grouppermissions.Object_ID AS Object_ID2
FROM users
  LEFT JOIN userpermissions ON (userpermissions.Username = users.Username
                                AND userpermissions.Permission_Name = ?)
  LEFT JOIN grouppermissions ON (users.Rank >= grouppermissions.Rank
                                 AND grouppermissions.Permission_Name = ?)
WHERE users.Username = ?
      AND (userpermissions.Username = users.Username
           OR users.Rank >= grouppermissions.Rank)", "sss", array($type, $type, $username));

        $value = array();
        foreach ($results as $item) {
            if (!is_null($item["Object_ID1"])) {
                $value[$item["Object_ID1"]] = true;
                self::$permissions["0" . $item["Object_ID1"] . ".$username.$type"] = true;
            }
            if (!is_null($item["Object_ID2"])) {
                $value[$item["Object_ID2"]] = true;
                self::$permissions["0" . $item["Object_ID2"] . ".$username.$type"] = true;
            }
        }

        self::$permissions[$keyname] = $value;
        return $value;
    }

    public static function allow_user($object_id, $type, $username) {
        Connection::query("INSERT INTO userpermissions (Object_ID, Permission_Name, Username) VALUES (?, ?, ?)",
            "iss", array($object_id, $type, $username));

        self::$permissions["0$object_id.$username.$type"] = true;
        self::$permissions["1$username.$type"][$object_id] = true;
    }
    public static function deny_user($object_id, $type, $username) {
        Connection::query("DELETE FROM userpermissions WHERE Object_ID = ?
                                                       AND Permission_Name = ?
                                                       AND Username = ?", "iss",
            array($object_id, $type, $username));

        self::$permissions["0$object_id.$username.$type"] = false;
        self::$permissions["1$username.$type"][$object_id] = false;
    }

    public static function allow_rank($object_id, $type, $rank) {
        Connection::query("INSERT INTO grouppermissions (Object_ID, Permission_Name, Rank) VALUES (?, ?, ?)",
            "isi", array($object_id, $type, $rank));
    }
    public static function deny_rank($object_id, $type, $rank) {
        Connection::query("DELETE FROM grouppermissions WHERE Object_ID = ?
                                                        AND Permission_Name = ?
                                                        AND Rank = ?", "isi",
            array($object_id, $type, $rank));
    }
}