<?php
class UsersPage implements IPage {
    private $user = "";

    public function __construct(PageInfo &$page) {
        $this->user = $page->pagelist[1];
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
        $head->stylesheet('pages/users');
        $head->script('pages/users');
    }

    private function recentActivity(){
        $recentActivity = Notifications::getWhereTriggerIs($this->user);

        $tableRows = "";
        for ($i = 0; $i < count($recentActivity); $i++){
            $tableRows .= "<div class='recentRow'><p class='activityLeft'>" . $recentActivity[$i]['sectionName'] . "</p><p class='activityMid'>" . $recentActivity[$i]['message'] . "</p><p class='activityRight'>" . $recentActivity[$i]['fuzzyTime'] . "</p></div>";
        }

        return $tableRows;
    }

    private function assignedBugs($currentUser){
        $bugs = $currentUser->getBugs();
        $output = "";

        for ($i = 0; $i <= count($bugs); $i++){
            $output .= "<a href='" . Path::getclientfolder("bugs", $bugs[$i]['Section_Slug'], $bugs[$i]['RID']) . "'><div class='assignedRow color-" .  $bugs[$i]['Colour'] . "'><p>" . $bugs[$i]['Section_Name'] . "</p><p>" . $bugs[$i]['Bug_Name'] . "</p></div></a>";
        }

        return array($output, count($bugs));


    }

    private function developingSections($currentUser){
        $sections = $currentUser->getSections();
        $sectionTiles = "";

        for ($i = 0; $i < count($sections); $i++){
            $bugText = $sections[$i]['Open_Bugs'];
            if ($bugText == 1){
                $bugText .= " Open Bug";
            }else {
                $bugText .= " Open Bugs";
            }
            $link = Path::getclientfolder('bugs', strtolower($sections[$i]['Name']));
            if ($sections[$i]['Colour'] == 0){
                Library::get('image');
                $sectionImage = new Image('sections', $sections[$i]['Name'], array(
                        "format" => "jpg",
                        "width" => "150",
                        "height" => "150"
                    ));

                $imgLink = "'" . $sectionImage->clientpath . "'";
                $imgCSS = "style=\"background-image: url(" . $imgLink . ");\"";
            }
            $newTile = "<a href='" . $link . "'><div " . $imgCSS . "class='sectionTile color-" .  $sections[$i]['Colour'] . "'><div class='sectionTileText'><h2>" . $sections[$i]['Name'] ."</h2><h5>" . $bugText . "</h5></div></div></a>";
            $sectionTiles .= $newTile;
        }

        return array($sectionTiles, count($sections));

    }

    public function body() {
        Library::get('users');
        $currentUser = Users::getUser($this->user);
        
        if (!$currentUser){
            echo "<h1>User not found. :(</h1>";
            return;
        }

        $coverPhotoLink = $currentUser->getCoverPhoto();
        $recentRows = self::recentActivity($currentUser);
        $assignedRows = self::assignedBugs($currentUser);
        $sectionTiles = self::developingSections($currentUser);

        ?>

        <style>
            #userCover {
                background-image: url('<?php echo $coverPhotoLink ?>');
            }
        </style>

        <div id='userCover' class='parallax'>
            <div class='backgroundStrip'>
                <div id='userInfoWrap'>
                    <img class='profilePic' src="<?php echo $currentUser->getAvatarLink(120); ?>"/>
                    <div class='userInfo'>
                        <h1><?php echo $currentUser->name ?></h1>
                        <p class='infoText'><?php echo $currentUser->getSummary(); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div id='contentBody'>
            
            <div class='bodyWrap'>
                <?php
                    if ($_SESSION['username'] == $currentUser->username){
                        echo "<button id='userDetailsButton' class='highlight'>CHANGE MY DETAILS</button>";
                        Modules::getoutput("userDetails", array(
                            "username" => $currentUser->username
                        ));
                    }
                ?>

                <div id='aboutDiv' class='contentDiv'>
                    <h2>About</h2>
                    <p class='bioText'><?php echo nl2br($currentUser->bio); ?></p>
                </div>

                <div id='activityDiv' class='contentDiv'>
                    <h2>Recent activity</h2>
                    <div id='activityRows'>
                        <?php echo $recentRows; ?>                    
                    </div>
                </div>

                <div id='assignedDiv' class='contentDiv'>
                    <h2>Assigned bugs (<?php echo $assignedRows[1]; ?>)</h2>
                    <div class='assignedRows'>
                        <?php echo $assignedRows[0] ?>
                    </div>
                </div>

                <div id='developingIn'>
                    <h2>Developing in (<?php echo $sectionTiles[1] ?>)</h2>
                    <div id='developingSections'>
                        <div id='sectionTiles'><?php echo $sectionTiles[0]  ?></div>
                    </div>
                </div>
                
            </div>
            
        </div>

        <?php
    }
}