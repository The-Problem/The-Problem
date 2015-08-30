<?php
class AdminSectionModule implements IModule {
    public function __construct() {

    }

    public function spinnersize() { return Modules::SPINNER_SMALL; }

    public function getcode($params = array(), Head $h) {
        /**
         * Parameters:
         *
         * "id" => The ID of the section
         */

        // get information on the section we are displaying
        $sections = Connection::query("SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status != 1) AS Closed_Bugs FROM sections WHERE Section_ID = ?", "i", array($params["id"]));
        $section = $sections[0];
        $name = htmlentities($section["Name"]);

        // fetch all developers to display in a table
        $developers = Connection::query("SELECT users.Username AS Username, Email FROM developers
                                           JOIN users ON (developers.Username = users.Username)
                                         WHERE developers.Section_ID = ?", "i", array($params["id"]));

        // create the section tile
        $style = "";
        if ($section["Color"] === 0) {
            Library::get("image");

            $img = new Image("sections", $section["Slug"], array(
                "format" => "jpg",
                "crop" => true,
                "width" => 150,
                "height" => 150
            ));
            $style = "background-image:url('" . htmlentities($img->clientpath) . "')";
        }

        // calculate percentages for the bug stats
        $total = $section["Closed_Bugs"] + $section["Open_Bugs"];
        if ($total > 0) $percentage = $section["Closed_Bugs"] / $total;
        else $percentage = 1.1;

        $developer_count = count($developers);
        if ($developer_count === 0) $dev_str = "are no developers";
        else if ($developer_count === 1) $dev_str = "is 1 developer";
        else $dev_str = "are $developer_count developers";

        // output everything
        ?>
<div class="section-header">
    <a class="close" href="#"><i class="fa fa-minus-square"></i></a>
    <div class="section-tile color-<?php echo $section["Color"]; ?>" style="<?php echo $style; ?>"></div><div class="right-column">
        <div class="section info">
            <h2><a href="<?php echo Path::getclientfolder("bugs", $section["Slug"]); ?>"><?php echo $name; ?></a></h2>

            <table>
                <tr><th>Description:</th><td><?php echo htmlentities($section["Description"]); ?></td></tr>
                <tr><th>Bugs:</th><td><?php if ($total > 0) {
                            echo $total; ?> total, <?php
                            echo $section["Open_Bugs"]; ?> open, <?php
                            echo $section["Closed_Bugs"]; ?> closed (<?php
                            echo floor($percentage * 1000) / 10; ?>%)<?php
                        } else { ?>No bugs<?php } ?></td></tr>
            </table>
        </div>
        <div class="section developer-list">
            <h3>Developers</h3>
            <p class="total">There <?php echo $dev_str; ?>.</p>

            <table>
                <?php
                foreach ($developers as $dev) {
                    $gravatar_id = md5(strtolower(trim($dev["Email"])));
                    $gravatar = "http://www.gravatar.com/avatar/$gravatar_id?d=identicon&s=30";

                    ?><tr data-username="<?php echo htmlentities($dev["Username"]); ?>">
                    <td class="user-image" style='background-image:url("<?php echo $gravatar; ?>");'></td>
                    <td class="user-name"><?php echo htmlentities($dev["Username"]); ?></td>
                    <td class="user-remove"><a href="javascript:void(0)" title="Remove developer"><i class="fa fa-times"></i></a></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <input type="text" placeholder="Add a developer..." />
        </div>
    </div>
</div>
<?php
    }

    public function getsurround($code, $params) {
        return $code;
    }
}