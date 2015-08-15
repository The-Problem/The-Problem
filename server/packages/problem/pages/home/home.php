<?php
class HomePage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        //Library::get("users");

        $template = Templates::findtemplate("default");
        if (/*Users::loggedin()*/true) return $template->add_class("loggedin");
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
        /*Library::get("image");
        $logo = new Image("branding", "logo-text", array(
            "format" => "png"
        ));

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
  JOIN developers ON (developers.Section_ID = sections.Section_ID)
WHERE developers.Username = ?", "s", array($username));

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
        <?php } ?>
    </div>

</div>

</div>




<?php } else { ?>
<header class="big">
    <img src="<?php echo $logo->clientpath; ?>" alt="The Problem" title="The Problem" />
    <h2>Login or register to get started</h2>

    <form method="post" class="login-box">
        <input type="text" name="username" placeholder="Username or email" />
        <input type="password" name="pass" placeholder="Password" />

        <div class="buttons">
            <button class="register-btn">Register</button><button class="login-btn">Login</button>
        </div>
    </form>
</header><?php } ?>

<div class="content">
    <div class="columns">
        <div class="left-column">
            <?php if ($username) { ?>
            <h2>Sections where you're a developer</h2>
                <div class="section-list">
                    <section class="deep-purple">
                        <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                            <div class="container">
                                <h3>General Feedback</h3>
                                <p class="section-stats">
                                    <span class="open">50 open bugs</span>
                                    <span class="all">150 bugs</span>
                                </p>
                            </div>
                        </a>
                    </section><section class="light-green">
                        <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                            <div class="container">
                                <h3>General Feedback</h3>
                                <p class="section-stats">
                                    <span class="open">50 open bugs</span>
                                    <span class="all">150 bugs</span>
                                </p>
                            </div>
                        </a>
                    </section><section class="deep-orange">
                        <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                            <div class="container">
                                <h3>General Feedback</h3>
                                <p class="section-stats">
                                    <span class="open">50 open bugs</span>
                                    <span class="all">150 bugs</span>
                                </p>
                            </div>
                        </a>
                    </section><section class="teal">
                        <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                            <div class="container">
                                <h3>General Feedback</h3>
                                <p class="section-stats">
                                    <span class="open">50 open bugs</span>
                                    <span class="all">150 bugs</span>
                                </p>
                            </div>
                        </a>
                    </section>
                </div>
            <h2>More Sections</h2>
            <?php } else { ?><h2>Sections</h2><?php } ?>
            <input class="search-box" type="search" placeholder="Search all sections" />

            <div class="section-list">
                <section class="deep-purple">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="light-green">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="deep-orange">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="teal">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="pink">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="blue">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="amber">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="indigo">
                    <a href="<?php echo $path; ?>" title="General Feedback" style="<?php echo $style; ?>">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section>
            </div>
        </div><?php if ($user) { ?><div class="right-column">
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

<?php }
}