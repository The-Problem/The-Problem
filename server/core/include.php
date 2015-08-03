<?php

include(__DIR__ . '/../cache/include.php');


if (!array_key_exists('lime_include_cache', $GLOBALS) || !is_array($GLOBALS['lime_include_cache'])) {
    $GLOBALS['lime_include_cache'] = array();
}

$GLOBALS['lime_included_list'] = array();
$GLOBALS['lime_has_cache_changed'] = false;

$GLOBALS['lime_cache_enabled'] = true;
$GLOBALS['lime_cache_aggressive'] = false;

/**
 * Intelligently includes a file by caching it if required
 *
 * @param String $file The file path, relative to the server directory
 * @param Boolean $relative
 */
function l_include($file, $relative = true) {
    if ($relative) $file = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . $file;

    if (in_array($file, $GLOBALS['lime_included_list'])) return;
    array_push($GLOBALS['lime_include_list'], $file);

    if (array_key_exists($file, $GLOBALS['lime_include_cache'])) call_user_func($GLOBALS['lime_include_cache'][$file]);
    else {
        $GLOBALS['lime_has_cache_changed'] = true;
        $GLOBALS['lime_include_cache'][$file] = function() {};
        require($file);
    }
}

/**
 * Flushes the include cache to the file
 */
function l_include_flush() {
    if (!$GLOBALS['lime_has_cache_changed'] || !$GLOBALS['lime_cache_enabled']) return;

    if ($GLOBALS['lime_cache_aggressive']) {
        $array_values = array();
        $code = array();

        foreach ($GLOBALS['lime_include_cache'] as $path => $func) {
            $escaped_path = str_replace("'", "\\'", $path);

            array_push($array_values, "'$escaped_path' => function() { }");
            array_push($code, preg_replace('/^.+\n/', '', file_get_contents($path)));
        }

        $value = "<?php\n";
        $value .= '$GLOBALS["lime_include_cache"] = array(' . "\n    " . implode(",\n    ", $array_values) . "\n);";
        $value .= "\n" . implode("\n", $code);

        file_put_contents(__DIR__ . '/../cache/include.php', $value);
    } else {

        $values = array();
        foreach ($GLOBALS['lime_include_cache'] as $path => $func) {
            $escaped_path = str_replace("'", "\\'", $path);

            array_push($values, "'$escaped_path' => function() { include('$escaped_path'); }");
        }

        $value = "<?php\n";
        $value .= '$GLOBALS["lime_include_cache"] = array(' . "\n    " . implode(",\n    ", $values) . "\n);";

        file_put_contents(__DIR__ . '/../cache/include.php', $value);
    }
}