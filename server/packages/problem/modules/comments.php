<?php
class CommentsModule implements IModule {
    public function __construct() {

    }

    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($bug = array(), Head $h) {
        $comments = Connection::query("SELECT * FROM comments
                                         JOIN objects ON (comments.Object_ID = objects.Object_ID)
                                         WHERE Bug_ID = ?", "i", array($bug["Bug_ID"]));
        

?>
<div class="comments">
    <?php
    foreach ($comments as $comment) {
        echo "<p>" . htmlentities($comment["Comment_Text"]) . "</p>";
    }
    ?>
</div>
<?php
    }

    public function getsurround($code, $params) {
        Pages::$head->stylesheet("modules/comments");
        return $code;
    }
}