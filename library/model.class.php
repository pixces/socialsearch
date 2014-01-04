<?php
Abstract class Model extends SQLQuery
{
    protected $_model;
    protected $limit;

    function __construct()
    {

        global $inflect;

        $this->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $this->_limit = $this->limit;
        $this->_model = get_class($this);
        $this->_table = strtolower($inflect->pluralize($this->_model));
        if (!isset($this->abstract)) {
            $this->_describe();
        }
    }

    function __destruct()
    {
    }

    public function setId($id){
        $this->id = $id;
    }

    public abstract function getById();

    public function getByField($field, $value){

        if (!$field || !$value) { return false; }
        $this->where($field, $value);
        $result = $this->search();
        if ($result){
            return $result[0];
        } else {
            return false;
        }
    }

    /**
     * Method will group posts based on
     * total count, published, private and draft
     * Get total counts
     */
    function getCounts()
    {
        $counts = array('total'=>0);
        $total = $this->custom('select count(*) as total from `' . $this->_table . '`');
        $status = $this->custom('select count(*) as total, status from `' . $this->_table . '` group by status ');

        if ($total) {
            $counts['total'] = $total[0][0]['total'];

            foreach ($status as $stat) {
                $counts[strtolower($stat[$this->_model]['status'])] = $stat[0]['total'];
            }
            return $counts;
        } else {
            return array('total' => 0);
        }
    }
}
