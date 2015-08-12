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
        Library::get("image");
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
        $user = true;

        if ($user) { ?>
<div class="welcome">
    <h1>Welcome, <a href="<?php echo Path::getclientfolder("users", "mrfishie"); ?>">Tom</a>.</h1>
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
            <?php if ($user) { ?>
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