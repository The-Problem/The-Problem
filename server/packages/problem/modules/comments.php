<?php
class CommentsModule implements IModule {
    public function __construct() {
        Pages::$head->stylesheet("modules/comments");
        Pages::$head->script("lib/autosize.min");
        Pages::$head->script("modules/comments");
    }

    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($bug = array(), Head $h) {
        // get all of the comments and some extra required fields
        $comments = Connection::query("SELECT *, (SELECT COUNT(*) FROM plusones
                                                  WHERE plusones.Object_ID = objects.Object_ID) AS Plus_Ones,
                                                 (SELECT COUNT(*) FROM plusones
                                                  WHERE plusones.Object_ID = objects.Object_ID
                                                    AND plusones.Username = ?) AS My_Plus_Ones,
                                                 (SELECT COUNT(*) FROM developers
                                                  WHERE developers.Section_ID = ?
                                                  AND developers.Username = comments.Username) AS Is_Developer,
                                                  comments.Object_ID AS Object_ID
                                         FROM comments
                                           JOIN objects ON (comments.Object_ID = objects.Object_ID)
                                           JOIN users ON (comments.Username = users.Username)
                                         WHERE comments.Bug_ID = ?", "sii", array($_SESSION["username"], $bug["Section_ID"], $bug["Bug_ID"]));


?>
<div class="comments">
    <?php

    // The OP (original post) is also displayed as a comment, so we need to convert the data from the bugs
    // table into something the "comment" module can understand.

    // get info on the poster of the bug
    $poster = Connection::query("SELECT Rank, Email, (SELECT COUNT(*) FROM developers
                                               WHERE developers.Section_ID = ?
                                               AND developers.Username = users.Username) AS Is_Developer FROM users WHERE Username = ?",
        "is", array($bug["Section_ID"], $bug["Author"]));

    // find how many people has plus-oned the bug
    $plusones = Connection::query("
SELECT COUNT(*) AS Plus_Ones,
       (SELECT COUNT(*) FROM plusones
        WHERE plusones.Object_ID = ?
          AND plusones.Username = ?) AS Mine
  FROM plusones WHERE Object_ID = ?", "isi", array($bug["Bug_ObjectID"], $_SESSION["username"], $bug["Bug_ObjectID"]));

    // create the OP array
    array_unshift($comments, array(
        "Username" => $bug["Author"],
        "Email" => $poster[0]["Email"],
        "Rank" => $poster[0]["Rank"],
        "Creation_Date" => $bug["Creation_Date"],
        "Edit_Date" => $bug["Edit_Date"],
        "Comment_Text" => $bug["Bug_Description"],
        "Raw_Text" => $bug["Bug_Raw_Description"],
        "Plus_Ones" => $plusones[0]["Plus_Ones"],
        "My_Plus_Ones" => $plusones[0]["Mine"],
        "Is_Developer" => $poster[0]["Is_Developer"],
        "Object_ID" => $bug["Bug_ObjectID"],
        "Bug_Section_ID" => $bug["Section_ID"]
    ));

    // display all of the comments
    Library::get("modules");
    foreach ($comments as $comment) {
        // we need this in the "comment" module for the OP badge
        $comment["Bug_Author"] = $bug["Author"];
        $comment["Bug_Section_ID"] = $bug["Section_ID"];
        Modules::getoutput("comment", $comment);
    }

    // find if we are allowed to comment
    Library::get("objects");
    $can_comment = Objects::permission($bug["Bug_ObjectID"], "bug.comment", $_SESSION["username"], $bug["Section_ID"]);


    if ($can_comment) {
        // show the comment format if we can comment
        $username = $_SESSION["username"];
        if (is_null($username)) $email = "guest@example.com";
        else {
            $user = Connection::query("SELECT Email FROM users WHERE Username = ?", "s", array($username));
            $email = $user[0]["Email"];
        }

        // we want to display the current users icon
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