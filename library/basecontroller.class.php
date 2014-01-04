<?php
/**
 * This class is for the functionality of the content link.
 * Using this class you can List, Add, Edit, Delete site links.
 */

class BaseController
{

    protected $_result;
    protected $_query;
    protected $_table;

    protected $_describe = array();

    protected $_orderBy;
    protected $_order;
    protected $_extraConditions;
    protected $_page;
    protected $_limit;
    protected $data;
    protected $_totalCount = 0;


    function where($field, $value)
    {
        $this->_extraConditions .= '`' . $this->_model . '`.`' . $field . '` = \'' . mysql_real_escape_string($value) . '\' AND ';
    }

    function like($field, $value)
    {
        $this->_extraConditions .= '`' . $this->_model . '`.`' . $field . '` LIKE \'%' . mysql_real_escape_string($value) . '%\' AND ';
    }

    function setLimit($limit)
    {
        $this->_limit = $limit;
    }

    function setPage($page)
    {
        $this->_page = $page;
    }

    function orderBy($orderBy, $order = 'ASC')
    {
        $this->_orderBy = $orderBy;
        $this->_order = $order;
    }

    /** Describes a Table **/
    protected function _describe()
    {
        global $db;

        $this->_describe = array();

        echo $query = 'DESCRIBE ' . $this->_table;

        $this->_result = $db->get_results($query);
        if ($this->_result) {

            foreach ($this->_result as $row) {

                array_push($this->_describe, $row->Field);
            }
        }

        foreach ($this->_describe as $field) {
            $this->data[$field] = null;
        }
    }


    /**
     * Fetch object list
     */
    function fetch()
    {

        global $db;

        $from = '`' . $this->_table . '` as `' . $this->_model . '` ';
        $conditions = '\'1\'=\'1\' AND ';
        $conditionsChild = '';
        $fromChild = '';

        if ($this->id) {
            $conditions .= '`' . $this->_model . '`.`id` = \'' . mysql_real_escape_string($this->id) . '\' AND ';
        }

        if ($this->_extraConditions) {
            $conditions .= $this->_extraConditions;
        }

        $conditions = substr($conditions, 0, -4);

        if (isset($this->_orderBy)) {
            $conditions .= ' ORDER BY `' . $this->_model . '`.`' . $this->_orderBy . '` ' . $this->_order;
        }

        if (isset($this->_page)) {
            $offset = ($this->_page - 1) * $this->_limit;
            $conditions .= ' LIMIT ' . $this->_limit . ' OFFSET ' . $offset;
        }

        $this->_query = 'SELECT * FROM ' . $from . ' WHERE ' . $conditions;

        $this->_result = $db->get_results($this->_query);

        if ($this->_result) {
            return $this->_result;
        } else {
            $this->clear();
            return false;
        }
    }


    /** Delete an Object **/
    function delete()
    {
        global $db;

        if ($this->id) {
            $query = 'DELETE FROM ' . $this->_table . ' WHERE `id`=\'' . mysql_real_escape_string($this->id) . '\'';
            $this->_result = $db->query($query);
            $this->clear();
            if ($this->_result == 0) {
                /** Error Generation **/
                return false;
            } else {
                return true;
            }
        } else {
            /** Error Generation **/
            return false;
        }

    }

    /** Saves an Object i.e. Updates/Inserts Query **/
    function save()
    {

        $query = '';
        global $db;

        if (isset($this->id)) {
            $updates = '';
            foreach ($this->_describe as $field) {
                if (isset($this->data[$field])) {
                    $updates .= '`' . $field . '` = \'' . mysql_real_escape_string($this->data[$field]) . '\',';
                }
            }

            $updates = substr($updates, 0, -1);

            $query = 'UPDATE ' . $this->_table . ' SET ' . $updates . ' WHERE `id`=\'' . mysql_real_escape_string($this->id) . '\'';
        } else {

            $fields = '';
            $values = '';
            foreach ($this->_describe as $field) {
                if ($this->data[$field]) {
                    $fields .= '`' . $field . '`,';
                    $values .= '\'' . mysql_real_escape_string($this->data[$field]) . '\',';
                }
            }
            $values = substr($values, 0, -1);
            $fields = substr($fields, 0, -1);

            $query = 'INSERT INTO ' . $this->_table . ' (' . $fields . ') VALUES (' . $values . ')';
        }

        $this->_result = $db->query($query);
        $this->clear();
        if ($this->_result == 0) {
            /** Error Generation **/
            print_r($this->_result);
            return false;
        } else {
            $this->id = $db->insert_id;
            return true;
        }
    }

    /** Clear All Variables **/

    function clear()
    {
        foreach ($this->_describe as $field) {
            $this->$field = null;
        }

        $this->_orderby = null;
        $this->_extraConditions = null;
        $this->_page = null;
        $this->_order = null;
    }

    /** Pagination Count **/
    function totalPages()
    {
        if ($this->_query && $this->_limit) {
            $pattern = '/SELECT (.*?) FROM (.*)LIMIT(.*)/i';
            $replacement = 'SELECT COUNT(*) FROM $2';
            $countQuery = preg_replace($pattern, $replacement, $this->_query);
            $this->_result = $db->get_var($countQuery);
            $count = $this->_result;
            $this->_totalCount = $count;
            $totalPages = ceil($count[0] / $this->_limit);
            return $totalPages;
        } else {
            /* Error Generation Code Here */
            return -1;
        }
    }


    function query($sql)
    {
        global $db;
        return $db->query($sql);
    }

    /** Get error string **/
    function getError()
    {
        return mysql_error($this->_dbHandle);
    }


    /**
     * End of the Class.
     */
}