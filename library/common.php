<?php

/** Check if environment is development and display errors **/
function setEnvironment()
{
    if (ENVIRONMENT == 'development') {
        error_reporting(E_ALL ^E_NOTICE );
        //error_reporting(E_ERROR | E_WARNING | E_PARSE);
        ini_set('display_errors', 'On');

        //create connections params
        define('DB_NAME', 'social');
        define('DB_USER', 'root');
        define('DB_PASSWORD', 'root');
        define('DB_HOST', 'localhost');
        define('DB_CHARSET', 'utf8');
        define('DB_COLLATE', '');
    } else {
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors', 'Off');
        ini_set('log_errors', 'On');
        ini_set('error_log', ROOT . DS . 'tmp' . DS . 'error.log');

        //create connection params
        define("DB_USER", "");
        define("DB_PASSWORD", "");
        define("DB_NAME", "");
        define("DB_HOST", "");
        define('DB_CHARSET', '');
        define('DB_COLLATE', '');

    }
}

/** Check for Magic Quotes and remove them **/
function stripSlashesDeep($value)
{
    $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
    return $value;
}

function removeMagicQuotes()
{
    if (get_magic_quotes_gpc()) {
        $_GET = stripSlashesDeep($_GET);
        $_POST = stripSlashesDeep($_POST);
        $_COOKIE = stripSlashesDeep($_COOKIE);
    }
}

/** Check register globals and remove them **/

function unregisterGlobals()
{
    if (ini_get('register_globals')) {
        $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if ($var === $GLOBALS[$key]) {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}


/** Secondary Call Function **/

function performAction($controller, $action, $queryString = null, $render = 0)
{
    $controllerName = ucfirst($controller) . 'Controller';
    $dispatch = new $controllerName($controller, $action);
    $dispatch->render = $render;
    return call_user_func_array(array($dispatch, $action), $queryString);
}

/** Routing **/

function routeURL($url)
{
    global $routing;

    foreach ($routing as $pattern => $result) {
        if (preg_match($pattern, $url)) {
            return preg_replace($pattern, $result, $url);
        }
    }

    return ($url);
}

/** Main Call Function **/

function callHook()
{
    #get the request_method
    #get the request query
    $query = $_SERVER['QUERY_STRING'];

    if ($query){
        parse_str($query,$request);
        $request['http_method'] = $_SERVER['REQUEST_METHOD'];
        $url = $request['url']."/";

        #check if the url starts with api, then append method to the call
        if (preg_match('/^api/',$url)){
            $url = $url."get/";
            $request['request_type'] = 'api';
        }
        unset($request['url']);

        #make this request a global parameter
        $_REQUEST = $request;
        unset($request);
    }

    global $default;
    $queryString = array();

    if (!isset($url)) {
        $controller = $default['controller'];
        $action = $default['action'];
    } else {
        $url = rtrim(routeURL($url), '/');

        $urlArray = array();
        $urlArray = explode("/", $url);

        $controller = $urlArray[0];
        array_shift($urlArray);
        if (isset($urlArray[0])) {
            $action = $urlArray[0];
            array_shift($urlArray);
        } else {
            $action = 'index'; // Default Action
        }
        $queryString = $urlArray;
    }
    //print_r(array($controller,$action,$queryString));
    $controllerName = ucfirst($controller) . 'Controller';

    $dispatch = new $controllerName($controller, $action);

    if ((int)method_exists($controllerName, $action)) {
        if (method_exists($controllerName, 'beforeAction')) {
            call_user_func_array(array($dispatch, "beforeAction"), $queryString);
        }
        call_user_func_array(array($dispatch, $action), $queryString);

        if (method_exists($controllerName, 'afterAction')) {
            call_user_func_array(array($dispatch, "afterAction"), $queryString);
        }
    } else {
        /* Error Generation Code Here */
    }
}

/** Autoload any classes that are required **/
function __autoload($className)
{
    if (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.class.php')) {
        require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.class.php');
    } else if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
        require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php');
    } else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php')) {
        require_once(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php');
    } else if (file_exists(ROOT . DS . 'library' . DS . 'social' . DS . strtolower($className) . '.php')) {
        require_once(ROOT . DS . 'library' . DS . 'social' . DS . strtolower($className) . '.php');
    } else {
        /* Error Generation Code Here */
        echo "Class definition for " . strtolower($className) . ' was not found';
        debug_print_backtrace();
    }
}

function load_config()
{
    #parse the ini file at public/files/ini
    #check values for environment
    #loop through each to define constants for each functional values


    #get the paths form css, js and image folders
    define('SITE_CSS', SITE_URL . DS . 'public/css/');
    define('SITE_JS', SITE_URL . DS . 'public/js/');
    define('SITE_IMAGE', SITE_URL . DS . 'public/images/');
    define('SITE_UPLOAD', SITE_URL . DS . 'public/upload/');
    define('UPLOAD_DST_DIR' , ROOT . DS . 'public' . DS . 'upload');

    #include the core function file
    require_once (ROOT . DS . 'library' . DS . 'functions.php');

    #generate a new user
    /*createNewUser();*/

}

/** GZip Output **/

function gzipOutput()
{
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (0 !== strpos($ua, 'Mozilla/4.0 (compatible; MSIE ')
        || false !== strpos($ua, 'Opera')
    ) {
        return false;
    }

    $version = (float)substr($ua, 30);
    return (
        $version < 6
            || ($version == 6 && false === strpos($ua, 'SV1'))
    );
}

/** Get Required Files **/

#initialize all data
gzipOutput() || ob_start("ob_gzhandler");

$cache = new Cache();
$inflect = new Inflection();
$utils = new Utils();

setEnvironment();
removeMagicQuotes();
unregisterGlobals();
load_config();
callHook();


