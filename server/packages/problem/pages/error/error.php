<?php
class ErrorPage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return true;
    }
    public function head(Head &$head) {
        $head->title .= " - Not Found";
    }

    public function body() { ?>
<h2 style="text-align:center; margin-top:100px;">404 Not Found</h2>
<p style="text-align:center;">We couldn't find that page.</p>
<?php }
}