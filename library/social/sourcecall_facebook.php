<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 4:24 PM
 * To change this template use File | Settings | File Templates.
 */ 
class SourceCall_Facebook implements SourceCall_Interface {

    protected $requestMethod = 'GET';
    protected $params = array();
    protected $callUrl = "/search";

    public function getFeed($data)
    {
        //prepare parameters;
        $this->params['q'] = urldecode( $data['keyword_string'] );
        $this->params['limit'] = $data['post_count'];
        $this->params['type'] = 'post';

        $getField = '?'.http_build_query($this->params);
        $this->requestMethod = 'GET';
        $this->callUrl .= $getField;

        $facebook = new Facebook(array('appId'=>FB_APP_ID,'secret'=>FB_SECRET_KEY));

        try{
            $data = $facebook->api($this->callUrl);
            if ($data){
                return $data;
            }
        } catch (FacebookApiException $e) {
                error_log($e);
        }
    }

}
