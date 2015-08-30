<?php
class CommentsModule implements IModule {
    public function __construct() {
        Pages::$head->stylesheet("modules/comments");
        Pages::$head->script("lib/autosize.min");
        Pages::$head->script("modules/comments");
    }

    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($bug = array(), Head $h) {
        $comments = Connection::query("SELECT *, (SELECT COUNT(*) FROM plusones
                                                  WHERE plusones.Object_ID = objects.Object_ID) AS Plus_Ones,
                                                 (SELECT COUNT(*) FROM plusones
                                                  WHERE plusones.Object_ID = objects.Object_ID
                                                    AND plusones.Username = ?) AS My_Plus_Ones,
                                                 (SELECT COUNT(*) FROM developers
                                                  WHERE developers.Section_ID = ?
                                                  AND developers.Username = comments.Username) AS Is_Developer
                                         FROM comments
                                           JOIN objects ON (comments.Object_ID = objects.Object_ID)
                                           JOIN users ON (comments.Username = users.Username)
                                         WHERE comments.Bug_ID = ?", "sii", array($_SESSION["username"], $bug["Section_ID"], $bug["Bug_ID"]));


?>
<div class="comments">
    <?php

    $poster = Connection::query("SELECT Rank, Email, (SELECT COUNT(*) FROM developers
                                               WHERE developers.Section_ID = ?
                                               AND developers.Username = users.Username) AS Is_Developer FROM users WHERE Username = ?",
        "is", array($bug["Section_ID"], $bug["Author"]));
    $plusones = Connection::query("
SELECT COUNT(*) AS Plus_Ones,
       (SELECT COUNT(*) FROM plusones
        WHERE plusones.Object_ID = ?
          AND plusones.Username = ?) AS Mine
  FROM plusones WHERE Object_ID = ?", "isi", array($bug["Bug_ObjectID"], $_SESSION["username"], $bug["Bug_ObjectID"]));

    array_unshift($comments, array(
        "Username" => $bug["Author"],
        "Email" => $poster[0]["Email"],
        "Rank" => $poster[0]["Rank"],
        "Creation_Date" => $bug["Creation_Date"],
        "Edit_Date" => $bug["Edit_Date"],
        "Comment_Text" => $bug["Bug_Description"],
        "Raw_Text" => $bug["Bug_Description"],
        "Plus_Ones" => $plusones[0]["Plus_Ones"],
        "My_Plus_Ones" => $plusones[0]["Mine"],
        "Is_Developer" => $poster[0]["Is_Developer"],
        "Object_ID" => $bug["Bug_ObjectID"],
        "Bug_Section_ID" => $bug["Section_ID"]
    ));

    Library::get("modules");
    foreach ($comments as $comment) {
        $comment["Bug_Author"] = $bug["Author"];
        $comment["Bug_Section_ID"] = $bug["Section_ID"];
        Modules::getoutput("comment", $comment);
    }

    Library::get("objects");
    $can_comment = Objects::permission($bug["Bug_ObjectID"], "bug.comment", $_SESSION["username"], $bug["Section_ID"]);


    if ($can_comment) {
        $username = $_SESSION["username"];
        if (is_null($username)) $email = "guest@example.com";
        else {
            $user = Connection::query("SELECT Email FROM users WHERE Username = ?", "s", array($username));
            $email = $user[0]["Email"];
        }

        $gravatar_id = md5(strtolower(trim($email)));
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