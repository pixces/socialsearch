<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 25/09/13
 * Time: 1:03 AM
 * To change this template use File | Settings | File Templates.
 */ 
class TestController extends Controller {


    public function beforeAction(){
        $this->doNotRenderHeader = true;
    }

    public function afterAction(){

    }


    public function index(){

        $facebook = new Facebook(array('appId'=>FB_APP_ID,'secret'=>FB_SECRET_KEY));

        $details = $facebook->api('/search?q=incredibleindia&type=post&limit=25');
        print_r($details);

        //?fields=id,name,link,picture.type(small),last_name,first_name,gender,username
        //$user = $facebook->api('/510049735697008');
        //$user = $facebook->api('/510049735697008/picture');
        //print_r($user);

    }




}
