<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 09/09/13
 * Time: 9:05 PM
 * To change this template use File | Settings | File Templates.
 */
class Post extends Model {

    var $hasOne = array('Stream' => 'Stream');

    public function getById()
    {
        // TODO: Implement getById() method.
    }

    public function fetchPosts($status = 'new'){

        if ($status == 'all'){
            $this->where('post_status','deleted','not');
        } else {
            $this->where('post_status',$status);
        }
        $this->orderBy('date_published_ts','DESC');
        $this->showHasOne();

        $result = $this->search();
        $this->totalPages();

        return $result;
    }

    /**
     * Method to create a custom query
     * to get counts of posts based on their status
     */
    public function getPostCount(){

        $sQl = "SELECT count(`id`) as total, `post_status` as status FROM `".$this->_table."` WHERE `post_status` IN ('new','approved') GROUP BY `post_status`";
        $result = $this->custom($sQl);

        if ($result){
            $summary = array('all'=>0,'new'=>0,'approved'=>0);
            $sum = 0;
            foreach($result as $count){
                $sum += $count[0]['total'];
                $summary[$count['Post']['status']] = $count[0]['total'];
            }
            $summary['all'] = $sum;
            return $summary;
        } else {
            return false;
        }
    }

    public function saveAll($dataArr){

        if (!$dataArr){
            return false;
        }

        foreach($dataArr as $data){

            foreach($data as $field => $value){
                $this->{$field} = $value;
            }
            if (!$this->save()){
                return false;
            }
        }
        return true;
    }

    public function changeStatus($newStatus){
        $this->post_status = $newStatus;
        return $this->save(true);
    }

    public function api_fetchPosts($media=null,$count=10,$offset=0){

        $this->setLimit($count);
        if ($media != null){
            $this->where('source',$media);
        }

        $this->where('post_status','approved');
        $this->orderBy('date_published_ts','DESC');

        $result = $this->search();
        if ($result){
            return $result;
        } else {
            return false;
        }
    }

}
