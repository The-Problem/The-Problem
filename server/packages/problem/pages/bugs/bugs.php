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
        Library::get("image");
        $coverImage = new Image("section", "section", array(
            "format" => "jpeg"
        ));
        ?>

<div id="sectionHead">
    <img src="<?php echo $coverImage->clientpath?>" id="coverImage">
    <div id="infoCard">
        <div id="infoCentre">
            <h1 id="sectionTitle">Alpha Beta</h1>
            <div id="developerArea">
                <h2 id="developers">Developers</h2>
            </div>
        </div>
    </div>
</div>

<div id="sectionContent">
    <div id="centerArea">
        <div id="topsection">
            <input id="searchBox" type="search" placeholder="What are you looking for?">
            <button type="button" id="newBug"><i class="fa fa-plus"></i></button>
        </div>
        <table id="bugsTable">
            <tr>
                <td id="toprow">
                <select id="sort">
                        <option value="initial" selected="selected" disabled="disabled">Sort</option>
                        <option value="1">Alphabetical</option>
                        <option value="2">Alpha - Reverse</option>
                        <option value="3">Newest</option>
                        <option value="4">Oldest</option>
                    </select>
                    <select id="status">
                        <option value="initial" selected="selected" disabled="disabled">Status</option>
                        <option value="1">Open</option>
                        <option value="2">Closed</option>
                        <option value="3">WiP</option>
                    </select>
                    <select id="submitted">
                        <option value="initial" selected="selected" disabled="disabled">Submitted</option>
                        <option value="t1">INSERT SUBMITTERS HERE</option>
                    </select>
                    <select id="assignee">
                        <option value="initial" selected="selected" disabled="disabled">Assignee</option>
                        <option value="1">INSERT DEVS HERE</option>
                </select>
                </td>
            </tr>
            <?php
            $bugs = Connection::query("SELECT bugs.Name, bugs.Bug_ID, bugs.Author, bugs.Creation_date, bugs.Status, bugs.RID  from bugs JOIN sections ON (sections.Section_ID = bugs.Section_ID) WHERE sections.Slug = ?","s",array($this->section));

            foreach ($bugs as $bug){
                echo "<tr>
                    <td class='bugEntry'>
                        <div class='leftColumn'>
                            <p class='bugName'>" . htmlentities($bug["Name"]) . "</p>
                            <p class='bugSubmitter'> Submitted";
            ?>

            <span class="timeago" title="<?php echo date("c", strtotime($bug["Creation_Date"])); ?>"></span>

            <?php
                echo " ago by " . htmlentities($bug["Author"]) . "</p>
                        </div>
                         
                        <div class='rightColumn'>
                            <p class='RID'>" . htmlentities($bug["RID"]) . "</p>
                            <i class='fa fa-check fa-black'></i>
                            <i class='fa fa-comment fa-black'></i>
                            <p class='commentNumber'>0</p>
                        </div>
                    </td>
                </tr>";}
            ?>
        </table>
    </div>
</div>

<?php 
        echo "Showing section: $this->section";
		
		?>
<h2>Bug list</h2>
<h3><a href="<?php echo Path::getclientfolder("bugs", $this->section, "new"); ?>">CREATE NEW</a></h3>
<ul>
<?php
$bugs = Connection::query("SELECT RID, bugs.Name FROM bugs
                     JOIN sections ON (sections.Section_ID = bugs.Section_ID)
				   WHERE sections.Slug = ?", "s", array($this->section));

foreach ($bugs as $bug) {
	echo "<li><a href='" . Path::getclientfolder("bugs", $this->section, $bug["RID"]) . "'>" . htmlentities($bug["Name"]) . "</a></li>";
}
?>
</ul>
        <?php
    }
}