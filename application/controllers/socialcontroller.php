<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 25/09/13
 * Time: 8:39 PM
 * To change this template use File | Settings | File Templates.
 */ 
class SocialController extends Controller {

    private $fb = null;

    public function beforeAction(){
        $this->doNotRenderHeader = true;
    }

    public function index(){

        $user = $this->fb_validate_user();

        if ($user){
            $this->set('logout_url',$logoutUrl = $this->getFacebook()->getLogoutUrl());
            $this->set('media',$_SESSION['media']);
            $this->set('user',$_SESSION[$_SESSION['media']]);
        }
    }

    function getFacebook(){
        if (is_null($this->fb)){
            $this->fb = new Facebook(array('appId'=>FB_APP_ID,'secret'=>FB_SECRET_KEY));
        }
        return $this->fb;
    }

    function pingback(){

        //check back to see if the user is loggedin
        $user = $this->fb_validate_user();

        if ($user){
            echo json_encode($_SESSION);
            exit;
        } else {
            echo json_encode(array('response'=>'false'));
        }

    }

    function login_facebook(){
        //Checking login status

        $user = $this->fb_validate_user();

        if ($user) {
            try {
                //Proceed knowing you have a logged in user who's authenticated.
                $user_profile = $this->getFacebook()->api('/me');

                //print_r($user_profile);
                $_SESSION['media'] = 'facebook';
                $_SESSION['facebook']['user_id'] = $user_profile['id'];
                $_SESSION['facebook']['user_name'] = $user_profile['name'];
                $_SESSION['facebook']['appId'] = FB_APP_ID;
                $_SESSION['facebook']['image'] = "https://graph.facebook.com/".$user_profile['id']."/picture";

                //close this page now
                echo "<script>javascript:window.close(); </script>";
                exit;

            } catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }

            //get the logout url

        } else {
            //redirect to the login
            $params['scope'] = array('read_insights','read_stream','publish_actions','publish_stream');
            $loginUrl = $this->getFacebook()->getLoginUrl($params);

            if ($loginUrl){
                header("location:".$loginUrl);
                exit;
            }
        }
    }

    public function logout_facebook(){

        $this->doNotRenderHeader = true;

        $user = $this->fb_validate_user();
        if ($user){
            $logoutUrl = $this->getFacebook()->getLogoutUrl();

            //unset the data
            $key = 'fb_'.FB_APP_ID;
            unset($_SESSION['media']);
            unset($_SESSION['facebook']);
            unset($_SESSION[$key."_access_token"]);
            unset($_SESSION[$key."_user_id"]);
            unset($_SESSION[$key."_code"]);

            header("location: ".$logoutUrl);
            exit;

        }
        echo "<script>javascript:window.close(); </script>";
        exit;

    }

    private function fb_validate_user(){

        $user = $this->getFacebook()->getUser();

        if ($user){
            return $user;
        } else {
            return false;
        }
    }

}
