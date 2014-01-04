<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 5:18 PM
 * To change this template use File | Settings | File Templates.
 */ 
class Parser_Facebook implements Parser_Interface  {

    public function parseJson($json)
    {
        $res = array();
        foreach($json['data'] as $data){

            $tmp = array();
            $tmp['post_id']             = $data['id'];
            $tmp['user_name']           = $data['from']['name'];
            $tmp['user_id']             = $data['from']['id'];
            $tmp['user_screen_name']    = null;
            $tmp['post_text']           = $data['message'];
            $tmp['post_story_text']     = isset($data['story']) ? $data['story'] : null;
            $tmp['post_picture']        = isset($data['picture']) ? $data['picture'] : null;
            $tmp['post_link']           = isset($data['link']) ? $data['link'] : null;
            $tmp['post_name']           = isset($data['name']) ? $data['name'] : null;
            $tmp['post_caption']        = isset($data['caption']) ? $data['caption'] : null;
            $tmp['post_description']    = isset($data['description']) ? $data['description'] : null;
            $tmp['post_type']           = $data['type'];
            $tmp['post_source']         = isset($data['application']['name']) ?  $data['application']['name'] : null;
            $tmp['post_status']         = 'new';
            $tmp['user_category']       =  isset($data['from']['category']) ? $data['from']['category'] : null;
            $tmp['source']              = 'facebook';
            $tmp['date_added']          = date("Y-m-d h:i:s");
            $tmp['date_published']      = $data['created_time'];
            $tmp['date_published_ts']   = strtotime($data['created_time']);
            $tmp['post_likes']          = isset($data['likes']) ? count($data['likes']['data']) : 0;
            $tmp['post_comments']       = isset($data['comments']) ? count($data['comments']['data']) : 0;
            $tmp['post_lang']           = 'EN';
            $tmp['user_url']            = null;
            $tmp['post_url']            = 'https://www.facebook.com/'.$data['id'];
            $tmp['user_profile_image']  = null;
            $tmp['user_lang']           = null;
            $tmp['user_location']       = null;
            $tmp['user_followers_count']= 0;
            $tmp['user_friend_count']   = 0;
            $tmp['user_status_count']   = 0;

            $res = array_merge($res,array($tmp));
        }

        if($res){
            return $res;
        } else {
            return false;
        }
    }
}