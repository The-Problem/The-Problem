<?php
class BlankTemplate implements ITemplate {
    public function head(Head &$head) { }
    public function showpage(Head $head, $pagecode, IPage $page) {
        echo $pagecode;
    }
}