<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 07/09/13
 * Time: 4:29 PM
 * To change this template use File | Settings | File Templates.
 */ 
class Stream extends Model {

    public function getById()
    {
        // TODO: Implement getById() method.
    }


    public function fetchAll(){
        $this->orderBy('id','DESC');
        $this->where('status','deleted','not');
        $result = $this->search();
        return $result;
    }

    public function changeStatus($status){

        $this->status = $status;

        return $this->save(true);





    }


}
