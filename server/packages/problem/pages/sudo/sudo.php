<?php
class SudoPage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        if ($_SESSION['sudo']) {
            Path::redirect($_GET['return']);
            return true;
        }

        $username = $_SESSION["username"];
        if (!$username) return false;

        $rank_res = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($username));
        $rank = $rank_res[0]["Rank"];
        return $rank >= 4;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
        $head->stylesheet("pages/login");
    }

    public function body() {
        $message = "Please re-enter your password. You won't need to do this for another hour.";

        if (isset($_POST['password'])) {
            $res = Connection::query("SELECT Password FROM users WHERE Username = ?", "s", array($_SESSION['username']));

            Library::get("password");
            if (password_verify($_POST['password'], $res[0]["Password"])) {
                $_SESSION["sudo"] = time();
                Path::redirect($_GET['return']);
            } else $message = "Invalid password. Please try again.";
            
        }

        ?>
<div id="loginBox">
    <form method="post">
        <h1>Entering sudo mode...</h1>
        <p><?php echo $message; ?></p>
        <input id="passField" type="password" name="password" placeholder="Password" /><br>
        <button type="submit">CONTINUE</button>
    </form>
</div>
<?php
    }
}