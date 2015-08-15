<?php
class RegisterStep2Page implements IPage {
    public function __construct(PageInfo &$info) {
    }
    
    public function template() {
        return Templates::findtemplate("default");
    }
    public function permission() {        
        Library::get("users");
        //return Users::loggedin() && Cookies::exists("go");
        return true;
    }
    
    public function subpages() { return true; }
    
    public function head(Head &$head) { }
    
    public function body() { ?>
<div class="section">
    <h1 class="title">Select your networks</h1>
    <p>Now it's time to select the networks you would like to add to your profile. Select the networks you would like to use below. You can add new networks at any time.</p>
</div>
<?php    
Library::get("modules");
Modules::getoutput("NetworkSelector");
?>
<div class="button-set light-bg">
    <a href="<?php echo Path::addget(Path::getclientfolder("feed"), "notice", "Welcome to mozzo! This is your feed page, where you can view all of your incoming items."); ?>" title="Continue" class="button-right highlight">Done</a>
    <div class="clear"></div>
</div>
<?php  } }