<?php
class AdminSectionModule implements IModule {
    public function __construct() {

    }

    public function spinnersize() { return Modules::SPINNER_SMALL; }

    public function getcode($params = array(), Head $h) {
        /**
         * Parameters:
         *
         * "id" => The ID of the section
         */

        $sections = Connection::query("SELECT * FROM sections WHERE Section_ID = ?", "i", array($params["id"]));
        $section = $sections[0];
        $name = htmlentities($section["Name"]);

        $style = "";
        if ($section["Color"] === 0) {
            Library::get("image");

            $img = new Image("sections", $section["Slug"], array(
                "format" => "jpg",
                "crop" => true,
                "width" => 150,
                "height" => 150
            ));
            $style = "background-image:url('" . htmlentities($img->clientpath) . "')";
        }

        ?>
<div class="section-header">
    <a class="close" href="#"><i class="fa fa-times"></i></a>
    <div class="section-tile color-<?php echo $section["Color"]; ?>" style="<?php echo $style; ?>"></div>
    <div class="right-column">
        <h2><?php echo $name; ?></h2>
        <p class="description"><?php echo htmlentities($section["Description"]); ?></p>
    </div>
</div>
<?php
    }

    public function getsurround($code, $params) {
        return $code;
    }
}