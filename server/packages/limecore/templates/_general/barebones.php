<?php
class BarebonesTemplate implements ITemplate {
    public function head(Head &$head) { }
    public function showpage(Head $head, $pagecode, IPage $page) {
        echo "<!DOCTYPE html>\n<html>\n<head>\n";
        echo $head->getcode();
        echo "</head>\n<body>\n";
        echo $pagecode;
        echo "</body>\n</html>";
    }
}