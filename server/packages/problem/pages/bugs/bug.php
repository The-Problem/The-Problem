<?php
class BugsBugPage implements IPage {
    private $bug = false;
    private $path;

    public function __construct(PageInfo &$page) {
        $path = $page->pagelist;
        $this->path = $path;

        // if there is an instruction to change the bug status
        if (array_key_exists('status', $_GET)) {
            // get the bug information
            $bug = Connection::query("SELECT bugs.Object_ID AS Object_ID, Bug_ID, Status, Author FROM bugs
                                        JOIN sections ON (sections.Section_ID = bugs.Section_ID)
                                      WHERE sections.Slug = ?
                                        AND bugs.RID = ?", "si", array($path[2], $path[3]));

            // only update if it needs to be updated
            if ($bug[0]["Status"] != $_GET["status"]) {
                // if the bug was deleted, un-delete it
                if ($bug[0]["Status"] === 0) {
                    $default_view = Connection::query("SELECT Value FROM configuration
                                             WHERE Type = 'permissions-default-bugs'
                                             AND Name = 'view'");
                    Objects::allow_rank($bug[0]["Object_ID"], "bug.view", $default_view[0]["Value"]);
                    Objects::allow_user($bug[0]["Object_ID"], "bug.view", $bug[0]["Author"]);
                }

                // update the bug
                Connection::query("UPDATE bugs
                           SET bugs.Status = ?
                             WHERE bugs.Bug_ID = ?", "ii", array($_GET['status'], $bug[0]["Bug_ID"]));

                // inform any watchers that the bug has been changed
                Connection::query("INSERT INTO notifications
                             (Triggered_By, Received_By,                    Target_One, Target_Two, Creation_Date, Type)
                      VALUES (           ?, (SELECT watchers.Username
                                               FROM watchers
                                             WHERE watchers.Object_ID = ?),          ?,       NULL,             ?,    2)",
                    "siis", array($_SESSION["username"], $bug[0]["Object_ID"], $bug[0]["Object_ID"], date('Y-m-d H:i:s')));
            }
        }

        // get information on the bug
        $res = Connection::query("
SELECT *, bugs.Description AS Bug_Description, bugs.Raw_Description AS Bug_Raw_Description, bugs.Object_ID AS Bug_ObjectID, bugs.Name AS Bug_Name FROM bugs
    JOIN sections ON (sections.Section_ID = bugs.Section_ID)
    WHERE sections.Slug = ?
    AND bugs.RID = ?", "si", array($path[2], $path[3]));
        $this->bug = $res[0];

        // if the bug doesn't exist, go to the error page
        if (!$this->bug) Pages::showpagefrompath(array("error"));
    }

    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        // make sure we have the permission to view the bug
        Library::get("objects");

        $viewable = Objects::permission($this->bug["Bug_ObjectID"], "bug.view", $_SESSION['username'], $this->bug["Section_ID"]);

        if (!$viewable) Path::redirect(Path::getclientfolder("bugs", $this->path[2]));
        return true;
    }
    public function subpages() {
        return false;
    }
    public function head(Head &$head) {

    }

    public function body() {
        $statuses = array(
            0 => "DELETED",
            1 => "OPEN",
            2 => "CLOSED",
            3 => "DUPLICATE",
            4 => "WIP"
        );

        // display the bug name and status
        echo "<h2 style='width:625px;margin:0 auto;padding:30px 0 10px 75px'>" . htmlentities($this->bug["Bug_Name"]) . "</h2>";

        Library::get("objects");
        echo "<h4 style='width:625px;margin:0 auto;padding:0 0 20px 75px'>" . $statuses[$this->bug["Status"]];

        // if we have permission to change the status, show status change links
        if (Objects::permission($this->bug["Bug_ObjectID"], "bug.change-status", $_SESSION['username'], $this->bug["Section_ID"])) {
            if ($this->bug["Status"] !== 1) echo ' - <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=1">Open</a>';
            if ($this->bug["Status"] !== 2) echo ' - <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=2">Close</a>';
            if ($this->bug["Status"] !== 3) echo ' - <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=3">Duplicate</a>';
            if ($this->bug["Status"] !== 4) echo ' - <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=4">WIP</a>';
        }
        echo "</h4>";

        // display the comment list
        Modules::getoutput("comments", $this->bug);
    }
}