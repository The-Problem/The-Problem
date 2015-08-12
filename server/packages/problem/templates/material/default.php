<?php
class DefaultTemplate implements ITemplate {
    private $body_classes = array(
        "nojs" => true
    );
    private $options = array(
        "showerror" => true,
        "shownotice" => true,
        "header" => true
    );
    private $funcs = array();

    public function __construct() {

    }

    public function Head(Head &$head) {
        $head->stylesheet("templates/default");
        $head->stylesheet("http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,700,700italic", true);

        $head->package("problem");
        $head->addcode("<link type='text/plain' rel='author' href='" . Path::getclientfolder() . "humans.txt' />");
    }

    public function big() {
        $this->add_class("big-header");
        return $this;
    }

    public function add_class($name) {
        $this->body_classes[$name] = true;
        return $this;
    }
    public function remove_class($name) {
        $this->body_classes[$name] = false;
        return $this;
    }
    public function option($name, $value = NULL) {
        if (!is_null($value)) $this->options[$name] = $value;
        return $this->options[$name];
    }
    public function no_error() {
        $this->option("showerror", false);
        return $this;
    }
    public function no_notice() {
        $this->option("shownotice", false);
        return $this;
    }
    public function no_header() {
        $this->option("header", false);
        return $this;
    }

    public function showpage(Head $head, $pagecode, IPage $page) {
        $classes = array();
        foreach ($this->body_classes as $name => $has) {
            if ($has) array_push($classes, $name);
        }

        ob_start();
        echo $this->header();
        echo $pagecode;
        echo $this->footer();
        $out = ob_get_clean();

        echo "<!DOCTYPE html>\n";

        $messages = array(
            "Don't trust strangers.",
            "This is a --><!-- useless comment",
            "Join the The Problem",
            "Go do something actually productive",
            "'Be one with the problem, young Skywalker'"
        );
        $motd = $messages[mt_rand(0, count($messages) - 1)];

        echo "<!-- MOTD: $motd -->";
        echo "<html>\n<head>\n";
        echo $head->getcode();
        echo "</head>\n<body class='" . implode(' ', $classes) . "'>\n";
        echo $out;
        echo "</body>\n</html>";
    }

    public function on_error($message) {
        echo "<div class='error'>$message</div>";
    }

    private function header() {
        Library::get("image");
        $logo = new Image("branding", "logo-text", array(
            "format" => "png",
            "height" => 40,
            "width" => 160
        ));

        if ($this->option("header")) {
            echo "<header>";

            echo '<h1 class="title"><a href="' . htmlentities(Path::getclientfolder()) . '" title="Home">' .
                '<img alt="The Problem" title="Home" src="' . $logo->clientpath . '" />' . '</a></h1>';

            echo "</header>";
        }
        echo "<div class='body'>";

        if (isset($_GET['notice']) && $this->option("shownotice")) echo '<p class="notice">' . htmlentities($_GET['notice']) . '</p>';
        if (isset($_GET['error']) && $this->option("showerror")) echo '<p class="error">' . htmlentities($_GET['error']) . '</p>';

        return "";
    }
    private function footer() {
        if (LIME_ENV === LIME_ENV_DEV && LIME_TERMINAL_MODE === LIME_TERMINAL_ENABLED) {
            Library::get("modules");
            Modules::getoutput("terminal");
        }

        echo "</div>";
        echo "<div class='page-time'>" . (Timer::get(6) * 1000) . "ms</div>";

        return "";
    }
}