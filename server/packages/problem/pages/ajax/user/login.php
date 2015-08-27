<?php
class AjaxUserLoginPage implements IPage {
    public function __construct(PageInfo &$info) {

    }

    public function template() {
        return Templates::findtemplate("ajax");
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) { }
    public function body() {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (strlen($username) > 0 && strlen($password) > 0) {
            Library::get("users");
            $loggedin = Users::login($username, $password);

            if ($loggedin) {
                $name = Connection::query("SELECT Name FROM users WHERE Username = ?", "s", array($username));

                $username = $_SESSION["username"];

                Library::get("objects");
                $ids = Objects::user_permissions("section.view", $username);
                $amount = count($ids);
                $clause = implode(',', array_fill(0, $amount, '?'));
                $types = str_repeat('i', $amount);
                $params = array_merge(array($username), $ids);

                $devSections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE Section_ID IN (SELECT Section_ID FROM developers
                       WHERE developers.Username = ?)
  AND sections.Object_ID IN ($clause)
ORDER BY Open_Bugs DESC, All_Bugs DESC", "s$types", $params);

                $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE Section_ID NOT IN (SELECT Section_ID FROM developers
                           WHERE developers.Username = ?)
  AND sections.Object_ID IN ($clause)
ORDER BY Open_Bugs DESC, All_Bugs DESC", "s$types", $params);

                Library::get("modules");

                Library::get("notifications");

                $bugs = Connection::query("
SELECT *, (SELECT COUNT(*) FROM comments
           WHERE comments.Bug_ID = bugs.Bug_ID) AS Comments,
          (SELECT COUNT(*) FROM plusones
           WHERE plusones.Object_ID = bugs.Object_ID) AS Plusones,
          bugs.Name AS Bug_Name,
          sections.Name AS Section_Name
  FROM bugs
    JOIN sections ON bugs.Section_ID = sections.Section_ID
  WHERE Author = ? OR Assigned = ?
ORDER BY Edit_Date DESC, Creation_Date DESC LIMIT 5", "ss", array($username, $username));

                $notifications = Notifications::get(10 - count($bugs));

                ob_start();
                Modules::getoutput("headerBar");
                $header = ob_get_flush();

                return array(
                    "name" => $name[0]["Name"],
                    "username" => $username,
                    "devSections" => implode('', array_map(function($item) {
                        ob_start();
                        Modules::getoutput("sectionTile", $item);
                        return ob_get_flush();
                    }, $devSections)),
                    "sections" => implode('', array_map(function($item) {
                        ob_start();
                        Modules::getoutput("sectionTile", $item);
                        return ob_get_flush();
                    }, $sections)),
                    "notifications" => $notifications,
                    "myBugs" => implode('', array_map(function($bug) {
                        $url = Path::getclientfolder($bug["Slug"], $bug["RID"]);
                        $title = htmlentities($bug["Bug_Name"]);

                        $comments = $bug["Comments"];
                        $plusones = $bug["Plusones"];

                        if ($comments === 0) $comments = "no";
                        if ($plusones === 0) $plusones = "no";

                        return '<section><p class="message"><a href="' . $url . '" title="' . $title . '">' . $title . '</a></p>'
                            . '<p class="stats">Sometime - <a href="' . $url . '#comments">' . $comments . ' comment' . ($comments === 1 ? "" : "s") . '</a> -'
                            . '<a href="' . $url . '#plusones">' . $plusones . ' upvote' . ($plusones === 1 ? "" : "s") . '</a></p></section>';
                    }, $bugs)),
                    "header" => $header
                );

            } else return array("error" => array("message" => "These login credentials appear to be incorrect. Please try again."));
        } else return array("error" => array("message" => "Please enter a valid username/email and password."));
    }
}