<?php

error_reporting(E_ALL);
ini_set("display_errors", "On");
set_time_limit(0);
//ini_set('memory_limit', '256M');


$dir_testu = dirname(__FILE__);
$dir_tbs = dirname($dir_testu);
$dir_plugins = $dir_tbs . DIRECTORY_SEPARATOR . 'plugins';

if (!file_exists($dir_plugins)) {
    $dir_plugins = dirname($dir_tbs) . DIRECTORY_SEPARATOR . 'tbs_plugins';
}

// include tbs classes
if (version_compare(PHP_VERSION, '5.0') < 0) {
    $tbsFileName = $dir_tbs . DIRECTORY_SEPARATOR . 'tbs_class_php4.php';
} else {
    $tbsFileName = $dir_tbs . DIRECTORY_SEPARATOR . 'tbs_class.php';
}

// include classes required for unit tests
if (version_compare(PHP_VERSION, '5.4') < 0) {
    // "@" is in order to avoid Deprecated warnings
   @require_once($dir_testu . DIRECTORY_SEPARATOR . 'simpletest' . DIRECTORY_SEPARATOR . 'simpleTest.php');
   @require_once($dir_testu . DIRECTORY_SEPARATOR . 'simpletest' . DIRECTORY_SEPARATOR . 'unit_tester.php');
    require_once($dir_testu . DIRECTORY_SEPARATOR . 'simpletest' . DIRECTORY_SEPARATOR . 'reporter.php');
   @require_once($dir_testu . DIRECTORY_SEPARATOR . 'simpletest' . DIRECTORY_SEPARATOR . 'mock_objects.php');
} else {
    require_once $dir_tbs . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
}


require_once($tbsFileName);

if (!file_exists($dir_plugins)) {
    echo("<span style='color:red;font-weight:900'>Plug-ins directory not found!</span>");
} else {
    require_once($dir_plugins . DIRECTORY_SEPARATOR . 'tbs_plugin_html.php');
    require_once($dir_plugins . DIRECTORY_SEPARATOR . 'tbs_plugin_bypage.php');
    require_once($dir_plugins . DIRECTORY_SEPARATOR . 'tbs_plugin_cache.php');
    require_once($dir_plugins . DIRECTORY_SEPARATOR . 'tbs_plugin_mergeonfly.php');
    require_once($dir_plugins . DIRECTORY_SEPARATOR . 'tbs_plugin_navbar.php');
   @require_once($dir_plugins . DIRECTORY_SEPARATOR . 'tbs_plugin_ref.php');
   @require_once($dir_plugins . DIRECTORY_SEPARATOR . 'tbs_plugin_syntaxes.php');
}

// other files required for unit tests
require_once($dir_testu . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'TBSUnitTestCase.php');
require_once($dir_testu . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'HtmlCodeCoverageReporter.php');

// include unit test classes
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'AttTestCase.php');
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'QuoteTestCase.php');
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'FrmTestCase.php');
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'StrconvTestCase.php');
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'FieldTestCase.php');
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'BlockTestCase.php');
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'MiscTestCase.php');
include($dir_testu . DIRECTORY_SEPARATOR . 'testcase' . DIRECTORY_SEPARATOR . 'SubTplTestCase.php');

// launch tests

$SimpleTest = new SimpleTest();
$tbs = new clsTinyButStrong();
$test = new TestSuite('TinyButStrong v' . $tbs->Version . ' (with PHP ' . PHP_VERSION . ', simpleTest ' . $SimpleTest->getVersion() . ')');
$test->add(new FieldTestCase());
$test->add(new BlockTestCase());
$test->add(new AttTestCase());
$test->add(new QuoteTestCase());
$test->add(new FrmTestCase());
$test->add(new StrconvTestCase());
$test->add(new MiscTestCase());
$test->add(new SubTplTestCase());
$test->run(new HtmlCodeCoverageReporter(array($tbsFileName, $dir_plugins . DIRECTORY_SEPARATOR)));
