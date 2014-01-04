<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 5:18 PM
 * To change this template use File | Settings | File Templates.
 */ 
class Parser_Twitter implements Parser_Interface {

    public function parseJson($json)
    {
        $jsonArr = json_decode($json,true);
        $res = array();
        foreach($jsonArr['statuses'] as $data){
            $tmp = array();
            $tmp['post_id']             = $data['id_str'];
            $tmp['post_text']           = $data['text'];
            $tmp['post_lang']           = $data['metadata']['iso_language_code'];
            $tmp['post_source']         = isset( $data['source']) ? strip_tags($data['source']): null;
            $tmp['user_profile_image']  = $data['user']['profile_image_url_https'];
            $tmp['user_name']           = $data['user']['name'];
            $tmp['user_screen_name']    = $data['user']['screen_name'];
            $tmp['user_id']             = $data['user']['id_str'];
            $tmp['user_lang']           = $data['user']['lang'];
            $tmp['user_location']       = $data['user']['location'];
            $tmp['user_followers_count']= $data['user']['followers_count'];
            $tmp['user_friend_count']   = $data['user']['friends_count'];
            $tmp['user_status_count']   = $data['user']['statuses_count'];
            $tmp['user_url']            = isset( $data['user']['url'] ) ? $data['user']['url'] : null;
            $tmp['date_published']      = $data['created_at'];
            $tmp['date_published_ts']   = strtotime($data['created_at']);
            $tmp['post_status']         = 'new';
            $tmp['post_type']           = 'tweet';
            $tmp['post_url']            = 'https://twitter.com/'.$tmp['user_screen_name']."/status/".$tmp['post_id'];
            $tmp['source']              = 'twitter';
            $tmp['date_added']          = date("Y-m-d h:i:s");

            $res = array_merge($res,array($tmp));
        }

        if($res){
            return $res;
        } else {
            return false;
        }
    }

}
