<?php
class BugsPage implements IPage {
    private $section;
    private $path;

    public function __construct(PageInfo &$page) {
        $this->path = $page->pagelist;
    }
    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
        $head->stylesheet("pages/bugs");
        $count = count($this->path);

        if ($count === 2) {
            $this->section = $this->path[1];
        } else if ($count > 2) {
            if ($this->path[2] === "new") Pages::showpagefrompath(array("bugs", "new", $this->path[1]), false);
            else Pages::showpagefrompath(array("bugs", "bug", $this->path[1], $this->path[2]), false);
        }

        $head->title .= " - " . $this->section;
    }

    public function body() {
        Library::get("objects");
        $permissions = Objects::user_permissions("bug.view", $_SESSION['username'], $this->section);

        Library::get("image");
        $coverImage = new Image("section", "section", array(
            "format" => "jpeg"
        ));

        function detectStatus($status){
            $returnalt = "";
            $returnval = "";

            if ($status === "0"){
                $returnalt = "deleted";
                $returnval = "fa-check fa-grey";
            }else if ($status === "1"){
                $returnalt = "open";
                $returnval =  "fa-check";
            } else if ($status === "2"){
                $returnalt = "closed";
                $returnval =  "fa-times";
            } else if ($status === "3"){
                $returnalt = "a duplicate";
                $returnval =  "fa-times fa-orange";
            } else if ($status === "4"){
                $returnalt = "a WiP";
                $returnval =  "fa-pencil";
            }

            return "<i title='This bug is " . $returnalt . "!' class='fa " . $returnval . "'></i>" ;
        }


        $bugs = Connection::query("SELECT bugs.Name, bugs.Bug_ID, bugs.Creation_Date, bugs.Author, bugs.Status, bugs.RID, bugs.Object_ID, (SELECT COUNT(*) FROM comments WHERE comments.Bug_ID = bugs.Bug_ID) as com  from bugs JOIN sections ON (sections.Section_ID = bugs.Section_ID) WHERE sections.Slug = ?","s",array($this->section));
        $devs = Connection::query("SELECT DISTINCT developers.Username from developers JOIN sections ON (sections.Section_ID = developers.Section_ID) WHERE sections.Slug = ?","s",array($this->section));
        $authors = Connection::query("SELECT DISTINCT bugs.Author from bugs JOIN sections ON (sections.Section_ID = bugs.Section_ID) WHERE sections.Slug = ?","s",array($this->section));
        
        $current_user = Connection::query("SELECT Rank FROM users WHERE Username = ?", "s", array($_SESSION['username']));
        $section = Connection::query("SELECT Object_ID, Name FROM sections WHERE Slug = ?", "s", array($this->section));
        ?>

<div id="sectionHead">
    <img src="<?php echo $coverImage->clientpath?>" id="coverImage">
    <div id="infoCard">
        <div id="infoCentre">
            <h1 id="sectionTitle"><?php echo htmlentities($section[0]["Name"]); ?></h1>
            <div id="developerArea">
                <h2 id="developers">Developers</h2>
                <div id="devImages">
                <?php
                Library::get('users');
                foreach ($devs as $dev){
                $selectedDev = Users::getUser(htmlentities($dev["Username"]));
                echo "<a href='" . Path::getclientfolder("~" . htmlentities($dev["Username"])) . "'>" . "<img class='profilePicture' title='" . htmlentities($dev["Username"]) . "' src='" . $selectedDev->getAvatarLink(40) . "'/></a>";
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="sectionContent">
    <div id="centerArea">
        <div id="topsection">
            <input id="searchBox" type="search" placeholder="What are you looking for?">
            <form action="<?php echo Path::getclientfolder("bugs", $this->section, "new"); ?>"><button type="submit" id="newBug"><i class="fa fa-plus"></i></button></form>
        </div>
        <table id="bugsTable">
            <tr>
                <td id="toprow">
                <select id="sort">
                        <option value="initial" selected="selected" disabled="disabled">Sort</option>
                        <option value="0">None</option>
                        <option value="1">Alphabetical</option>
                        <option value="2">Alpha - Reverse</option>
                        <option value="3">Newest</option>
                        <option value="4">Oldest</option>
                    </select>
                    <select id="status">
                        <option value="initial" selected="selected" disabled="disabled">Status</option>
                        <option value="-1">None</option>
                        <option value="0">Deleted</option>
                        <option value="1">Open</option>
                        <option value="2">Closed</option>
                        <option value="3">Duplicate</option>
                        <option value="4">WiP</option>
                    </select>
                    <select id="submitted">
                        <option value="initial" selected="selected" disabled="disabled">Submitted</option>
                        <option value="0">None</option>
                        <?php
                        foreach ($authors as $author){
                            echo "<option value='" . htmlentities($author["Author"]) . "'>" . htmlentities($author["Author"]) . "</option>";
                        }
                        ?>
                    </select>
                    <select id="assignee">
                        <option value="initial" selected="selected" disabled="disabled">Assignee</option>
                        <option value="0">None</option>
                        <?php
                        foreach ($devs as $dev){
                            echo "<option value='" . htmlentities($dev["Username"]) . "'>" . htmlentities($dev["Username"]) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if ($current_user[0]["Rank"] >= 4) { ?><a id="permissionsLink" href="<?php echo Path::getclientfolder("admin", "object", $section[0]["Object_ID"]); ?>">Permissions</a><?php } ?>-->
                    <h6>Hint: Try hovering over the coloured symbols</h6>
                </td>
            </tr>
            <?php
            foreach ($bugs as $bug){
                if (!in_array($bug["Object_ID"], $permissions)) continue;
                echo "<tr>
                    <td class='bugEntry'>
                        <div class='leftColumn'>
                            <a href='" . Path::getclientfolder("bugs", $this->section, $bug["RID"]) . "'<p class='bugName'>" . htmlentities($bug["Name"]) . "</p></a>
                            <p class='bugSubmitter'> Submitted";
            ?>

            <span class="timeago" title="<?php echo date("c", strtotime($bug["Creation_Date"])); ?>"></span>

            <?php
                echo"by " . "<a href='" . Path::getclientfolder("~" . htmlentities($bug["Author"])) . "'>" . htmlentities($bug["Author"]) . "</a></p>
                        </div>
                        <div class='rightColumn'>
                            <p class='RID'>#" . htmlentities($bug["RID"]) . "</p>"
                            . detectStatus(htmlentities($bug["Status"])) . 
                            "<i class='fa fa-comments fa-black'></i>
                            <p class='commentNumber'>" . htmlentities($bug["com"]) . "</p>
                        </div>
                    </td>
                </tr>";}
            ?>
            <tr style="display: none">
                <td>
                    <div class="leftColumn">
                        <p class="bugName">Nothing here... but us chickens</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
        <?php
    }
}
