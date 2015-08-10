<?php

class Object {
    const TYPE_COMMENT = 1;
    const TYPE_BUG = 2;
    const TYPE_SECTION = 3;

    public $id = -1;
    public $type = false;

    public function __construct($info = false) {
        if ($info) {
            $this->id = $info["Object_ID"];
            $this->type = $info["Object_Type"];
        }
    }
}