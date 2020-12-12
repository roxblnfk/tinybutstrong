<?php

error_reporting(E_ALL & ~E_USER_NOTICE & ~E_STRICT);
ini_set("display_errors", "On");
set_time_limit(0);
//ini_set('memory_limit', '256M');


$dir_test = dirname(__FILE__);
$dir_tbs = dirname($dir_test);
chdir($dir_test);

// include tbs classes
if (version_compare(PHP_VERSION, '5.0') < 0) {
    $tbsFileName = "{$dir_tbs}/tbs_class_php4.php";
} else {
    $tbsFileName = "{$dir_tbs}/tbs_class.php";
}

// include classes required for unit tests
if (version_compare(PHP_VERSION, '5.4') < 0) {
    // "@" is in order to avoid Deprecated warnings
   @require_once("{$dir_test}/simpletest/simpleTest.php");
   @require_once("{$dir_test}/simpletest/unit_tester.php");
    require_once("{$dir_test}/simpletest/reporter.php");
   @require_once("{$dir_test}/simpletest/mock_objects.php");
} else {
    require_once "{$dir_tbs}/vendor/autoload.php";
}

require_once($tbsFileName);

// other files required for unit tests
require_once("{$dir_test}/include/TBSUnitTestCase.php");

if (PHP_SAPI === 'cli') { // Text output
    require_once("{$dir_test}/include/TextCoverageReporter.php");
    $reporter = new TextCoverageReporter();
} else {                  // HTML output
    require_once("{$dir_test}/include/HtmlCodeCoverageReporter.php");
    $reporter = new HtmlCodeCoverageReporter(array($tbsFileName));
}

// include unit test classes
include("{$dir_test}/testcase/AttTestCase.php");
include("{$dir_test}/testcase/QuoteTestCase.php");
include("{$dir_test}/testcase/FrmTestCase.php");
include("{$dir_test}/testcase/StrconvTestCase.php");
include("{$dir_test}/testcase/FieldTestCase.php");
include("{$dir_test}/testcase/BlockTestCase.php");
include("{$dir_test}/testcase/BlockGrpTestCase.php");
include("{$dir_test}/testcase/MiscTestCase.php");
include("{$dir_test}/testcase/SubTplTestCase.php");
include("{$dir_test}/testcase/DataSourceTestCase.php");

// launch tests
$SimpleTest = new SimpleTest();
$tbs = new clsTinyButStrong();
$bit = (PHP_INT_SIZE <= 4) ? '32' : '64' ;
$test = new TestSuite('TinyButStrong v' . $tbs->Version . ' (with PHP ' . PHP_VERSION . " , " . $bit . "-bits), simpleTest " . $SimpleTest->getVersion() . ')');

$test->add(new FieldTestCase());
$test->add(new BlockTestCase());
$test->add(new AttTestCase());
$test->add(new QuoteTestCase());
$test->add(new FrmTestCase());
$test->add(new StrconvTestCase());
$test->add(new MiscTestCase());
$test->add(new SubTplTestCase());
$test->add(new DataSourceTestCase());

$test->run($reporter);
