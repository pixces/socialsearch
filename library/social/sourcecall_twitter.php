<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 4:23 PM
 * To change this template use File | Settings | File Templates.
 */ 
class SourceCall_Twitter implements SourceCall_Interface{

    protected $settings = array(
        'oauth_access_token'        => TW_ACCESS_KEY,
        'oauth_access_token_secret' => TW_ACCESS_SECRET,
        'consumer_key'              => TW_CONSUMER_KEY,
        'consumer_secret'           => TW_CONSUMER_SECRET
    );

    protected $requestMethod = 'GET';
    protected $callUrl = "https://api.twitter.com/1.1/";
    protected $params = array();

    public function getFeed($data)
    {
        //prepare parameters;
        $this->params['q'] = urldecode( $data['keyword_string'] );
        $this->params['count'] = $data['post_count'];

        $this->requestMethod = 'GET';
        $this->callUrl = $data['base_api_url'];

        $getField = '?'.http_build_query($this->params);

        $twitter = new Twitter_oAuth($this->settings);
        $jsonData = $twitter->setGetfield($getField)->buildOauth($this->callUrl,$this->requestMethod)->performRequest();

        if ($jsonData){
            return $jsonData;
        } else {
            echo "API Call returned no feed"."<br>";
            return false;
        }

    }



}
