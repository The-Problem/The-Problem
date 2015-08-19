<?php

define('LIME_CACHE_DISABLED', 0);
define('LIME_CACHE_SIMPLE', 1);
define('LIME_CACHE_AGGRESSIVE', 2);

define('LIME_CACHE_ROOT', __DIR__ . '/../cache');

$GLOBALS['lime_include_cache'] = array();
$GLOBALS['lime_included_list'] = array();
$GLOBALS['lime_has_cache_changed'] = false;

$GLOBALS["lime_cache_version"] = 2;

/**
 * Intelligently includes a file by caching it if required
 *
 * @param String $file The file path, relative to the server directory
 * @param Boolean $relative
 */
function l_include($file, $relative = true) {
    static $included_list = array();
    static $has_included = false;
    static $files_have_loaded = false;

    if ($relative) $file = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . $file;

    $file = __realpath($file);

    if (LIME_CACHE_MODE === LIME_CACHE_DISABLED) require_once($file);
    else {
        if (!$has_included) {
            if (file_exists(LIME_CACHE_ROOT . '/include.php')) include(LIME_CACHE_ROOT . '/include.php');

            if (!array_key_exists('lime_include_cache', $GLOBALS) || !is_array($GLOBALS['lime_include_cache'])) {
                $GLOBALS['lime_include_cache'] = array();
            }

            if ($GLOBALS["lime_icache_mode"] === LIME_CACHE_AGGRESSIVE && LIME_CACHE_MODE !== LIME_CACHE_AGGRESSIVE) {
                $files_have_loaded = true;
            }
            if ($GLOBALS["lime_icache_mode"] !== LIME_CACHE_MODE
                || $GLOBALS["lime_icache_version"] !== $GLOBALS["lime_cache_version"]) {
                $GLOBALS["lime_include_cache"] = array();
                if ($GLOBALS["lime_icache_mode"] === LIME_CACHE_AGGRESSIVE) $files_have_loaded = true;
            }

            $has_included = true;
        }

        if (in_array($file, $included_list)) return;
        array_push($included_list, $file);

        if (array_key_exists($file, $GLOBALS['lime_include_cache'])) {
            call_user_func($GLOBALS['lime_include_cache'][$file]);
        }
        else {
            $GLOBALS['lime_has_cache_changed'] = true;
            $GLOBALS['lime_include_cache'][$file] = function () {
            };
            if (!$files_have_loaded) require($file);
        }
    }
}

/**
 * Flushes the include cache to the file
 */
function l_include_flush() {
    if (LIME_CACHE_MODE === LIME_CACHE_DISABLED || !$GLOBALS['lime_has_cache_changed']) return;

    $value = "<?php\n";
    if (LIME_CACHE_MODE === LIME_CACHE_AGGRESSIVE) $value .= "define('LIME_IN_CACHE', true);\n";
    $value .= '$GLOBALS["lime_icache_version"] = ' . $GLOBALS['lime_cache_version'] . ';' . "\n";
    $value .= '$GLOBALS["lime_icache_mode"] = ' . LIME_CACHE_MODE . ";\n";

    if (LIME_CACHE_MODE === LIME_CACHE_AGGRESSIVE) {
        $array_values = array();
        $code = array();

        foreach ($GLOBALS['lime_include_cache'] as $path => $func) {

            $escaped_path = str_replace("'", "\\'", str_replace("\\", "\\\\", $path));

            array_push($array_values, "'$escaped_path' => function() { }");

            $file = file_get_contents($path);
            $start_tags = substr_count($file, "<?php");
            $end_tags = substr_count($file, "?>");
            if ($start_tags !== $end_tags) $file .= "\n?>";

            array_push($code, /*"\n//START:  $path\n" .*/ trim($file));
        }

        $value .= '$GLOBALS["lime_include_cache"] = array(' . "\n    " . implode(",\n    ", $array_values) . "\n);";
        $value .= "\n?>" . implode("", $code);
    } else {

        $values = array();
        foreach ($GLOBALS['lime_include_cache'] as $path => $func) {
            $escaped_path = str_replace("'", "\\'", $path);

            array_push($values, "'$escaped_path' => function() { include('$escaped_path'); }");
        }

        $value .= '$GLOBALS["lime_include_cache"] = array(' . "\n    " . implode(",\n    ", $values) . "\n);";
    }

    file_put_contents(LIME_CACHE_ROOT . '/include.php', $value);
}

function __realpath($filename) {
    $filename = str_replace('//', '/', $filename);
    $parts = explode('/', $filename);
    $out = array();
    foreach ($parts as $part){
        if ($part == '.') continue;
        if ($part == '..') {
            array_pop($out);
            continue;
        }
        $out[] = $part;
    }
    return implode('/', $out);
}