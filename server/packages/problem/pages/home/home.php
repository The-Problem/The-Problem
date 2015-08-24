<?php
class HomePage implements IPage {
    public function __construct(PageInfo &$page) {
    }
    public function template() {
        //$_SESSION["username"] = "mrfishie";

        $template = Templates::findtemplate("default");
        if ($_SESSION["username"]) return $template->add_class("loggedin");
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
        $head->script("pages/home");
    }

    public function body() {
        Library::get("image");
        $logo = new Image("branding", "logo", array(
            "format" => "png"
        ));

        $username = $_SESSION["username"];

        $sections = array();

        Library::get("objects");
        $permissions = Objects::user_permissions("user.view", $_SESSION["username"]);
        $ids = array();
        foreach ($permissions as $id => $permission) {
            if ($permission) array_push($ids, $id);
        }
        $amount = count($ids);
        $clause = implode(',', array_fill(0, $amount, '?'));
        $types = str_repeat('i', $amount);

        if ($username) {
            $params = array_merge(array($username), $ids);

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
  WHERE Section_ID IN (SELECT Section_ID FROM developers
                       WHERE developers.Username = ?)
  AND sections.Object_ID IN ($clause)
ORDER BY Open_Bugs DESC, All_Bugs DESC", "s$types", $params);

            $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE Section_ID NOT IN (SELECT Section_ID FROM developers
                           WHERE developers.Username = ?)
  AND sections.Object_ID IN ($clause)
ORDER BY Open_Bugs DESC, All_Bugs DESC", "s$types", $params);

            ?>
<div class="welcome">
    <h1>Welcome, <a href="<?php echo Path::getclientfolder("~" . htmlentities($username)); ?>"><?php echo htmlentities($user[0]["Name"]); ?></a>.</h1>
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
FROM sections WHERE sections.Object_ID IN ($clause)", "$types", $ids); ?>

<header class="big">
    <h1><img src="<?php echo $logo->clientpath; ?>" alt="The Problem" title="The Problem" /><span><?php echo htmlentities(Pages::$head->title); ?></span></h1>
    <h2>Login or register to get started</h2>

    <form method="post" action="<?php echo Path::getclientfolder('login'); ?>" class="login-box">
        <input type="text" name="username" placeholder="Username or email" />
        <input type="password" name="password" placeholder="Password" />

        <div class="buttons">
            <button class="login-btn">LOGIN</button><button class="register-btn">REGISTER</button>
        </div>
    </form>
</header>

<div class="content">

<div class="columns">

    <div class="left-column">
        <h2>Sections</h2>
<?php } ?>
        <?php if (count($sections)) {
            Library::get("objects");

            ?>
        <input class="search-box" type="search" placeholder="Search all sections" />
        <div class="section-list searchable">
            <?php
            foreach ($sections as $section) {

                Modules::getoutput("sectionTile", $section);
            }
            ?>
            <div class="none" style="display:none">We couldn't find anything matching that query.</div>
        </div>
        <?php } else { ?><div class="none">Nothing here just yet...</div> <?php } ?>
    </div>

    <?php if ($username) {
        // todo: get notifications

        $bugs = Connection::query("
SELECT *, (SELECT COUNT(*) FROM comments
           WHERE comments.Bug_ID = bugs.Bug_ID) AS Comments,
          (SELECT COUNT(*) FROM plusones
           WHERE plusones.Object_ID = bugs.Object_ID) AS Plusones,
          bugs.Name AS Bug_Name,
          sections.Name AS Section_Name
  FROM bugs
    JOIN sections ON bugs.Section_ID = sections.Section_ID
  WHERE Author = ?", "s", array($username));
        ?>
    <div class="right-column">
        <h2>Notifications</h2>
        <div class="notification-list">
            <section>
                <p class="message">
                    <a href="<?php echo Path::getclientfolder('~cmnvb'); ?>">Patrick</a> replied to
                    <a href="<?php echo Path::getclientfolder('general-feedback', 20); ?>#5">your comment</a>:
                    "I'll leave my matter till later..."
                </p>
                <p class="stats">Just then - <a href="<?php echo Path::getclientfolder('sections', 'general-feedback'); ?>">General Feedback</a></p>
            </section>
            <section>
                <p class="message">
                    <a href="<?php echo Path::getclientfolder('~Dr2n'); ?>">Darren</a> +1'd
                    <a href="<?php echo Path::getclientfolder('general-feedback', 20); ?>">your bug</a>,
                    "Needs More Dragons"
                </p>
                <p class="stats">Just then - <a href="<?php echo Path::getclientfolder('sections', 'general-feedback'); ?>">General Feedback</a></p>
            </section>
            <section>
                <p class="message">
                    You've been assigned to
                    <a href="<?php echo Path::getclientfolder('sections', 'general-feedback', 50); ?>">#50</a>,
                    "Needs More Unicorns"
                </p>
                <p class="stats">3 hours ago - <a href="<?php echo Path::getclientfolder('sections', 'general-feedback'); ?>">General Feedback</a></p>
            </section>
        </div>

        <?php if (count($bugs)) { ?>
        <h2>My Bugs</h2>
        <div class="notification-list">
            <?php
            foreach ($bugs as $bug) {
                $url = Path::getclientfolder($bug["Slug"], $bug["RID"]);
                $title = htmlentities($bug["Bug_Name"]);

                $comments = $bug["Comments"];
                $plusones = $bug["Plusones"];

                if ($comments === 0) $comments = "no";
                if ($plusones === 0) $plusones = "no";


                ?>
            <section>
                <p class="message">
                    <a href="<?php echo $url; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
                </p>
                <p class="stats">Sometime -
                    <a href="<?php echo $url; ?>#comments"><?php echo $comments; ?> comment<?php echo $comments === 1 ? "" : "s"; ?></a> -
                    <a href="<?php echo $url; ?>#plusones"><?php echo $plusones; ?> +1<?php echo $plusones === 1 ? "" : "s"; ?></a>
                </p>
            </section>
                <?php
            }
            ?>
        </div>
        <?php } ?>
    <?php } ?>

</div>

</div>
<?php } }