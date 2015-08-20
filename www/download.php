<?php
    if (array_key_exists('branch', $_GET)) {
        $url = 'https://github.com/The-Problem/The-Problem/archive/' . urlencode($_GET['branch']) . '.zip';

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init($url);

        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        $header = curl_getinfo($ch);

        curl_close($ch);


        header("Content-Type: application/zip");
        die($data);
    }
?><!DOCTYPE html>
<html>
<head>
    <title>Download The Problem</title>
</head>
<body>
<h1>Download</h1>
<form method="get">
    <label>Pick a branch: <select name="branch">
        <?php
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/The-Problem/The-Problem/branches');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: The-Problem-Download'));
            $result = curl_exec($ch);
            curl_close($ch);
            $branches = json_decode($result, true);

            foreach ($branches as $branch) {
                $name = htmlentities($branch["name"]);
                echo "<option value='$name'>$name</option>\n";
            }
        ?>
    </select></label>
    <button type="submit">Download</button>
</form>
</body>
</html>