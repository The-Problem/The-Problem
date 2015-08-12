<?php
class HomePage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        return Templates::findtemplate("default")->no_header();
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

        ?>
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
</header>

<div class="content">
    <div class="columns">
        <div class="left-column">
            <h2>Sections</h2>
            <input class="search-box" type="search" placeholder="Search all sections" />

            <div class="section-list">
                <section class="deep-purple">
                    <a href="#" title="General Feedback">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="light-green">
                    <a href="#" title="General Feedback">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="deep-orange">
                    <a href="#" title="General Feedback">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="teal">
                    <a href="#" title="General Feedback">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="pink">
                    <a href="#" title="General Feedback">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="blue">
                    <a href="#" title="General Feedback">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="amber">
                    <a href="#" title="General Feedback">
                        <div class="container">
                            <h3>General Feedback</h3>
                            <p class="section-stats">
                                <span class="open">50 open bugs</span>
                                <span class="all">150 bugs</span>
                            </p>
                        </div>
                    </a>
                </section><section class="indigo">
                    <a href="#" title="General Feedback">
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
        </div>
    </div>
</div>

<?php }
}