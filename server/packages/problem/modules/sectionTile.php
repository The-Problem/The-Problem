<?php

class SectionTileModule implements IModule {
    public function __construct() {
        Pages::$head->stylesheet("modules/sectionTile");
    }

    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($section = array(), Head $h) {
        $path = Path::getclientfolder("bugs", $section["Slug"]);
        $name = htmlentities($section["Name"]);

        $open = $section["Open_Bugs"];
        $all = $section["All_Bugs"];

        //if ($open === 0) $open = "no";
        if ($all === 0) $all = "No";

        $style = "";
        if ($section["Color"] === 0) {
            Library::get("image");

            $img = new Image("sections", $section["Slug"], array(
                "format" => "jpg",
                "tint" => "0-0-0x0.6",
                "crop" => true,
                "width" => 150,
                "height" => 150
            ));

            $style = "background-image:url('" . htmlentities($img->clientpath) . "')";
        }

        Library::get("string");

        ?>
<section data-name="<?php echo strtolower($name); ?>">
    <a href="<?php echo $path; ?>"
       title="<?php echo htmlentities($section["Description"]); ?>"
       class="section-tile color-<?php echo $section["Color"]; ?>"
       style="<?php echo $style; ?>">
        <div class="container">
            <h3><?php echo $name; ?></h3>
            <p class="section-stats">
                <?php if ($all !== "No") {
                    ?><span class="percentage"><?php echo floor(($all - $open) / $all * 100); ?>% closed</span>
                <span class="open-all"><?php echo String::readablenumber($all); ?> bug<?php echo $all === 1 ? "" : "s"; ?><?php if ($open !== 0) { ?>,
                    <?php echo String::readablenumber($open); ?> open</span><?php } } else { ?>
                <span class="all"><?php echo $all; ?> bug<?php echo $all === 1 ? "" : "s"; ?></span><?php } ?>
            </p>
        </div>
    </a>
</section><?php
    }

    public function getsurround($code, $params) {
        return $code;
    }
}