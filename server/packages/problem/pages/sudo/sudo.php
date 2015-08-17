<?php
class SudoPage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        Library::get("cookies");
        if (Cookies::prop("sudo")) {
            Path::redirect($_GET['return']);
            return true;
        }

        $username = Cookies::prop("username");
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
            // todo: password hashing
            $res = Connection::query("SELECT COUNT(*) AS Count FROM users WHERE Username = ? AND Password = ?", "ss", array(
                Cookies::prop("username"),
                $_POST['password']
            ));

            if ($res[0]["Count"]) {
                Cookies::prop("sudo", true);
                Path::redirect($_GET['return']);
            } else $message = "Invalid password. Please try again.";
        }

        ?>
<div id="loginBox">
    <form method="post">
        <h1>Entering sudo mode...</h1>
        <p><?php echo $message; ?></p>
        <input id="passField" type="password" name="password" placeholder="Password" /><br>
        <button action="submit">CONTINUE</button>
    </form>
</div>
<?php
    }
}