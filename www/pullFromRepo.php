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

include("../server/core/lime.php");
LimePHP::initialize();

Library::get("jobs");
Jobs::execute("pullFromRepo", array());

echo "</code></pre>";
echo "<p>Success! Updated with Github repository.</p>";

Library::flush_cache();
l_include_flush();
?>

</html>