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

echo "<span>$</span> cd ../\n";
chdir(__DIR__ . '/../');

run("rm -rf *");
run("git clone -b Development https://github.com/The-Problem/The-Problem.git");
run("mv The-Problem/* .");
run("rm -rf The-Problem");
run("cp server/profile-production.php server/profile.php");
run("cp ../database.php server/database.php");
echo "</code></pre>";
echo "<p>Success! Updated with Github repository.</p>";


function run($script) {
    echo "<span>$</span> $script\n";
    echo htmlentities(shell_exec($script)) . "\n";
}
?>

</html>