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
     * @param User $user
     * @param string $type
     * @return bool
     */
    public function has_permission($user, $type) {
        Library::get("connection");

        $results = Connection::query("SELECT COUNT(users.Username) FROM users
                             WHERE users.Username = ?
                             AND (userpermissions.Username = users.Username
                                  OR grouppermissions.Rank = users.Rank)
                             AND userpermissions.Permission_Name = ?
                             AND grouppermissions.Permission_Name = ?", "sss", array(
            $user->username, $type, $type
        ));
    }
}