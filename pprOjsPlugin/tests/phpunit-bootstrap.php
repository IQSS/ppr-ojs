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

define('INDEX_FILE_LOCATION', dirname(dirname(__FILE__)).'/index.php');
echo INDEX_FILE_LOCATION;
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

// NB: Our test framework provides the possibility to
// import mock classes to replace regular classes.
// This is necessary to mock static method calls.
// Unfortunately we can only define one mock environment
// per test run as PHP does not allow to change a class
// implementation while running.
// We therefore need to define the mock environment globally
// so that tests can check their environment requirement
// before they start importing.
if (isset($_SERVER['PKP_MOCK_ENV'])) {
	define('PHPUNIT_CURRENT_MOCK_ENV', $_SERVER['PKP_MOCK_ENV']);
	$mockEnvs = '';
	foreach(array('lib/pkp/tests/mock/', 'tests/mock/') as $testDir) {
		$normalizedMockEnv = normalizeMockEnvironment($testDir . $_SERVER['PKP_MOCK_ENV']);
		if ($normalizedMockEnv) {
			if (!empty($mockEnvs)) $mockEnvs .= ';';
			$mockEnvs .= $normalizedMockEnv;
		}
	}
	define('PHPUNIT_ADDITIONAL_INCLUDE_DIRS', $mockEnvs);
} else {
	// Use the current test folder as mock environment
	// if no environment has been explicitly set.
	// The phpunit cli tool's last parameter is the test class, file or directory
	define('PHPUNIT_CURRENT_MOCK_ENV', '__NONE__');
	assert(is_array($_SERVER['argv']) and count($_SERVER['argv'])>1);
	$testDir = end($_SERVER['argv']);
	define('PHPUNIT_ADDITIONAL_INCLUDE_DIRS', normalizeMockEnvironment($testDir));
}

/**
 *  A function to declare dependency on a mock environment.
 *  Tests depending on static mock classes should use this
 *  function so that they cannot be executed in the wrong
 *  test environment.
 *
 *  @param $mockEnv string
 */
function require_mock_env($mockEnv) {
	if (PHPUNIT_CURRENT_MOCK_ENV == '__NONE__' || PHPUNIT_CURRENT_MOCK_ENV != $mockEnv) {
		// Tests that require different mock environments cannot run
		// in the same test batch as this would require re-defining
		// already defined classes.
		debug_print_backtrace();
		die(
			'You are trying to run a test in the wrong mock environment ('
			. PHPUNIT_CURRENT_MOCK_ENV . ' rather than ' . $mockEnv.')!'
		);
	}
}

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
	static $mockEnvArray = [];

	// Test whether we have a mock implementation of
	// the requested class.
	foreach($mockEnvArray as $mockEnv) {
		$classParts = explode('.', $class);
		$mockClassFile = $mockEnv . '/Mock'.array_pop($classParts) . '.inc.php';
		if (file_exists($mockClassFile)) {
			require_once($mockClassFile);
			return;
		}
	}

    // No mock implementation found, do the normal import
    $filePath = str_replace('.', '/', $class) . '.inc.php';
    require_once(dirname(INDEX_FILE_LOCATION).'/'.$filePath);
}

/**
 * A function to transform a mock environment name
 * in a list of additional include directories.
 *
 * @param $mockEnv string
 * @return string A mock environment directory to check when
 * importing class files.
 */
function normalizeMockEnvironment($mockEnv) {
		if (substr($mockEnv, 0, 1) != '/') {
			$mockEnv = getcwd() . '/' . $mockEnv;
		}
		if (!is_dir($mockEnv)) {
			$mockEnv = dirname($mockEnv);
		}
		$mockEnv = realpath($mockEnv);

		// Test whether this is a valid directory.
		if (is_dir($mockEnv)) {
			return $mockEnv;
		} else {
			// Make sure that we do not try to
			// identify a mock env again but mark
			// it as "not found".
			return false;
		}
}

// Remove the PKP error handler so that PHPUnit
// can set its own error handler and catch errors for us.
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

// Show errors in the UI
ini_set('display_errors', true);
