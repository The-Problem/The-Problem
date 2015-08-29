<?php
class AjaxAdminAddSectionPage implements IPage {
    public function __construct(PageInfo &$page) {
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

    public function head(Head &$head) { }

    public function body() {
        $username = $_SESSION["username"];
        if (!$username) return array("error" => "You do not have the required permission to perform this action");

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];
        if ($rank < 4) return array("error" => "You do not have the required permission to perform this action");

        $new_name = $_POST['name'];
        $new_description = $_POST['description'];
        $new_devs = $_POST['devs'];
        $new_color = $_POST['color'];

        Connection::query("INSERT INTO objects (Object_Type) VALUES (0)");
        $object_id = Connection::insertid();

        Library::get('objects');
        $default_view = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-section'
                                               AND Name = 'view'");
        $default_create = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-bugs'
                                               AND Name = 'create'");

        Objects::allow_rank($object_id, 'section.view', $default_view[0]["Value"]);
        Objects::allow_rank($object_id, 'section.create-bug', $default_create[0]["Value"]);

        // calculate slug
        $slug = preg_replace('/\\W/', '-', strtolower($new_name));

        $sections = Connection::query("SELECT COUNT(*) AS Number FROM sections WHERE Slug LIKE ?", "s", array(
            "$slug%"
        ));
        if ($sections[0]["Number"] > 0) $slug .= $sections[0]["Number"];

        Connection::query("INSERT INTO sections (Name, Object_ID, Description, Slug, Color) VALUES (?, ?, ?, ?, ?)", "sissi",
            array(
                $new_name, $object_id, $new_description, $slug, $new_color
            ));
        $section_id = Connection::insertid();

        $query = "INSERT INTO developers (Section_ID, Username) VALUES (?, ?)";
        foreach ($new_devs as $dev) {
            Connection::query($query, "is", array($section_id, $dev));
        }

        return array("id" => $section_id);
    }
}