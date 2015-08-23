<?php
class CommentsModule implements IModule {
    public function __construct() {
        Pages::$head->script("lib/jquery.timeago");

    }

    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($bug = array(), Head $h) {
        $comments = Connection::query("SELECT *, (SELECT COUNT(*) FROM plusones
                                                  WHERE plusones.Object_ID = objects.Object_ID) AS Plus_Ones
                                         FROM comments
                                         JOIN objects ON (comments.Object_ID = objects.Object_ID)
                                         JOIN users ON (comments.Username = users.Username)
                                         WHERE comments.Bug_ID = ?", "i", array($bug["Bug_ID"]));


?>
<div class="comments">
    <?php
    Library::get("parsedown");
    $parsedown = new Parsedown();

    $ranks = array(
        0 => false,
        1 => false,
        2 => "dev",
        3 => "mod",
        4 => "admin"
    );

    $poster = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($bug["Author"]));
    $plusones = Connection::query("SELECT COUNT(*) AS Plus_Ones FROM plusones WHERE Object_ID = ?", "i", array($bug["Object_ID"]));

    array_unshift($comments, array(
        "Username" => $bug["Author"],
        "Rank" => $poster[0]["Rank"],
        "Creation_Date" => $bug["Creation_Date"],
        "Edit_Date" => $bug["Edit_Date"],
        "Comment_Text" => $bug["Bug_Description"],
        "Plus_Ones" => $plusones[0]["Plus_Ones"]
    ));

    foreach ($comments as $comment) {
        $gravatar_id = md5(strtolower(trim($comment["Username"])));
        $gravatar = "http://www.gravatar.com/avatar/$gravatar_id?d=identicon&s=60";

        $rank = $ranks[$comment["Rank"]];

        $content = $parsedown->text($comment["Comment_Text"]);
        $creation_date = strtotime($comment["Creation_Date"]);

        ?>
    <div class="comment">
        <div class="left-column">
            <img src="<?php echo $gravatar; ?>" width="60" height="60" />
        </div><div class="right-column">
            <div class="header">
                <?php if ($bug["Author"] === $comment["Username"]) { ?><span class="rank op">OP</span><?php } ?>
                <?php if ($rank) {?><span class="rank <?php echo $rank; ?>"><?php echo strtoupper($rank); ?></span><?php } ?>
                <a href="<?php echo htmlentities(Path::getclientfolder("~" . $comment["Username"])); ?>"><?php echo htmlentities($comment["Username"]); ?></a>
                commented <span class="timeago" title="<?php echo date('c', $creation_date); ?>"><?php echo date('h:i A F j, Y', $creation_date); ?></span>

                <div class="right plus-one">
                    <span class="current"><?php echo $comment["Plus_Ones"]; ?></span><span class="hover"> + 1</span><span class="next"></span><i class="fa fa-thumbs-up"></i>
                </div>
            </div>
            <div class="content">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
        <?php
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