<?php

class Objects {
    const TYPE_SECTION = 0;
    const TYPE_BUG = 1;
    const TYPE_COMMENT = 2;

    private static $permissions = array();

    /**
     * Finds if a user has a certain permission granted
     *
     * @param integer  $object_id Object ID to check permissions on
     * @param string   $type      Name of the permission to find
     * @param string?  $username  Username to check, or NULL for a guest
     * @param integer? $section   The section ID the object exists in, for Developer ranks
     * @return bool Whether the permission is granted
     */
    public static function permission($object_id, $type, $username = NULL, $section = NULL) {
        $keyname = "0$object_id.$username.$type";

        // cache permissions
        if (array_key_exists($keyname, self::$permissions)) return self::$permissions[$keyname];

        // "this should be a fun query!"
        $results = Connection::query("
SELECT COUNT(*) AS Has_Permission,
  userpermissions.Username,
  users.Username,
  grouppermissions.Rank,
  GREATEST(
      CASE WHEN developers.Username IS NULL
        THEN 0
      ELSE 2
      END,
      COALESCE(users.Rank, 0)
  ) AS User_rank
FROM objects
  LEFT JOIN users ON (users.Username = ?)
  LEFT JOIN sections ON (objects.Object_Type = 0 AND sections.Object_ID = objects.Object_ID)
  LEFt JOIN developers ON (developers.Section_ID = COALESCE(?, sections.Section_ID) AND developers.Username = users.Username)

  LEFT JOIN userpermissions ON (userpermissions.Permission_Name = ?
                                AND userpermissions.Object_ID = objects.Object_ID)
  LEFT JOIN grouppermissions ON (grouppermissions.Permission_Name = ?
                                 AND grouppermissions.Object_ID = objects.Object_ID)
WHERE objects.Object_ID = ?
  AND (? IS NULL OR users.Username = ?)
HAVING userpermissions.Username = users.Username
       OR User_rank >= grouppermissions.Rank", "sississ", array($username, $section, $type, $type, $object_id, $username, $username));
        // "KILL ME NOW."

        // the value is stored in the "Has_Permission" column
        $value = $results[0]["Has_Permission"] > 0;
        // add to cache
        self::$permissions[$keyname] = $value;
        return $value;
    }

    /**
     * Fetches a list of all objects that are granted for the user
     * of the type
     *
     * @param string   $type     Type of permission to be checking
     * @param string?  $username User to check against
     * @param integer? $section  The section ID the objects exists in, for Developer ranks
     * @return array All object IDs with the permission granted
     */
    public static function user_permissions($type, $username = NULL, $section = NULL) {
        $keyname = "1$username.$type";

        // cache permissions
        if (array_key_exists($keyname, self::$permissions) && self::$permissions[$keyname] !== FALSE) return self::$permissions[$keyname];

        // and another query
        $results = Connection::query("
SELECT userpermissions.Username,
  users.Username,
  userpermissions.Object_ID AS Object_ID1,
  grouppermissions.Object_ID AS Object_ID2,
  grouppermissions.Rank,
  GREATEST(
      CASE WHEN developers.Username IS NULL
        THEN 0
      ELSE 2
      END,
      COALESCE(users.Rank, 0)
  ) AS User_rank
FROM objects
  LEFT JOIN users ON (users.Username = ?)
  LEFT JOIN sections ON (objects.Object_Type = 0 AND sections.Object_ID = objects.Object_ID)
  LEFt JOIN developers ON (developers.Section_ID = COALESCE(?, sections.Section_ID) AND developers.Username = users.Username)

  LEFT JOIN userpermissions ON (userpermissions.Permission_Name = ?
                                AND userpermissions.Object_ID = objects.Object_ID)
  LEFT JOIN grouppermissions ON (grouppermissions.Permission_Name = ?
                                 AND grouppermissions.Object_ID = objects.Object_ID)
WHERE (? IS NULL OR users.Username = ?)
HAVING userpermissions.Username = users.Username
       OR User_rank >= grouppermissions.Rank", "sissss", array($username, $section, $type, $type, $username, $username));

        // convert results into a sane format and cache
        $value = array();
        foreach ($results as $item) {
            if (!is_null($item["Object_ID1"])) {
                array_push($value, $item["Object_ID1"]);
                self::$permissions["0" . $item["Object_ID1"] . ".$username.$type"] = true;
            }
            if (!is_null($item["Object_ID2"])) {
                array_push($value, $item["Object_ID2"]);
                self::$permissions["0" . $item["Object_ID2"] . ".$username.$type"] = true;
            }
        }

        self::$permissions[$keyname] = $value;
        return $value;
    }

    /**
     * Grant a permission on an object to a user
     *
     * @param integer $object_id Object ID of the permission to grant
     * @param string  $type      Name of the permission to grant
     * @param string  $username  User to grant the permission to
     */
    public static function allow_user($object_id, $type, $username) {
        Connection::query("INSERT INTO userpermissions (Object_ID, Permission_Name, Username) VALUES (?, ?, ?)",
            "iss", array($object_id, $type, $username));

        self::$permissions["0$object_id.$username.$type"] = true;

        $multi = self::$permissions["1$username.$type"];
        if (!in_array($object_id, $multi)) array_push($multi, $object_id);
    }

    /**
     * Deny a permission on an object to a user
     *
     * @param integer $object_id Object ID of the permission to deny
     * @param string  $type      Name of the permission to deny
     * @param string  $username  User to deny the permission from
     */
    public static function deny_user($object_id, $type, $username) {
        Connection::query("DELETE FROM userpermissions WHERE Object_ID = ?
                                                       AND Permission_Name = ?
                                                       AND Username = ?", "iss",
            array($object_id, $type, $username));

        self::$permissions["0$object_id.$username.$type"] = false;

        $key = "1$username.$type";
        self::$permissions[$key] = array_diff(self::$permissions[$key], array($object_id));
    }

    /**
     * Grant a permission on an object to any users in or above a rank
     *
     * @param integer $object_id Object ID of the permission to grant
     * @param string  $type      Name of the permission to grant
     * @param integer $rank      Rank to grant the permission to
     */
    public static function allow_rank($object_id, $type, $rank) {
        Connection::query("INSERT INTO grouppermissions (Object_ID, Permission_Name, Rank) VALUES (?, ?, ?)
                           ON DUPLICATE KEY UPDATE Rank = ?",
            "isii", array($object_id, $type, $rank, $rank));
    }

    /**
     * Deny a permission on an object to any users in or above a rank
     *
     * @param integer $object_id Object ID of the permission to deny
     * @param string  $type      Name of the permission to deny
     * @param integer $rank      Rank to deny the permission from
     */
    public static function deny_rank($object_id, $type, $rank) {
        Connection::query("DELETE FROM grouppermissions WHERE Object_ID = ?
                                                        AND Permission_Name = ?
                                                        AND Rank = ?", "isi",
            array($object_id, $type, $rank));
    }

    /**
     * Deny a permission for all ranks on an object
     *
     * @param integer $object_id Object ID of the permission to deny
     * @param string  $type      Name of the permission to deny
     */
    public static function deny_all_groups($object_id, $type) {
        Connection::query("DELETE FROM grouppermissions WHERE Object_ID = ?
                                                          AND Permission_Name = ?", "is", array(
            $object_id, $type
        ));
    }
}