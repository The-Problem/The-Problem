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

        /*
        $background = new Image("default", "sections", array(
            "format" => "jpg",
            "tint" => "0-0-0x0.6",
            "crop" => true,
            "width" => 150,
            "height" => 150
        ));
        $style = "background-image:url('" . $background->clientpath . "')";

        $path = Path::getclientfolder("sections", "general-feedback");

        //$user = Users::loggedin();
        $user = true;*/

        function showSection($section) {
            $img = new Image("sections", $section["Section_ID"], array(
                "format" => "jpg",
                "tint" => "0-0-0x0.6",
                "crop" => true,
                "width" => 150,
                "height" => 150
            ));
            $path = Path::getclientfolder("sections", htmlentities($section["Slug"]));
            $name = htmlentities($section["Name"]);

            $open = $section["Open_Bugs"];
            $all = $section["All_Bugs"];

            ?>
            <section>
                <a href="<?php echo $path; ?>"
                   title="<?php echo $name; ?>"
                   style="background-image:url('<?php echo htmlentities($img->clientpath); ?>')">
                    <div class="container">
                        <h3><?php echo $name; ?></h3>
                        <p class="section-stats">
                            <span class="open"><?php echo $open; ?> open bug<?php echo $open === 1 ? "" : "s"; ?></span>
                            <span class="all"><?php echo $all; ?> bug<?php echo $open === 1 ? "" : "s"; ?></span>
                        </p>
                    </div>
                </a>
            </section>
            <?php
        }

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
                       WHERE Developers.Username = ?)", "s", array($username));

            $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE Section_ID NOT IN (SELECT Section_ID FROM Developers
                           WHERE Developers.Username = ?)", "s", array($username));
            ?>
<div class="welcome">
    <h1>Welcome, <a href="<?php echo Path::getclientfolder("users", htmlentities($username)); ?>"><?php echo htmlentities($user[0]["Username"]); ?></a>.</h1>
</div>

<div class="content">

<div class="columns">

    <div class="left-column">
        <?php if (count($devSections)) { ?>
        <h2>Sections where you're a developer</h2>
        <div class="section-list">
            <?php
            foreach($devSections as $section) {
                showSection($section);
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

        <div class=section-list>
            <?php
            if (count($sections)) {
                foreach ($sections as $section) {
                    showSection($section);
                }
            } else { ?><div class="none">Nothing here just yet...</div> <?php }
            ?>
        </div>
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