<!DOCTYPE html>
<html>
<head>
    <title>Pull From Repo</title>

    <style>
        body {
            background-color:#000;
            color:#FFF;
            font-family:Arial, sans-serif;
        }
        pre {
            border:solid 1px #CCC;
            padding:10px;
            color:#EEE;
        }
        pre span {
            color:#0F0;
        }
    </style>
</head>

<?php
echo "<p>Starting pull...</p>";
echo "<pre><code>";

echo "<span>$</span> cd ../\n\n";
chdir(__DIR__ . '/../');

run("rm -rf *");
run("git clone -b Development https://github.com/The-Problem/The-Problem.git");
run("mv The-Problem/* .");
run("rm -rf The-Problem");
run("cp server/profile-production.php server/profile.php");
run("cp ../database.php server/database.php");

echo "<span>$</span> cat database.sql | ./import_sql\n";

require(__DIR__ . '/../server/database.php');
$con = new MySQLi(LIME_DB_HOST, LIME_DB_USERNAME, LIME_DB_PASSWORD, LIME_DB_DATABASE);
$con->multi_query(file_get_contents("database.sql"));
do {
    $con->use_result();
} while ($con->more_results() && $con->next_result());

echo "Checking for presence of tables...\n";
$res = $con->query("SHOW TABLES LIKE 'users'");

if ($con->errno) echo "<span style='color:red'>Error!</span> " . $con->error . " (" . $con->errno . ")\n";

if ($res->num_rows <= 0) echo "<span style='color:red'>Warning!</span> It doesn't look like the tables imported correctly.\n";
else echo "Everything looks OK.\n";
echo "Finished importing tables.\n\n";

echo "</code></pre>";
echo "<p>Success! Updated with Github repository.</p>";


function run($script) {
    echo "<span>$</span> $script\n";
    echo htmlentities(shell_exec($script)) . "\n";
}
?>

</html>