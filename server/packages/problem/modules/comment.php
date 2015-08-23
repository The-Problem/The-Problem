<?php
class CommentModule implements IModule {
    private $parsedown;
    private $ranks = array(
        0 => false,
        1 => false,
        2 => "dev",
        3 => "mod",
        4 => "admin"
    );

    public function __construct() {
        Library::get("parsedown");
        $this->parsedown = new Parsedown();
    }

    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($comment = array(), Head $h) {
        $gravatar_id = md5(strtolower(trim($comment["Email"])));
        $gravatar = "http://www.gravatar.com/avatar/$gravatar_id?d=identicon&s=60";

        $r = $comment["Rank"];
        if ($comment["Is_Developer"]) $r = max(2, $r);
        $rank = $this->ranks[$r];

        $content = $this->parsedown->text($comment["Comment_Text"]);
        $creation_date = strtotime($comment["Creation_Date"]);

        $userlink = htmlentities(Path::getclientfolder("~" . $comment["Username"]));

        ?>
<div class="comment">
    <div class="left-column">
        <a href="<?php echo $userlink; ?>"><img src="<?php echo $gravatar; ?>" width="60" height="60" /></a>
    </div><div class="right-column">
        <div class="header">
            <?php if ($comment["Bug_Author"] === $comment["Username"]) { ?><span class="rank op">OP</span><?php } ?>
            <?php if ($rank) {?><span class="rank <?php echo $rank; ?>"><?php echo strtoupper($rank); ?></span><?php } ?>
            <a href="<?php echo $userlink; ?>"><?php echo htmlentities($comment["Username"]); ?></a>
            commented <span class="timeago" title="<?php echo date('c', $creation_date); ?>"><?php echo date('h:i A F j, Y', $creation_date); ?></span>

            <div class="right plus-one">
                <a href="javascript:void(0)" title="+1 this comment">
                <span class="current"><?php echo $comment["Plus_Ones"]; ?></span><span class="hover"> + 1</span><span class="next"></span><i class="fa fa-thumbs-up"></i>
                </a>
            </div>
        </div>
        <div class="content">
            <?php echo $content; ?>
        </div>
    </div>
</div>
<?php
    }

    public function getsurround($code, $params) {
        return $code;
    }
}