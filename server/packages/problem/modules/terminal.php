<?php

class TerminalModule implements IModule {
    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($params = array(), Head $h) {
        ?>
<div class="terminal">
    <div class="slider"></div>
    <div class="output">
        <pre><span class="out"></span><span class="prompt">$ </span><span class="in"></span><span class="cursor">_</span></pre>
    </div>

    <input type="text" style="position:absolute;left:-9999em" />
</div>
<?php }

    public function getsurround($code, $params) {
        Pages::$head->stylesheet("modules/terminal");
        Pages::$head->script("modules/terminal");

        Pages::$head->addcode('<script src="' . Path::getclientfolder("res", "js", "lib") . 'prism.js" data-manual></script>');

        return $code;
    }
}