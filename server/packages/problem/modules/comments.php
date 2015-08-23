<?php
class CommentsModule implements IModule {
    public function __construct() {
        Pages::$head->stylesheet("modules/comments");
        Pages::$head->script("lib/jquery.timeago");
        Pages::$head->script("lib/autosize.min");
        Pages::$head->script("modules/comments");
    }

    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($bug = array(), Head $h) {
        $comments = Connection::query("SELECT *, (SELECT COUNT(*) FROM plusones
                                                  WHERE plusones.Object_ID = objects.Object_ID) AS Plus_Ones,
                                                 (SELECT COUNT(*) FROM developers
                                                  WHERE developers.Section_ID = ?
                                                  AND developers.Username = comments.Username) AS Is_Developer
                                         FROM comments
                                         JOIN objects ON (comments.Object_ID = objects.Object_ID)
                                         JOIN users ON (comments.Username = users.Username)
                                         WHERE comments.Bug_ID = ?", "ii", array($bug["Section_ID"], $bug["Bug_ID"]));


?>
<div class="comments">
    <?php

    $poster = Connection::query("SELECT Rank, Email, (SELECT COUNT(*) FROM developers
                                               WHERE developers.Section_ID = ?
                                               AND developers.Username = users.Username) AS Is_Developer FROM users WHERE Username = ?",
        "is", array($bug["Section_ID"], $bug["Author"]));
    $plusones = Connection::query("SELECT COUNT(*) AS Plus_Ones FROM plusones WHERE Object_ID = ?", "i", array($bug["Object_ID"]));

    array_unshift($comments, array(
        "Username" => $bug["Author"],
        "Email" => $poster[0]["Email"],
        "Rank" => $poster[0]["Rank"],
        "Creation_Date" => $bug["Creation_Date"],
        "Edit_Date" => $bug["Edit_Date"],
        "Comment_Text" => $bug["Bug_Description"],
        "Plus_Ones" => $plusones[0]["Plus_Ones"],
        "Is_Developer" => $poster[0]["Is_Developer"]
    ));

    Library::get("modules");
    foreach ($comments as $comment) {
        $comment["Bug_Author"] = $bug["Author"];
        Modules::getoutput("comment", $comment);
    }
    ?>

    <?php
    $username = $_SESSION["username"];
    if ($username) {
        $user = Connection::query("SELECT Email FROM users WHERE Username = ?", "s", array($username));
        $gravatar_id = md5(strtolower(trim($user[0]["Email"])));
        $gravatar = "http://www.gravatar.com/avatar/$gravatar_id?d=identicon&s=60";
    ?>
    <div class="comment new" data-bug-id="<?php echo $bug["Bug_ID"]; ?>">
        <div class="left-column">
            <a href="<?php echo htmlentities(Path::getclientfolder("~$username")); ?>"><img src="<?php echo $gravatar; ?>" width="60" height="60" /></a>
        </div><div class="right-column">

            <div class="content">
                <form>
                    <textarea placeholder="Something to say?"></textarea>
                    <div class="buttons">
                        <label><input type="checkbox" checked />Comment on enter</label>
                        <div class="right">
                            <button type="submit" class="highlight">COMMENT</button>
                        </div>
                        <div class="clear-fix"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php
    }

    public function getsurround($code, $params) {
        return $code;
    }
}