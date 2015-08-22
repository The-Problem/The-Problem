<?php
class ErrorPage implements IPage {
    private $redirecting = false;

    public function __construct(PageInfo &$page) {
        $path = $page->pagelist;

        $length = count($path);
        if ($length && $path[0][0] === "~") {
            $username = substr($path[0], 1);
            $remaining_path = array_slice($path, 1);
            $resulting_path = array("users", $username);

            $this->redirecting = true;
            Pages::showpagefrompath(array_merge($resulting_path, $remaining_path));
        }
    }
    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        if (!$this->redirecting) Path::redirect(Path::getclientfolder());

        return false;
    }
    public function subpages() {
        return false;
    }
    public function head(Head &$head) {
        $head->title .= " - Not Found";
    }

    public function body() { ?>
<h2 style="text-align:center; margin-top:100px;">404 Not Found</h2>
<p style="text-align:center;">We couldn't find that page.</p>
<?php }
}