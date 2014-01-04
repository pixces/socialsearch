<?php

class Controller
{
    protected $_controller;
    protected $_action;
    protected $_template;
    protected $_pageTitle;
    protected $_pageType;
    protected $_level1_sef;
    protected $_method;
    protected $_request;
    protected $_is_api = false;

    public $doNotRenderHeader;
    public $render;

    public function set_pageType($type)
    {
        $this->set('pageType',$type);
    }

    public function set_pageTitle($title)
    {
        $this->set('pageTitle',$title);
    }

    function __construct($controller, $action)
    {
        #define request methods and request params
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");

        if (isset($_REQUEST['request_type']) && $_REQUEST['request_type'] == 'api'){
            //header('content-type: application/json; charset=utf-8');
            $this->_is_api = true;
        }

        $this->_method = $_SERVER['REQUEST_METHOD'];
        if ($this->_method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->_method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->_method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        if ($this->_method == 'GET'){
            $this->_request = $_REQUEST;
        }

        /*

        switch($this->method) {
            case 'DELETE':
            case 'POST':
            case 'GET':
                $this->request = $this->_cleanInputs($_GET);
                break;
            case 'PUT':
                $this->request = $this->_cleanInputs($_GET);
                $this->file = file_get_contents("php://input");
                break;
            default:
                $this->_response('Invalid Method', 405);
                break;
        }   */


        global $inflect;
        $this->_controller = ucfirst($controller);
        $this->_action = $action;

        $model = ucfirst($inflect->singularize($controller));
        $this->doNotRenderHeader = 0;
        $this->render = 1;

        $this->_loadModel($model);
        $this->_template = new Template($controller, $action);

        #get navigation to be displayed
    }

    private function get_navigation()
    {

    }

    function set($name, $value)
    {
        $this->_template->set($name, $value);
    }

    function setTemplate($template){
        $this->_template->setTemplate($template);
    }

    function __destruct()
    {
        if ($this->render) {
            if ($this->_is_api){
                $this->_template->renderJson();
            } else {
                $this->_template->render($this->doNotRenderHeader);
            }
        }
    }

    function _loadModel($model)
    {
        if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($model) . '.php')) {
            $this->$model = new $model;
        }
    }

    public function getCrumbs(){}

}