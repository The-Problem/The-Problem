<?php
/*** SERVER CONFIG ***/

/**
 * Server Environment
 * Changes some things like logging, debugging info, etc
 *
 * Valid options:
 *  - LIME_ENV_DEV  - Development environment
 *  - LIME_ENV_PROD - Production environment
 */
define('LIME_ENV', LIME_ENV_DEV);

/**
 * Whether to enable the client-side terminal
 * This effectively allows any user of the site to run shell commands and do bad things
 * DO NOT have this enabled on a production server - Lime automatically disables it if
 * the environment is production anyway.
 *
 * Valid options:
 *  - LIME_TERMINAL_ENABLED  - Enable the terminal
 *  - LIME_TERMINAL_DISABLED - Disable the terminal
 */
define('LIME_TERMINAL_MODE', LIME_TERMINAL_ENABLED);

/*** CACHE CONFIG ***/

/**
 * Include cache
 * Caches dynamic file includes, for example from the library system, so that they
 * can be optimized by PHP.
 *
 * Valid options:
 *  - LIME_CACHE_DISABLED   - No include caching
 *  - LIME_CACHE_SIMPLE     - Cache file references, fast but you'll need to invalidate the cache if you move Lime
 *  - LIME_CACHE_AGGRESSIVE - Cache file contents, faster than simple but you'll need to invalidate the cache every time you change a file
 */
define('LIME_CACHE_MODE', LIME_CACHE_SIMPLE);

/**
 * Library cache
 * Caches library definition files (library.json), and when combined with the include cache,
 * almost completely eliminates file reading.
 *
 * Valid options:
 *  - LIME_LIBCACHE_DISABLED   - No library caching
 *  - LIME_LIBCACHE_VALIDATE   - Still caches the files, but checks the last edit date and invalidates the cache if it has changed
 *  - LIME_LIBCACHE_AGGRESSIVE - Caches the files, and doesn't check for edit dates
 */
define('LIME_LIBCACHE_MODE', LIME_LIBCACHE_VALIDATE);

require('database.php');