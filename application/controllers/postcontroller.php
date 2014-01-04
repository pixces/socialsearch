<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 12:35 AM
 * To change this template use File | Settings | File Templates.
 */ 
class PostController extends Controller {

    protected $_maxLimit = 25;
    protected $_currentPage = 1;

    function beforeAction(){
    }

    function afterAction(){
        $this->set_pageTitle('Moderate Posts');
    }

    public function index(){

        #by default get all posts
        #other options
        #   - all new
        #   - all approved

        #default display only new posts
        $statusList = array('new','approved','rejected','all');
        $status = 'new';

        if (func_num_args()){
            $arg = func_get_args();
            if (in_array(func_get_arg(0), $statusList)){
                $status = $arg[0];
                if ($arg[1]){
                    $this->_currentPage = $arg[1];
                }
            } else {
                $this->_currentPage = $arg[0];
            }
        }



        #get the count of all post status
        #not including the deleted posts
        $post_summary = $this->Post->getPostCount();

        if ($post_summary['all'] > 0){
            //fetch data only if there are posts
            $this->Post->setLimit($this->_maxLimit);
            $this->Post->setPage($this->_currentPage);

            //get the post for the page;
            $postList = $this->Post->fetchPosts($status);

            //get total posts
            $totalPages = $this->Post->getTotalPage();

            #map the pagination values
            $pager = array(
                'current'       => $this->_currentPage,
                'totalPages'    => $totalPages,
                'min_offset'    => (($this->_currentPage - 1) * $this->_maxLimit) + 1,
                'max_offset'    => $this->_currentPage * $this->_maxLimit,
                'max_post'      => $this->_maxLimit * $totalPages,
                'prev'          => ($this->_currentPage - 1) <= 0 ? 1 : $this->_currentPage - 1,
                'next'          => ($this->_currentPage + 1 > $totalPages) ? $totalPages : $this->_currentPage + 1
            );
            $this->set('current_status',$status);
            $this->set('pager',$pager);
            $this->set('post_summary',$post_summary);
            $this->set('postList',$postList);
        } else {
            $this->set('post_summary', 0);
        }
    }

    public function delete(){
        $this->doNotRenderHeader = true;

        if ($_POST){

            if ($_POST['id']){

                $this->Post->id = $_POST['id'];
                if ($this->Post->delete()){
                    echo json_encode(array('status' => 'success', 'message' => sprintf('Post successfully deleted.')));
                    exit;
                }
            } else {
                //error no id present
                echo json_encode(array('status' => 'error', 'message' => 'Cannot delete search keyword.'));
            }
        }
        exit;
    }


    public function change_status(){

        $this->doNotRenderHeader = true;

        if ($_POST && $_POST['id'] && $_POST['data']){

            $post_id = $_POST['id'];
            $oldStatus = $_POST['data'];
            $newStatus = ( $oldStatus == 'new' || $oldStatus == 'rejected' ) ? 'approved' : 'rejected';

            $this->Post->id = $post_id;

            if ( $this->Post->changeStatus($newStatus) ){
                echo json_encode(array('status' => 'success', 'message' => sprintf('Post status updated to <b>\'%s\'</b>', $newStatus), 'newstatus' => $newStatus));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Cannot update search keyword status.'));
            }
            exit;

        }
    }

    public function api_post_get(){

        $this->doNotRenderHeader = true;

        if ($this->_method != 'GET'){
            $this->set('ERROR','Only accepts Get request');
        }

        $media = isset($this->_request['media']) ? $this->_request['media'] : null;
        $count = isset($this->_request['count']) ? $this->_request['count'] : 10;
        //$offset = isset($this->_request['offset']) ? $this->_request['offset'] : 0;
        $callback = isset($this->_request['callback']) ? $this->_request['callback'] : null;

        #todo: make offset to work
        #not getting considered now;

        $result = $this->Post->api_fetchPosts($media,$count);

        if ($result){
            if ($result){
                $posts = array();
                $t=0;
                foreach($result as $post){
                    $posts = array_merge($posts,array($post['Post']));
                    $t++;
                }
            }
            if (!is_null($callback)){
                $this->set('callback',$callback);
            }
            $this->set('data',$posts);
        } else {
            $this->set('data','No posts found for the query');
        }
    }

    /**
     * Method to publish post from the front end
     * it will publish post to the Social Media
     * also save the same to the database
     */
    public function publish(){
        $this->doNotRenderHeader = true;

        if (!$_POST){
            echo json_encode(array('status' => 'error', 'message' => 'Only POST Method allowed for Publish.'));
            exit;
        }

        $media = $_POST['media'];
        $post_text = $_POST['post_text'];
        $user_id = $_POST['user_id'];
        $user_name = $_POST['user_name'];

        //prepare the default data_set
        $data['stream_id'] = -1;
        $data['source'] = $_POST['media'];
        $data['date_published'] = date('Y-m-dTh:i:s');
        $data['date_published_ts'] = time();
        $data['date_added'] = date('c',$data['date_published_ts']);
        $data['post_text'] = $_POST['post_text'];
        $data['post_lang'] = 'EN';
        $data['post_url'] = 'https://www.facebook.com/'.$data['post_id'];
        $data['post_type'] = 'status';
        $data['user_name'] = $_POST['user_name'];
        $data['user_id'] = $_POST['user_id'];
        $data['post_status'] = 'new';

        $fb = new Facebook(array('appId'=>FB_APP_ID,'secret'=>FB_SECRET_KEY));
        $user = $fb->getUser();
        if (!$user){
            echo json_encode(array('status' => 'error', 'message' => 'The requested user is logged out'));
        }

        //$response = $fb->api('/me/permissions');
        //print_r($response);
        //exit;

        if ($_SESSION['fb_'.FB_APP_ID."_user_id"] == $_POST['user_id'] ){

            $path = "/".$_POST['user_id']."/feed";
            $params = array('message'=>$_POST['post_text']);

            //send this post for publishing
            try {
                $response = $fb->api($path,'POST',$params);
                if(!$response){
                    echo json_encode(array('status' => 'error', 'message' => 'Some error while posting.'));
                    exit;
                }

                $post_id = $response['id'];

                //add additional params to the data
                $data['post_id'] = $post_id;
                $data['post_hash'] = md5($data['stream_id']."|".$data['post_id']."|".$data['date_published']);

                //prepare the data to save to database
                foreach($data as $field => $value){
                    $this->Post->{$field} = $value;
                }

                if($this->Post->save()){
                    echo json_encode(array('status' => 'success', 'message' => "Post Published. Will be displayed after moderation."));
                    exit;
                } else {
                    echo json_encode(array('status' => 'error', 'message' => "Post Published. Unable to save to database."));
                    exit;
                }
            } catch (FacebookApiException $e) {
                echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
                exit;
            }

        }
    }

    /*
    public function test_api(){

        //$this->doNotRenderHeader = true;
    }*/

}
