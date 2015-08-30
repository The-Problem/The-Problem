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
            $bug = Connection::query("SELECT bugs.Object_ID AS Object_ID, Bug_ID, Status FROM bugs
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
        $head->stylesheet("pages/bug");
    }

    public function body() {
        Library::get("objects");

        $statuses = array(
            0 => "DELETED",
            1 => "OPEN",
            2 => "CLOSED",
            3 => "DUPLICATE",
            4 => "WIP"
        );

        function detectStatus($status){
            if ($status === "0"){
                return "grey";
            }else if ($status === "1"){
                return  "green";
            } else if ($status === "2"){
                return  "red";
            } else if ($status === "3"){
                return  "orange";
            } else if ($status === "4"){
                return  "yellow";
            }
        }
    ?>
<div id="centerArea">
    <div id="bugInfo">
        <?php echo "<h1><a id='section' href='" . Path::getclientfolder("bugs", $this->path[2]) . "'>" . htmlentities($this->path[2]) . "</a> > " . htmlentities($this->bug["Bug_Name"]) . "<span id='RID'> #" . $this->path[3] . "</span></h1>";
        echo "<h3> submitted by " . htmlentities($this->bug["Author"]);?> <span class="timeago" title="<?php echo date("c", strtotime($this->bug["Creation_Date"])); ?>"></span>
    </div>
    <div id="bugContent">
        <div id="leftColumn">
            <?php
                Modules::getoutput("comments", $this->bug);
            ?>
        </div>
        <div id="rightColumn">
            <h3>Status</h3>
            <div id="statuses">
            <?php
                echo "<h4><span id='status' class='" . detectStatus(htmlentities($this->bug["Status"])) . "'>" . $statuses[$this->bug["Status"]] . "</span>";

                if (Objects::permission($this->bug["Bug_ObjectID"], "bug.change-status", $_SESSION['username'], $this->bug["Section_ID"])) {
                    if ($this->bug["Status"] !== 1) echo ' <br> <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=1">Open</a>';
                    if ($this->bug["Status"] !== 2) echo ' <br> <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=2">Close</a>';
                    if ($this->bug["Status"] !== 3) echo ' <br> <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=3">Duplicate</a>';
                    if ($this->bug["Status"] !== 4) echo ' <br> <a href="' . Path::getclientfolder("bugs", $this->path[2], $this->path[3]) . '?status=4">WIP</a>';             
                }
                echo "</h4>";
        }
            ?>

            <h3>Assignee</h3>
            <h4>Ran out</h4>
            <h3>Notifications</h3>
            <h4>of time</h4>
                
            </div>
        </div>
    </div>
</div>



<?php    }
}?>