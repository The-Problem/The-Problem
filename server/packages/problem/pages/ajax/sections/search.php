<?php
class AjaxSectionsSearchPage implements IPage {
    public function __construct(PageInfo &$page) {
    }

    public function template() {
        return Templates::findtemplate("blank");
    }

    public function subpages() {
        return false;
    }
    public function permission() {
        return true;
    }
    public function head(Head &$head) { }

    public function body() {
        Library::get('modules', 'cookies');

        $query = $_GET['query'];
        $username = Cookies::prop('username');

        $sections = array();
        if ($username) {
            $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE Section_ID NOT IN (SELECT Section_ID FROM Developers
                           WHERE Developers.Username = ?)
  AND INSTR(Name, ?) >= 1
ORDER BY Open_Bugs DESC, All_Bugs DESC", "ss", array($username, $query));
        } else {
            $sections = Connection::query("
SELECT *, (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID
           AND bugs.Status = 1) AS Open_Bugs,
          (SELECT COUNT(*) FROM bugs
           WHERE bugs.Section_ID = sections.Section_ID) AS All_Bugs
FROM sections
  WHERE INSTR(Name, ?) >= 1
ORDER BY Open_Bugs DESC, All_Bugs DESC", "s", array($query));
        }

        if (count($sections)) {
            foreach ($sections as $section) {
                Modules::getoutput('sectionTile', $section);
            }
        } else echo '<div class="none">We couldn\'t find anything matching "' . htmlentities($query) . '".</div>';
    }
}