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
        $head()
    }

    public function body() {
        echo "Viewing profile for $this->user";
        Library::get('users');
        $currentUser = Users.getUser('current');
        ?>

        <section id='userCover'>
            <div id='userInfoWrap'>
                <img class='profilePic' src='<?php $currentUser->getAvatarLink();?>'/>
                <div class='userInfo'>
                    <h1><?php $currentUser->name ?></h1>

                </div>
            </div>

        </section>

        <?php
    }
}