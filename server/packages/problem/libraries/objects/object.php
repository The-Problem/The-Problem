<?php

class Object {
    const TYPE_COMMENT = 1;
    const TYPE_BUG = 2;
    const TYPE_SECTION = 3;

    public $id = -1;
    public $type = false;

    private $permissions = array();

    public function __construct($info = false) {
        if ($info) {
            $this->id = $info["Object_ID"];
            $this->type = $info["Object_Type"];
        }
    }

    /**
     * @param string $username
     * @param string $type
     * @return bool
     */
    public function has_permission($username, $type) {
        Library::get("connection");

        $results = Connection::query("
SELECT COUNT(users.Username) FROM users
  LEFT JOIN userpermissions ON (userpermissions.Username = users.Username
                                AND userpermissions.Object_ID = ?
                                AND userpermissions.Permission_Name = ?)
  LEFT JOIN grouppermissions ON (users.Rank >= grouppermissions.Rank
                                 AND grouppermissions.Object_ID = userpermissions.Object_ID
                                 AND grouppermissions.Permission_Name = userpermissions.Permission_Name)
WHERE users.Username = ?
      AND (userpermissions.Username = users.Username
           OR users.Rank >= grouppermissions.Rank)", "iss", array(
            $this->id, $type, $username
        ));
    }
}