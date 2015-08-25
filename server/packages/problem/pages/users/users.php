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

    public function body() {
        Library::get('users');
        $currentUser = Users::getUser($this->user);
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
                        <p><?php echo $currentUser->getSummary(); ?></p>
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
                </div>

                <div id='assignedDiv' class='contentDiv'>
                    <h2>Assigned Bugs (</h2>
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