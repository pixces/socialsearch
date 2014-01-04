<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 07/09/13
 * Time: 4:26 PM
 * To change this template use File | Settings | File Templates.
 */ 
class StreamController extends Controller {

    CONST TW_API_URL = 'https://api.twitter.com/1.1/search/tweets.json';
    CONST FB_API_URL = 'https://graph.facebook.com/search';
    CONST GP_API_URL = 'https://plus.google.com/search';

    protected $result_count = 100;
    protected $response = 'json';
    protected $callFrequency = array ('tw'=>3600, 'fb'=>3600, 'gp'=>3600);


    function beforeAction(){
    }

    function afterAction(){
       $this->set_pageTitle('manage streams');
    }

    public function index(){

    }

    /**
     * AJAX Call
     * save form data to database
     * also create Call URLS and add them to database
     */
    public function addStream(){

        $this->doNotRenderHeader = true;

        if ($_POST && $_POST['formAction'] == 'doAddStream'){

            $data = $_POST;

            if (!isset($data['keyword']) || $data['keyword'] == ''){
                echo json_encode(array('status' => 'error', 'message' => 'No search keyword entered in form.' ));
                exit;
            }

            #check for sources
            #create call urls
            $keywordString = ($data['is_phrase'] == 'y') ? '"'.$data['keyword'].'"' : $data['keyword'];

            #$sources
            //twitter
            if ($data['is_twitter'] == 'y'){
                $kwString = $this->_createKwString($data,'twitter');
                $source['tw'] = $kwString;
            }

            //facebook
            if ($data['is_facebook'] == 'y'){
                $kwString = $this->_createKwString($data,'facebook');
                $source['fb'] = $kwString;
            }

            //gplus
            if ($data['is_gplus'] == 'y'){
                $kwString = $this->_createKwString($data,'gplus');
                $source['gp'] = $kwString;
            }

            #set date & status
            $data['created_date']  = date('Y-m-d h:i:s');
            $data['status'] = 'active';


            //save the basic details
            foreach($data as $field => $value){
                $this->Stream->{$field} = $value;
            }

            if ($this->Stream->save()){
                $stream_id = $this->Stream->insert_id;
                $callData['stream_id'] = $stream_id;
                $callData['source'] = $source;

                if ( $this->_saveCallDetails($callData) ){
                    echo json_encode(array('status' => 'success', 'message' => sprintf('Search keyword <b>\'%s\'</b> successfully added.', $data['keyword'])));
                } else {
                    //keywords was saved by
                    echo json_encode(array('status' => 'error', 'message' => sprintf('Search keyword <b>\'%s\'</b> successfully added, but Call Urls not added.', $data['keyword'])));
                }
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Cannot add search keyword to database.'));
            }
        }
        exit;
    }

    public function delete(){
        $this->doNotRenderHeader = true;

        if ($_POST){

            if ($_POST['id']){

                $this->Stream->id = $_POST['id'];
                if ($this->Stream->delete()){
                    echo json_encode(array('status' => 'success', 'message' => sprintf('Search keyword <b>\'%s\'</b> successfully deleted.', $_POST['name'])));
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
           $stream_id = $_POST['id'];
           $oldStatus = $_POST['data'];
           $keyword = $_POST['name'];
           $newStatus = ($oldStatus == 'active') ? 'inactive' : 'active';

           $this->Stream->id = $stream_id;
           if ( $this->Stream->changeStatus($newStatus) ){
               echo json_encode(array('status' => 'success', 'message' => sprintf('Search keyword <b>\'%s\'</b> status updated to <b>\'%s\'</b>', $keyword,$newStatus), 'newstatus' => $newStatus));
           } else {
               echo json_encode(array('status' => 'error', 'message' => 'Cannot update search keyword status.'));
           }
           exit;

        }

    }

    public function streamList(){

        $this->doNotRenderHeader = true;

        #get the list of streams
        $result = $this->Stream->fetchAll();

        if ($result){
            #total keyword count
            $listSize = count($result);

            #get total post count for each stream
            $this->set('count',$listSize);
            $this->set('list',$result);
        } else {
            $this->set('count',0);
        }

        exit;
    }

    private function _getPostCount(){
        $post = new Post();
        $counts = $post->getCountByStream();
    }

    private function _createKwString($data,$source = 'twitter'){

        $keyword = $data['keyword'];
        $is_profile = $data['is_profile'] == 'y' ? true : false;
        $is_phrase = $data['is_phrase'] == 'y' ? true : false;
        $kwString = "";

        switch($source){
            case 'twitter':
                if ($is_profile){
                    $kwString = "from:".$keyword;
                } else if ($is_phrase){
                    $kwString = '"'.$keyword.'"';
                } else {
                    $kwString = $keyword;
                }
                break;
            case 'facebook':
            case 'gplus':
                if ($is_phrase){
                    $kwString = '"'.$keyword.'"';
                } else {
                    $kwString = $keyword;
                }
                break;
        }

        return urlencode($kwString);
    }


    private function _saveCallDetails($data){

        $callObj = new Call();
        $callDetails = array();
        $source = $data['source'];

        $t = 0;
        foreach($source as $media => $kw){

            $callDetails[$t]['stream_id'] = $data['stream_id'];
            $callDetails[$t]['source'] = ($media == 'tw') ? 'twitter' : (($media == 'fb') ? 'facebook' : 'googleplus') ;
            $callDetails[$t]['keyword_string'] = $kw;
            $callDetails[$t]['base_api_url'] = ($media == 'tw') ? self::TW_API_URL : (($media == 'fb') ? self::FB_API_URL : self::GP_API_URL) ;
            $callDetails[$t]['post_count'] = 25;
            $callDetails[$t]['frequency'] = $this->callFrequency[$media];
            $callDetails[$t]['date_added'] = date('Y-m-d h:i:s');
            $t++;
        }

       #save all these to database
       if ( $callObj->saveAll($callDetails) ) {
           return true;
       } else {
           return false;
       }
    }

}
