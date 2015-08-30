<?php
class AjaxAdminUpdatePage implements IPage {
    private $handlers = array();

    private function is_rank($val, $previous) {
        if ($val >= 0 && $val <= 4) return $val;
        return $previous;
    }

    public function __construct(PageInfo &$page) {
        // add handlers for each page item
        // A handler is for a specific property, and is passed the new value and the old value.
        // It should validate the value, and either return the new value or the old value, or a completely
        // new value if required.
        $this->handlers = array(
            "overview-name" => array(
                // the site name
                "sitename" => function($val, $previous) {
                    return $val;
                }
            ),
            "overview-visibility" => array(
                // the site visibility
                "visibility" => function($val, $previous) {
                    Library::get("objects");

                    // update the permission
                    if ($val === "public") {
                        Objects::allow_rank(0, "site.view", 0);
                        return $val;
                    }
                    if ($val === "private") {
                        Objects::allow_rank(0, "site.view", 1);
                        return $val;
                    }

                    return $previous;
                },
                // the site registration
                "registration" => function($val, $previous) {
                    if ($val === "open" || $val === "closed") return $val;
                    return $previous;
                }
            ),
            // default permissions for sections
            "permissions-default-section" => array(
                "view" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 0
                                           AND Permission_Name = 'section.view'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                }
            ),
            // default permissions for bugs
            "permissions-default-bugs" => array(
                "view" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'bug.view'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "status" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'bug.change-status'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "edit" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'comment.edit'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "delete" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'comment.remove'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "create" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 0
                                           AND Permission_Name = 'section.create-bug'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "assigning" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'bug.assign'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                }
            ),
            // default permissions for comments
            "permissions-default-comments" => array(
                "create" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'bug.comment'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "delete" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 2
                                           AND Permission_Name = 'comment.remove'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "edit" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 2
                                           AND Permission_Name = 'comment.edit'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "upvote" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    // update all objects with this permission that are using the old
                    // default value
                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 2
                                           AND Permission_Name = 'comment.upvote'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                }
            )
        );

    }

    public function template() {
        return Templates::findtemplate("ajax");
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
    }

    public function body() {
        // prevent changing anything without being logged in as an administrator
        $username = $_SESSION["username"];
        // if the user isn't logged in, tell the client to send them to the login page
        if (!$username) return array("error" => array("login" => true));

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];
        // if the user isn't an admin, tell the client to send them to the homepage
        if ($rank < 4) return array("error" => array("home" => true));
        // if the user's SUDO has expired, tell the client to send them to the SUDO page
        if (!$_SESSION['sudo']) return array("error" => array("sudo" => true));

        // if there is no handler registered for the property, bail
        if (!array_key_exists($_POST['type'], $this->handlers)) return array("error" => true);
        if (!array_key_exists($_POST['name'], $this->handlers[$_POST['type']])) return array("error" => true);

        // fetch the previous value
        $previous = Connection::query("SELECT Value FROM configuration WHERE Type = ? AND Name = ?", "ss", array(
            $_POST['type'],
            $_POST['name']
        ));
        if (!count($previous)) return array("error" => true);

        // get the new value from the handler function, then update it if it hasn't changed
        $newvalue = call_user_func($this->handlers[$_POST['type']][$_POST['name']], $_POST['value'], $previous[0]["Value"]);
        if ($newvalue !== $previous[0]["Value"]) {
            Connection::query("UPDATE configuration SET Value = ? WHERE Type = ? AND Name = ?", "sss", array(
                $newvalue, $_POST['type'], $_POST['name']
            ));
        }

        return array("value" => $newvalue);
    }
}