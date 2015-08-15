<?php

class SectionTileModule implements IModule {
    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($section = array(), Head $h) {
        $path = Path::getclientfolder($section["Slug"]);
        $name = htmlentities($section["Name"]);

        $open = $section["Open_Bugs"];
        $all = $section["All_Bugs"];

        if ($open === 0) $open = "No";
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

        ?>
<section>
    <a href="<?php echo $path; ?>"
       title="<?php echo htmlentities($section["Description"]); ?>"
       class="color-<?php echo $section["Color"]; ?>"
       style="<?php echo $style; ?>">
        <div class="container">
            <h3><?php echo $name; ?></h3>
            <p class="section-stats">
                <?php if ($all !== "No") { ?><span class="open"><?php echo $open; ?> open bug<?php echo $open === 1 ? "" : "s"; ?></span><?php } ?>
                <span class="all"><?php echo $all; ?> bug<?php echo $all === 1 ? "" : "s"; ?></span>
            </p>
        </div>
    </a>
</section><?php
    }

    public function getsurround($code, $params) {
        Pages::$head->stylesheet("modules/section");

        return $code;
    }
}