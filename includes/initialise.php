<?php
require "lib/password.php";
include('dbconfig.php');

defined('MAGIC_QUOTES_ACTIVE') ? null : define('MAGIC_QUOTES_ACTIVE', get_magic_quotes_gpc());
defined('REAL_ESCAPE_STRING_EXISTS') ? null : define('REAL_ESCAPE_STRING_EXISTS', function_exists('pdo_real_escape_string'));
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', dirname($_SERVER['SCRIPT_FILENAME']). DS);
defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT. 'includes'. DS);
require_once(LIB_PATH. 'database.php');
require_once(LIB_PATH. 'databaseobject.php');
require_once(LIB_PATH. 'session.php');
require_once(LIB_PATH. 'site.php');
require_once(LIB_PATH. 'language.php');
require_once(LIB_PATH. 'functions.php');
defined('CRLF') ? null : define('CRLF', "\r\n");
?>