<?php
class AjaxAdminUpdatePage implements IPage {
    private $handlers = array();

    public function __construct(PageInfo &$page) {
        $this->handlers = array(
            "overview-name" => array(
                "sitename" => function($val, $previous) {
                    return $val;
                }
            ),
            "overview-visibility" => array(
                "visibility" => function($val, $previous) {
                    if ($val === "public" || $val === "private") return $val;
                    return $previous;
                },
                "registration" => function($val, $previous) {
                    if ($val === "open" || $val === "closed") return $val;
                    return $previous;
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
        Library::get("cookies");
        $username = Cookies::prop("username");
        if (!$username) return array("error" => array("login" => true));

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];

        if ($rank < 4) return array("error" => array("home" => true));
        if (!Cookies::prop("sudo")) return array("error" => array("sudo" => true));

        if (!array_key_exists($_POST['type'], $this->handlers)) return array("error" => array());
        if (!array_key_exists($_POST['name'], $this->handlers[$_POST['type']])) return array("error" => array());

        $previous = Connection::query("SELECT Value FROM configuration WHERE Type = ? AND Name = ?", "ss", array(
            $_POST['type'],
            $_POST['name']
        ));
        if (!count($previous)) return array("error" => array());

        $newvalue = call_user_func($this->handlers[$_POST['type']][$_POST['name']], $_POST['value'], $previous[0]["Value"]);
        if ($newvalue !== $previous[0]["Value"]) {
            Connection::query("UPDATE configuration SET Value = ? WHERE Type = ? AND Name = ?", "sss", array(
                $newvalue, $_POST['type'], $_POST['name']
            ));
        }

        return array("value" => $newvalue);
    }
}