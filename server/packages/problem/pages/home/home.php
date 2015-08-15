<?php
class HomePage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        Library::get("cookies");

        Cookies::prop("username", "mrfishie");

        $template = Templates::findtemplate("default");
        if (Cookies::prop("username")) return $template->add_class("loggedin");
        return $template->no_header();
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
    }

    public function body() {
        Library::get("image");
        $logo = new Image("branding", "logo-text", array(
            "format" => "png"
        ));

        Library::get("cookies");
        $username = Cookies::prop("username");

        $sections = array();

        if ($username) {
            $user = Connection::query("
SELECT * FROM users
  WHERE Username = ?", "s", array($username));

            $devSections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE Section_ID IN (SELECT Section_ID FROM Developers
                       WHERE Developers.Username = ?)
ORDER BY Open_Bugs DESC", "s", array($username));

            $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE Section_ID NOT IN (SELECT Section_ID FROM Developers
                           WHERE Developers.Username = ?)
ORDER BY Open_Bugs DESC", "s", array($username));

            ?>
<div class="welcome">
    <h1>Welcome, <a href="<?php echo Path::getclientfolder("users", htmlentities($username)); ?>"><?php echo htmlentities($user[0]["Name"]); ?></a>.</h1>
</div>

<div class="content">

<div class="columns">

    <div class="left-column">
        <?php if (count($devSections)) { ?>
        <h2>Sections where you're a developer</h2>
        <div class="section-list">
            <?php
            foreach($devSections as $section) {
                Modules::getoutput("sectionTile", $section);
            }
            ?>
        </div>
        <h2>More Sections</h2>
        <?php } else { ?><h2>Sections</h2><?php } ?>
<?php } else {
            $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections"); ?>

<header class="big">
    <img src="<?php echo $logo->clientpath; ?>" alt="The Problem" title="The Problem" />
    <h2>Login or register to get started</h2>

    <form method="post" class="login-box">
        <input type="text" name="username" placeholder="Username or email" />
        <input type="password" name="pass" placeholder="Password" />

        <div class="buttons">
            <button class="register-btn">Register</button>
            <button class="login-btn">Login</button>
        </div>
    </form>
</header>

<div class="content">

<div class="columns">

    <div class="left-column">
        <h2>Sections</h2>
<?php } ?>
        <?php if (count($sections)) { ?>
        <input class="search-box" type="search" placeholder="Search all sections" />
        <div class=section-list>
            <?php
            foreach ($sections as $section) {
                Modules::getoutput("sectionTile", $section);
            }
            ?>
        </div>
        <?php } else { ?><div class="none">Nothing here just yet...</div> <?php } ?>
    </div>

    <?php if ($username) { ?>
    <div class="right-column">
        <h2>Notifications</h2>
        <div class="notification-list">
            <section>
                <p class="message">
                    <a href="<?php echo Path::getclientfolder('sections', 'general-feedback', 20); ?>">
                        Patrick replied to your comment: "I'll leave my matter till later..."
                    </a>
                </p>
                <p class="stats">Just then - <a href="<?php echo Path::getclientfolder('sections', 'general-feedback'); ?>">General Feedback</a></p>
            </section>
            <section>
                <p class="message">
                    <a href="<?php echo Path::getclientfolder('sections', 'general-feedback', 18); ?>">
                        Darren +1'd your bug, "Needs More Dragons"
                    </a>
                </p>
                <p class="stats">Just then - <a href="<?php echo Path::getclientfolder('sections', 'general-feedback'); ?>">General Feedback</a></p>
            </section>
            <section>
                <p class="message">
                    <a href="<?php echo Path::getclientfolder('sections', 'general-feedback', 50); ?>">
                        You've been assigned to #50, "Needs More Unicorns"
                    </a>
                </p>
                <p class="stats">3 hours ago - <a href="<?php echo Path::getclientfolder('sections', 'general-feedback'); ?>">General Feedback</a></p>
            </section>
        </div>
    </div>
    <?php } ?>

</div>

</div>
<?php } }