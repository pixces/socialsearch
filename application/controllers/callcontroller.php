<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 2:04 PM
 *
 * Will run as a cron
 * Check for call to be made
 * DO API Call
 * Fetch JSON Data
 * Parse data
 * Enter parsed data to table
 */
class CallController extends Controller {

    protected $stream_id;


    function beforeAction(){
    }

    function afterAction(){

    }

    public function makeCalls(){
        $this->doNotRenderHeader = true;
        //get the list of all calls to be made
        //i.e., nex call time < =  now
        //also update next call to time+frequency

        $callTime = time();
        $callList = $this->Call->fetchCalls();

        if($callList){

            foreach($callList as $call){
                echo "--- Started making calls ----<br>";
                echo $call['source'].": ".urldecode($call['keyword_string'])."<br>";

                //processing the call
                $process = $this->processCall($call);

                //update the call details if process is successful
                if ($process){
                    $call['last_call_time'] = $callTime;
                    $call['next_call_time'] = $callTime + $call['frequency'];

                    //update the details
                    $this->Call->saveOne($call);
                }
                echo "--- End Calls ----<br>";
            }
        }  else {
            echo "No New call to be initiated<br>";
        }
        exit;
    }


    private function processCall($call){

        //set global stream id to be used in other
        //methods calls
        $this->stream_id = $call['stream_id'];

        //make call to the respective api
        //to get the latest feeds
        $sourceObj = Social_Factory::sourceCall_Factory($call['source']);
        $jsonData = $sourceObj->getFeed($call);

        if ($jsonData){

            //parse this json to get postarray for saving to database
            $dataArray = $this->parseJson($jsonData,$call['source']);

            if ($dataArray){
                //save all these data to file
                if ($this->savePost($dataArray) ){
                    return true;
                }
            }
        }
        return false;
    }

    private function parseJson($json,$type){

        $parserObj = Social_Factory::parser_factory($type);
        $postsArr = $parserObj->parseJson($json);

        if ($postsArr){
            return $postsArr;
        } else {
            echo "No post found in feed after parsing"."<br>";
            return false;
        }
    }


    private function savePost($posts){

        #add other common data to the posts
        if ($posts){
            foreach($posts as &$post){
                $post['stream_id'] = $this->stream_id;
                $post['post_hash'] = md5($this->stream_id."|".$post['post_id']."|".$post['date_published']);
            }

            $postObj = new Post();
            if ( $postObj->saveAll($posts) ){
                echo "All posts saved to database"."<br>";
                return true;
            } else {
                echo "Cannot save posts to database"."<br>";
                return false;
            }
        }
    }





}
