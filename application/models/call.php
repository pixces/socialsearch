<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 1:19 PM
 * To change this template use File | Settings | File Templates.
 */
class Call extends Model {


    public function getById()
    {
        // TODO: Implement getById() method.
    }

    public function saveOne($call){

        foreach($call as $field => $value){
            $this->{$field} = $value;
        }
        parent::save();
    }

    public function saveAll($data){

        foreach($data as $details){
            foreach($details as $field => $value){
                $this->{$field} = $value;
            }
            if ( !parent::save() ){
                return false;
            }
        }
        return true;
    }

    public function fetchCalls(){

        $now = time();
        $table_stream = 'streams';
        $table_calls = $this->_table;

        $sQl = "SELECT `calls`.* from `".$table_calls."` `calls` LEFT JOIN `".$table_stream."` `stream` ON (`calls`.`stream_id` = `stream`.`id`) WHERE `calls`.`next_call_time` <= '".$now."' AND `stream`.`status` = 'active'";

        $result = $this->custom($sQl);
        $list = array();
        if ($result){
            foreach($result as $call){

                $list = array_merge($list,array($call['Call']));
            }
        }
        return $list;
    }

}
