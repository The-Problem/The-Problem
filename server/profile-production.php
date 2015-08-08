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
define('LIME_ENV', LIME_ENV_PROD);

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
define('LIME_CACHE_MODE', LIME_CACHE_AGGRESSIVE);