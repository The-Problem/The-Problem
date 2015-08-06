<?php

define('LIME_CACHE_DISABLED', 0);
define('LIME_CACHE_SIMPLE', 1);
define('LIME_CACHE_AGGRESSIVE', 2);

define('LIME_CACHE_MODE', LIME_CACHE_SIMPLE);

if (!array_key_exists('lime_include_cache', $GLOBALS) || !is_array($GLOBALS['lime_include_cache'])) {
    $GLOBALS['lime_include_cache'] = array();
}

$GLOBALS['lime_included_list'] = array();
$GLOBALS['lime_has_cache_changed'] = false;

//var_dump($GLOBALS['lime_include_cache']);

/**
 * Intelligently includes a file by caching it if required
 *
 * @param String $file The file path, relative to the server directory
 * @param Boolean $relative
 */
function l_include($file, $relative = true) {
    static $included_list = array();
    static $has_included = false;


    if ($relative) $file = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . $file;

    if (LIME_CACHE_MODE === LIME_CACHE_DISABLED) require_once($file);
    else {
        if (!$has_included) {
            if (file_exists( __DIR__ . '/../cache/include.php')) include(__DIR__ . '/../cache/include.php');
            $has_included = true;
        }

        if (in_array($file, $included_list)) return;
        array_push($included_list, $file);

        if (array_key_exists($file, $GLOBALS['lime_include_cache'])) call_user_func($GLOBALS['lime_include_cache'][$file]);
        else {
            $GLOBALS['lime_has_cache_changed'] = true;
            $GLOBALS['lime_include_cache'][$file] = function () {
            };
            require($file);
        }
    }
}

/**
 * Flushes the include cache to the file
 */
function l_include_flush() {
    if (LIME_CACHE_MODE === LIME_CACHE_DISABLED || !$GLOBALS['lime_has_cache_changed']) return;

    if (LIME_CACHE_MODE === LIME_CACHE_AGGRESSIVE) {
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