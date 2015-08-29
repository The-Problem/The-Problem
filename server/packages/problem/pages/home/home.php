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
        Library::get("image");
        $logo = new Image("branding", "logo", array(
            "format" => "png"
        ));

        $username = $_SESSION["username"];

        $sections = array();

        Library::get("objects");
        $ids = Objects::user_permissions("section.view", $username);
        $amount = count($ids);
        $clause = implode(',', array_fill(0, $amount, '?'));
        $types = str_repeat('i', $amount);

        $viewable = Objects::permission(0, "site.view", $username);

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

    <?php if ($viewable) { ?>
    <div class="left-column">
        <?php if (count($devSections)) { ?>
        <h2>Sections where you're a developer</h2>
        <div class="list-table">
            <?php
            foreach($devSections as $section) {
                Modules::getoutput("sectionTile", $section);
            }
            ?>
        </div>
        <h2>More Sections</h2>
        <?php } else { ?><h2>Sections</h2><?php } } ?>
<?php } else {
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
                <button class="login-btn">LOGIN</button><button class="register-btn">REGISTER</button>
            </div>
        </form>
        <div class="login-spinner spinner"><div class='circle circle-1'></div><div class='circle circle-2'></div><div class='circle circle-3'></div><div class='circle circle-4'></div><div class='circle circle-5'></div><div class='circle circle-6'></div><div class='circle circle-7'></div><div class='circle circle-8'></div></div>
    </div>
</header>

<div class="content">

<div class="columns">

    <?php if ($viewable) { ?>
    <div class="left-column">
        <h2>Sections</h2>
    <?php } ?>
<?php } ?>
    <?php if ($viewable) { ?>
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

    <?php if ($username) {
        Library::get("notifications");

        $bugs = Connection::query("
SELECT *, (SELECT COUNT(*) FROM comments
           WHERE comments.Bug_ID = bugs.Bug_ID) AS Comments,
          (SELECT COUNT(*) FROM plusones
           WHERE plusones.Object_ID = bugs.Object_ID) AS Plusones,
          bugs.Name AS Bug_Name,
          sections.Name AS Section_Name
  FROM bugs
    JOIN sections ON bugs.Section_ID = sections.Section_ID
  WHERE Author = ? OR Assigned = ?
ORDER BY Edit_Date DESC, Creation_Date DESC LIMIT 5", "ss", array($username, $username));

        $notifications = Notifications::get(10 - count($bugs));

        ?>
    <div class="right-column">
        <?php if (strlen($notifications)) { ?>
        <h2>Notifications</h2>
        <div class="notification-list">
            <?php echo $notifications; ?>
        </div>
        <?php } ?>

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
                    <a href="<?php echo $url; ?>#plusones"><?php echo $plusones; ?> upvote<?php echo $plusones === 1 ? "" : "s"; ?></a>
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