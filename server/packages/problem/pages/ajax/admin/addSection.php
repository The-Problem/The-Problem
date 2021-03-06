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
        // prevent changing anything without being logged in as an administrator
        $username = $_SESSION["username"];
        if (!$username) return array("error" => "You do not have the required permission to perform this action");

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];
        if ($rank < 4) return array("error" => "You do not have the required permission to perform this action");

        // get the values from the URL
        $new_name = $_POST['name'];
        $new_description = $_POST['description'];
        $new_devs = $_POST['devs'];
        $new_color = $_POST['color'];

        // we need to create the object first to avoid foreign key constraint errors
        Connection::query("INSERT INTO objects (Object_Type) VALUES (0)");
        $object_id = Connection::insertid();

        // get the default values for permissions, and then allow the ranks
        Library::get('objects');
        $default_view = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-section'
                                               AND Name = 'view'");
        $default_create = Connection::query("SELECT Value FROM configuration
                                               WHERE Type = 'permissions-default-bugs'
                                               AND Name = 'create'");

        Objects::allow_rank($object_id, 'section.view', $default_view[0]["Value"]);
        Objects::allow_rank($object_id, 'section.create-bug', $default_create[0]["Value"]);

        // calculate slug to be used in the URL for the section, by removing all non-word
        // characters
        $slug = preg_replace('/\\W/', '-', strtolower($new_name));

        // multiple sections can have the same name, but slugs need to be unique,
        // so add a number to the end if required
        $sections = Connection::query("SELECT COUNT(*) AS Number FROM sections WHERE Slug LIKE ?", "s", array(
            "$slug%"
        ));
        if ($sections[0]["Number"] > 0) $slug .= $sections[0]["Number"];

        // create the section in the database
        Connection::query("INSERT INTO sections (Name, Object_ID, Description, Slug, Color) VALUES (?, ?, ?, ?, ?)", "sissi",
            array(
                $new_name, $object_id, $new_description, $slug, $new_color
            ));
        $section_id = Connection::insertid();

        // add all developers
        $query = "INSERT INTO developers (Section_ID, Username) VALUES (?, ?)";
        foreach ($new_devs as $dev) {
            Connection::query($query, "is", array($section_id, $dev));
        }

        return array("id" => $section_id);
    }
}