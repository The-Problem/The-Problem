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
    }

    private function recentActivity(){
        $tableRows = "";

        Library::get('notifications');
        $recentActivity = Notifications::getWhereTriggerIs($this->user);

        for ($i = 0; $i < count($recentActivity); $i++){
            $tableRows .= "<tr><td>" . $recentActivity[$i]['sectionName'] . "</td><td>" . $recentActivity[$i]['message'] . "</td></tr>";
        }

        return $tableRows;
    }

    private function assignedBugs(){
        $query = 
                "SELECT bugs.Name as 'Bug_Name', sections.Name as 'Section_Name', sections.Color as 'Colour'
                FROM bugs
                    LEFT JOIN sections ON bugs.Section_ID = sections.Section_ID
                    LEFT JOIN plusones ON bugs.Object_ID = plusones.Object_ID
                WHERE bugs.Assigned = ?";


        $queryResult = Connection::query($query, "s", array($this->user));
        echo var_dump($queryResult);

        $output = "";

        for ($i = 0; $i <= count($query); $i++){
            $output .= "<tr class='color-" .  $queryResult[$i]['Colour'] . "'><td>" . $queryResult[$i]['Section_Name'] . "</td><td>" . $queryResult[$i]['Bug_Name'] . "</td></tr>";
        }

        return $output;


    }

    public function body() {
        Library::get('users');
        $currentUser = Users::getUser($this->user);
        
        if (!$currentUser){
            echo "<h1>User not found. :(</h1>";
            return;
        }

        $coverPhotoLink = $currentUser->getCoverPhoto();


        ?>

        <style>
            #userCover {
                background-image: url('<?php echo $coverPhotoLink ?>');
            }
        </style>

        <div id='userCover'>
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
                <div id='aboutDiv' class='contentDiv'>
                    <h2>About</h2>
                    <p><?php echo $currentUser->bio; ?></p>
                </div>

                <div id='activityDiv' class='contentDiv'>
                    <h2>Recent Activity</h2>
                    <table class='activityTable'>
                        <?php echo self::recentActivity();?>                    
                    </table>
                </div>

                <div id='assignedDiv' class='contentDiv'>
                    <h2>Assigned Bugs (</h2>
                    <table class='assignedTable'>
                        <?php echo self::assignedBugs(); ?>
                    </table>
                </div>

                <div id='developingIn'>
                    <h2>Developing in (</h2>
                    <div id='developingSections'>
                        <div id='sectionTile'></div>
                    </div>
                </div>
                
            </div>
            
        </div>

        <?php
    }
}