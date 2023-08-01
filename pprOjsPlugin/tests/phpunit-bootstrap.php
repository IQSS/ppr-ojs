<?php
/**
 * PKP-specific phpunit bootstrap file.
 *
 * Integrates PHPUnit with the PKP application environment
 * and enables running/debugging tests from within Eclipse or
 * other CASE tools.
 */


// This script may not be executed remotely.
if (isset($_SERVER['SERVER_NAME'])) {
	die('This script can only be executed from the command-line.');
}

//define('INDEX_FILE_LOCATION', dirname(dirname(__FILE__)).'/index.php');
define('INDEX_FILE_LOCATION', '/var/www/html/index.php');
define('PPR_PLUGIN_LOCATION', dirname(dirname(__FILE__)));
echo INDEX_FILE_LOCATION;
echo PPR_PLUGIN_LOCATION;
chdir(dirname(INDEX_FILE_LOCATION));

// Configure PKP error handling for tests
define('DONT_DIE_ON_ERROR', true);

// Don't support sessions
define('SESSION_DISABLE_INIT', true);

// Configure assertions for tests
ini_set('assert.active', true);
ini_set('assert.bail', false);
ini_set('assert.warning', true);
ini_set('assert.callback', null);
ini_set('assert.quiet_eval', false);

/**
 * Provide a test-specific implementation of the import function
 * so we can drop in mock classes, especially to mock
 * static method calls.
 *
 * @see bootstrap.inc.php
 *
 * @param string $class
 */
function import($class) {
    $classParts = explode('.', $class);
    $mockClassFile = PPR_PLUGIN_LOCATION . '/tests/src/mocks/'.array_pop($classParts) . '.inc.php';
    if (file_exists($mockClassFile)) {
        require_once($mockClassFile);
        return;
    }

    $filePath = str_replace('.', '/', $class) . '.inc.php';
    if (file_exists(PPR_PLUGIN_LOCATION.'/'.$filePath)) {
        require_once(PPR_PLUGIN_LOCATION.'/'.$filePath);
        return;
    }

    // No mock implementation found, do the normal import
    require_once('./'.$filePath);
}

require_once('./lib/pkp/includes/bootstrap.inc.php');

// Remove the PKP error handler so that PHPUnit
// can set its own error handler and catch errors for us.
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

// Show errors in the UI
ini_set('display_errors', true);
