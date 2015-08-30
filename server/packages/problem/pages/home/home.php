<?php
class HomePage implements IPage {

    public function __construct(PageInfo &$page) {
    }
    public function template() {
        $template = Templates::findtemplate("default");
        if ($_SESSION["username"]) return $template->add_class("loggedin");
        return $template->add_class('hide-header');
    }
    public function permission() {
        return true;
    }
    public function subpages() {
        return false;
    }

    public function head(Head &$head) {
        $head->stylesheet("modules/login");
        $head->stylesheet("pages/home");
        $head->script("pages/home");
    }

    public function body() {
        // get the logo image
        Library::get("image");
        $logo = new Image("branding", "logo", array(
            "format" => "png"
        ));

        // if the user is logged in, use the "loggedInHome" module instead
        if ($_SESSION["username"]) Modules::getoutput("loggedInHome");
        else {

        // get all sections the user can view and generate some SQL for that
        Library::get("objects");
        $ids = Objects::user_permissions("section.view", NULL);
        $amount = count($ids);
        $clause = implode(',', array_fill(0, $amount, '?'));
        $types = str_repeat('i', $amount);

        // find if the user can view the site, if not, they get a full-page login screen
        // otherwise they can see the section list
        $viewable = Objects::permission(0, "site.view", NULL);

        $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
       WHERE bugs.Section_ID = sections.Section_ID
       AND bugs.Status = 1) AS Open_Bugs,
      (SELECT COUNT(*) FROM bugs
       WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections WHERE sections.Object_ID IN ($clause)", "$types", $ids); ?>

<header class="big<?php if (!$viewable) echo ' entire-page'; ?>">
    <div class="scroll-container">
        <h1><img src="<?php echo $logo->clientpath; ?>" alt="The Problem" title="The Problem" /><span><?php echo htmlentities(Pages::$head->title); ?></span></h1>
        <h2>Login or register to get started</h2>

        <form method="post" action="<?php echo Path::getclientfolder('login'); ?>" class="login-box">
            <p class="login-error"></p>
            <input type="text" name="username" placeholder="Username or email" />
            <input type="password" name="password" placeholder="Password" />

            <div class="buttons">
                <button class="login-btn">LOGIN</button><button class="register-btn" type="button">REGISTER</button>
            </div>
            <div class="login-spinner spinner-small"><div class='circle circle-1'></div><div class='circle circle-2'></div><div class='circle circle-3'></div><div class='circle circle-4'></div><div class='circle circle-5'></div><div class='circle circle-6'></div><div class='circle circle-7'></div><div class='circle circle-8'></div></div>
        </form>
    </div>
</header>

<div class="content">

<div class="columns">

    <?php if ($viewable) { ?>
    <div class="left-column">
        <h2>Sections</h2>
        <?php if (count($sections)) {
            Library::get("objects");

            ?>
        <input class="search-box" type="search" placeholder="Search all sections" />
        <div class="list-table searchable">
            <?php
            foreach ($sections as $section) {
                Modules::getoutput("sectionTile", $section);
            }
            ?>
            <div class="none" style="display:none">We couldn't find anything matching that query.</div>
        </div>
        <?php } else { ?><div class="none">Nothing here just yet...</div> <?php } ?>
    </div>
    <?php } ?>

</div>

</div>
<?php } } }