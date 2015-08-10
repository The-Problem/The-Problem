<?php

class TerminalModule implements IModule {
    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($params = array(), Head $h) {
        Pages::$head->stylesheet("modules/terminal");
        Pages::$head->script("modules/terminal");

        ?>
<div class="terminal">
    <div class="output">
        <pre><span class="out"></span><span class="prompt">$ </span><span class="in"></span><span class="cursor">_</span></pre>
    </div>

    <input type="text" style="position:absolute;left:-9999em" />
</div>
<?php }

    public function getsurround($code, $params) {
        return $code;
    }
}