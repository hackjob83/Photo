<?php
// during dev and testing
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// for production
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
// ini_set("log_errors", 1);
// ini_set("error_log", "/path/to/error/log.log");


define('SITE_TITLE','Photo Upload Demo');

define('DEVELOPER_EMAIL','your_email@gmail.com');
define('MAX_FILE_SIZE',5242880);

date_default_timezone_set('America/Chicago');

define('BASE_URL','http://your_site.com/');

define('IMAGES_DIR', BASE_URL . 'images/');
define('JS_DIR', BASE_URL . 'js/');
define('CSS_DIR', BASE_URL . 'css/');
define('INCLUDES_DIR', 'includes/');

// database stuffs
define('HOST','');
define('DB_NAME','');
define('USER','');
define('PASS','');
define('DSN', 'mysql:host=' . HOST . ';dbname=' . DB_NAME . ';charset=utf8');

// turn off prepare emulation since we are > 5.1 
// and can set the errmode here instead of using $DBH->setAttribute()
try {
    $DBH = new PDO(DSN, USER, PASS, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    echo "Cannot connect to database " . DB_NAME . " for reason : " . $e->getMessage();
    exit();
}

require_once 'classes/gump.class.php';
require_once 'classes/photo.class.php';
require_once 'functions.php';

$Gump = new GUMP();