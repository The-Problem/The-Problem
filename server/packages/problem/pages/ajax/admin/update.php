<?php
class AjaxAdminUpdatePage implements IPage {
    private $handlers = array();

    private function is_rank($val, $previous) {
        if ($val >= 0 && $val <= 4) return $val;
        return $previous;
    }

    public function __construct(PageInfo &$page) {
        $is_rank = function($val, $previous) {
            if ($val >= 0 && $val <= 4) return $val;
            return $previous;
        };

        $this->handlers = array(
            "overview-name" => array(
                "sitename" => function($val, $previous) {
                    return $val;
                }
            ),
            "overview-visibility" => array(
                "visibility" => function($val, $previous) {
                    Library::get("objects");

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
                "registration" => function($val, $previous) {
                    if ($val === "open" || $val === "closed") return $val;
                    return $previous;
                }
            ),
            "permissions-default-section" => array(
                "view" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 0
                                           AND Permission_Name = 'section.view'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                }
            ),
            "permissions-default-bugs" => array(
                "view" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

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

                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'bug.edit'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "delete" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'bug.delete'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "create" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

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

                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 1
                                           AND Permission_Name = 'bug.assign'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                }
            ),
            "permissions-default-comments" => array(
                "create" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

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

                    Connection::query("UPDATE grouppermissions
                                         JOIN objects ON (objects.Object_ID = grouppermissions.Object_ID)
                                       SET grouppermissions.Rank = ?
                                         WHERE objects.Object_Type = 2
                                           AND Permission_Name = 'comment.delete'
                                           AND Rank = ?", "ii", array($newval, $previous));

                    return $newval;
                },
                "edit" => function($val, $previous) {
                    $newval = $this->is_rank($val, $previous);

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
        $username = $_SESSION["username"];
        if (!$username) return array("error" => array("login" => true));

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];

        if ($rank < 4) return array("error" => array("home" => true));
        if (!$_SESSION['sudo']) return array("error" => array("sudo" => true));

        if (!array_key_exists($_POST['type'], $this->handlers)) return array("error" => true);
        if (!array_key_exists($_POST['name'], $this->handlers[$_POST['type']])) return array("error" => true);

        $previous = Connection::query("SELECT Value FROM configuration WHERE Type = ? AND Name = ?", "ss", array(
            $_POST['type'],
            $_POST['name']
        ));
        if (!count($previous)) return array("error" => true);

        $newvalue = call_user_func($this->handlers[$_POST['type']][$_POST['name']], $_POST['value'], $previous[0]["Value"]);
        if ($newvalue !== $previous[0]["Value"]) {
            Connection::query("UPDATE configuration SET Value = ? WHERE Type = ? AND Name = ?", "sss", array(
                $newvalue, $_POST['type'], $_POST['name']
            ));
        }

        return array("value" => $newvalue);
    }
}