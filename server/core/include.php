<?php

define('LIME_CACHE_DISABLED', 0);
define('LIME_CACHE_SIMPLE', 1);
define('LIME_CACHE_AGGRESSIVE', 2);

$GLOBALS['lime_include_cache'] = array();
$GLOBALS['lime_included_list'] = array();
$GLOBALS['lime_has_cache_changed'] = false;

$GLOBALS["lime_cache_version"] = "0.1";

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

    $file = realpath($file);

    if (LIME_CACHE_MODE === LIME_CACHE_DISABLED) require_once($file);
    else {
        if (!$has_included) {
            if (file_exists( __DIR__ . '/../cache/include.php')) include(__DIR__ . '/../cache/include.php');

            if (!array_key_exists('lime_include_cache', $GLOBALS) || !is_array($GLOBALS['lime_include_cache'])) {
                $GLOBALS['lime_include_cache'] = array();
            }

            if ($GLOBALS["lime_icache_mode"] === LIME_CACHE_AGGRESSIVE && LIME_CACHE_MODE !== LIME_CACHE_AGGRESSIVE) {
                $files_have_loaded = true;
            }
            if ($GLOBALS["lime_icache_mode"] !== LIME_CACHE_MODE) $GLOBALS["lime_include_cache"] = array();
            if ($GLOBALS["lime_icache_version"] !== $GLOBALS["lime_cache_version"]) $GLOBALS["lime_include_cache"] = array();

            $has_included = true;
        }

        if (in_array($file, $included_list)) return;
        array_push($included_list, $file);

        if (array_key_exists($file, $GLOBALS['lime_include_cache'])) call_user_func($GLOBALS['lime_include_cache'][$file]);
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
    $value .= '$GLOBALS["lime_icache_version"] = "' . $GLOBALS['lime_cache_version'] . '";' . "\n";
    $value .= '$GLOBALS["lime_icache_mode"] = ' . LIME_CACHE_MODE . ";\n";

    if (LIME_CACHE_MODE === LIME_CACHE_AGGRESSIVE) {
        $array_values = array();
        $code = array();

        foreach ($GLOBALS['lime_include_cache'] as $path => $func) {

            $escaped_path = str_replace("'", "\\'", str_replace("\\", "\\\\", $path));

            array_push($array_values, "'$escaped_path' => function() { }");
            array_push($code, "\n//START:  $path\n" . preg_replace('/^.+\n/', '', file_get_contents($path)));
        }

        $value .= '$GLOBALS["lime_include_cache"] = array(' . "\n    " . implode(",\n    ", $array_values) . "\n);";
        $value .= "\n" . implode("\n", $code);
    } else {

        $values = array();
        foreach ($GLOBALS['lime_include_cache'] as $path => $func) {
            $escaped_path = str_replace("'", "\\'", $path);

            array_push($values, "'$escaped_path' => function() { include('$escaped_path'); }");
        }

        $value .= '$GLOBALS["lime_include_cache"] = array(' . "\n    " . implode(",\n    ", $values) . "\n);";
    }

    file_put_contents(__DIR__ . '/../cache/include.php', $value);
}