<?php

class Objects {
    const TYPE_SECTION = 0;
    const TYPE_BUG = 1;
    const TYPE_COMMENT = 2;

    private static $permissions = array();

    public static function permission($object_id, $type, $username = NULL, $section = NULL) {
        $keyname = "0$object_id.$username.$type";
        if (array_key_exists($keyname, self::$permissions)) return self::$permissions[$keyname];

        $results = Connection::query("
SELECT COUNT(*) AS Has_Permission,
  userpermissions.Username,
  users.Username,
  grouppermissions.Rank, GREATEST(
              CASE WHEN developers.Username IS NULL
                THEN 0
              ELSE 2
              END,
              COALESCE(users.Rank, 0)
          ) AS User_rank
FROM objects
  LEFT JOIN users ON (users.Username = ?)
  JOIN sections ON (sections.Object_ID = objects.Object_ID)
  JOIN developers ON (developers.Section_ID = COALESCE(?, sections.Section_ID) AND developers.Username = users.Username)

  LEFT JOIN userpermissions ON (userpermissions.Username = users.Username
                                AND userpermissions.Permission_Name = ?
                                AND userpermissions.Object_ID = objects.Object_ID)
  LEFT JOIN grouppermissions ON (grouppermissions.Permission_Name = ?
                                 AND grouppermissions.Object_ID = objects.Object_ID)
WHERE objects.Object_ID = ? AND (? IS NULL OR users.Username = ?)
HAVING userpermissions.Username = users.Username
  OR User_rank >= grouppermissions.Rank", "ssssiss", array($username, $section, $type, $type, $object_id, $username, $username));

        $value = $results[0]["Has_Permission"] > 0;
        self::$permissions[$keyname] = $value;
        return $value;
    }

    public static function user_permissions($type, $username = NULL, $section = NULL) {
        $keyname = "1$username.$type";
        if (array_key_exists($keyname, self::$permissions) && self::$permissions[$keyname] !== FALSE) return self::$permissions[$keyname];

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