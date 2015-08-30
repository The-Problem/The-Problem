<?php
class LoggedInHomeModule implements IModule {
    public function spinnersize() { return Modules::SPINNER_LARGE; }

    public function getcode($params = array(), Head $h) {
        $username = $_SESSION["username"];

        Library::get("objects");
        $ids = Objects::user_permissions("section.view", $username);
        if (!count($ids)) $ids = array(0);

        // build a part of the query based on the "section.view" permission, so we only select ones that
        // we can actually view
        $amount = count($ids);
        $clause = implode(',', array_fill(0, $amount, '?'));
        $types = str_repeat('i', $amount);

        $viewable = Objects::permission(0, "site.view", $username);

        $params = array_merge(array($username), $ids);

        $user = Connection::query("
SELECT * FROM users
  WHERE Username = ?", "s", array($username));

        // we show the sections that the user is developing in separately, so we need a separate query
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

        // get the other sections that the user doesn't develop
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
        <?php if ($viewable) { ?>

        <?php if (count($devSections)) { ?>
        <h2>Sections where you're a developer</h2>
        <div class="list-table">
            <?php
            // output developer sections
            foreach($devSections as $section) {
                Modules::getoutput("sectionTile", $section);
            }
            ?>
        </div>
        <h2>More Sections</h2>
        <?php } else { ?><h2>Sections</h2><?php } ?>

        <?php if (count($sections)) { ?>
        <input class="search-box" type="search" placeholder="Search all sections" />
        <div class="list-table searchable">
            <?php
            // output remaining sections
            foreach ($sections as $section) {
                Modules::getoutput("sectionTile", $section);
            }

            // this is shown if there are no search results
            ?>
            <div class="none" style="display:none">We couldn't find anything matching that query.</div>
        </div>
        <?php } else { ?><div class="none">Nothing here just yet...</div><?php } } ?>
    </div>
    <div class="right-column">
        <?php
        Library::get("notifications");

        // get all bugs and some statistics
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

        // the bugs are limited to 5, and notifications are 10 - amount of bugs
        // so we will always end up with at least 5 notifications if there are
        // any
        $notifications = Notifications::get(array(
            "limit" => 10 - count($bugs)
        ));
        ?>
        <h2>Notifications</h2>
        <div class="notification-list">
            <?php if (count($notifications)) {
                foreach ($notifications as $notification) {
                    ?>
                    <section>
                        <p class="message"><?php echo $notification["message"]; ?></p>
                        <p class="stats">
                            <span class="timeago" title="<?php echo $notification["time"]; ?>"></span>
                            - <a href="<?php echo Path::getclientfolder("bugs", $notification["sectionSlug"]); ?>"><?php echo htmlentities($notification["sectionName"]); ?></a>
                        </p>
                    </section>
                    <?php
                }
            } else echo "<p class='none'>No notifications yet</p>"; ?>
        </div>
        <?php if (count($bugs)) { ?>
        <h2>My Bugs</h2>
        <div class="notification-list">
            <?php
                foreach ($bugs as $bug) {
                    $url = Path::getclientfolder("bugs", $bug["Slug"], $bug["RID"]);
                    $title = htmlentities($bug["Bug_Name"]);

                    $comments = $bug["Comments"];
                    $plusones = $bug["Plusones"];

                    // get the latest activity time, either from the latest comment
                    // or the bug itself if there are no comments
                    $time = Connection::query("
SELECT  COALESCE(
                  (SELECT MAX(Creation_Date) AS Creation_Date
                    FROM comments
                  WHERE comments.Bug_ID = bugs.Bug_ID),
                  bugs.Creation_Date) AS Activity_Time
FROM bugs
  WHERE bugs.Bug_ID = ?", "i", array($bug["Bug_ID"]));

                    ?>
                    <section>
                        <p class="message">
                            <a href="<?php echo $url; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
                        </p>

                        <p class="stats"><span class="timeago" title="<?php echo date('c', strtotime($time[0]["Activity_Time"])); ?>"></span> -
                            <?php echo $comments; ?><i class="fa fa-comments"></i> -
                            <?php echo $plusones; ?><i class="fa fa-thumbs-up"></i>
                        </p>
                    </section>
                    <?php
                }
            ?>
        </div>
        <?php } ?>
    </div>
</div>

</div>
<?php
    }

    public function getsurround($code, $params) {
        return $code;
    }
}