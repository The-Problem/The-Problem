<?php
class AjaxTemplate implements ITemplate {
    public function head(Head &$head) { }
    public function showpage(Head $head, $pagecode, IPage $page) {
        echo json_encode((array)$pagecode);
    }
}