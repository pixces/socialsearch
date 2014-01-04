<?php

class SQLQuery
{
    protected $_dbHandle;
    protected $_result;
    protected $_query;
    protected $_table;
    protected $_describe = array();
    protected $_orderBy;
    protected $_order;
    protected $_extraConditions;
    protected $_hO;
    protected $_hM;
    protected $_hMABTM;
    protected $_page;
    protected $_limit;
    protected $_totalpages;
    protected $_pagination_link;

    /** Connects to database **/
    public function connect($address, $account, $pwd, $name)
    {
        $this->_dbHandle = mysql_connect($address, $account, $pwd);
        if ($this->_dbHandle != 0) {
            if (mysql_select_db($name, $this->_dbHandle)) {
                //echo "connection successsful";
                return 1;
            } else {
                echo "cannot connect";
                echo mysql_errno();
                return 0;
            }
        } else {
            echo "cannot connect 1";
            echo mysql_errno();
            return 0;
        }
    }

    /** Disconnects from database **/
    public function disconnect()
    {
        if (@mysql_close($this->_dbHandle) != 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /** Select Query **/

    /**
     *
     * @param string $field
     * @param string $value
     * @param string $action //is '=', not !=
     */
    public function where($field, $value,$action='is')
    {
        if ($action == 'is'){
            $operator = '=';
        } else if ($action = 'not'){
            $operator = '!=';
        }
        $this->_extraConditions .= '`' . $this->_model . '`.`' . $field . '` '.$operator.' \'' . mysql_real_escape_string($value) . '\' AND ';
    }

    public function find_in_set($value,$field){
        $this->_extraConditions .= 'FIND_IN_SET('.$value.',`' . $this->_model . '`.`' . $field . '`) AND ';
    }

    public function setOr(array $data)
    {
        $whrCls = array();
        foreach($data as $field => $value){
            $whrCls[] = '`'.$this->_model.'`.`'.$field.'` = \'' . mysql_real_escape_string($value) . '\'';
        }

        if($whrCls){
            $this->_extraConditions .= '('.implode(" OR ",$whrCls).') AND ';
        }
    }


    public function like($field, $value)
    {
        $this->_extraConditions .= '`' . $this->_model . '`.`' . $field . '` LIKE \'%' . mysql_real_escape_string($value) . '%\' AND ';
    }

    public function in($check, array $array)
    {
        $value = "'".implode("','",$array)."'";
        $this->_extraConditions .= ' ' . $check . ' in (' . $value . ') AND ';
    }

    public function showHasOne()
    {
        $this->_hO = 1;
    }

    public function showHasMany()
    {
        $this->_hM = 1;
    }

    public function showHMABTM()
    {
        $this->_hMABTM = 1;
    }

    public function setLimit($limit)
    {
        $this->_limit = $limit;
    }

    public function setPage($page)
    {
        $this->_page = $page;
    }

    public function setTotalPage($count)
    {
        $this->_totalpages = $count;
    }

    public function getTotalPage()
    {
        return $this->_totalpages;
    }

    public function setPaginationLink($link)
    {
        $this->_pagination_link = $link;
    }

    public function getPaginationLink()
    {
        if (is_null($this->_pagination_link)) {
            $this->createPaginationLink();
        }
        return $this->_pagination_link;
    }

    public function orderBy($orderBy, $order = 'ASC')
    {
        $this->_orderBy = $orderBy;
        $this->_order = $order;

    }

    public function search()
    {

        global $inflect;

        $from = '`' . $this->_table . '` as `' . $this->_model . '` ';
        $conditions = '\'1\'=\'1\' AND ';
        $conditionsChild = '';
        $fromChild = '';

        if ($this->_hO == 1 && isset($this->hasOne)) {

            foreach ($this->hasOne as $alias => $model) {
                $table = strtolower($inflect->pluralize($model));
                $singularAlias = strtolower($alias);
                $from .= 'LEFT JOIN `' . $table . '` as `' . $alias . '` ';
                $from .= 'ON `' . $this->_model . '`.`' . $singularAlias . '_id` = `' . $alias . '`.`id`  ';
            }
        }

        if ($this->id) {
            $conditions .= '`' . $this->_model . '`.`id` = \'' . mysql_real_escape_string($this->id) . '\' AND ';
        }

        if ($this->_extraConditions) {
            $conditions .= $this->_extraConditions;
        }

        $conditions = substr($conditions, 0, -4);

        if (isset($this->_orderBy)) {
            if($this->_orderBy == 'random'){
                $conditions .= ' ORDER BY  RAND() ';
            } else {
                $conditions .= ' ORDER BY `' . $this->_model . '`.`' . $this->_orderBy . '` ' . $this->_order;
            }
        }

        if (isset($this->_limit) && !isset($this->_page)) {
            $this->_page = 1;
        }

        if (isset($this->_page)) {

            $offset = ($this->_page - 1) * $this->_limit;
            $conditions .= ' LIMIT ' . $this->_limit . ' OFFSET ' . $offset;
        }

        $this->_query = 'SELECT * FROM ' . $from . ' WHERE ' . $conditions;

        //echo $this->_query;

        $this->_result = mysql_query($this->_query, $this->_dbHandle);

        $result = array();
        $table = array();
        $field = array();
        $tempResults = array();

        $numOfFields = mysql_num_fields($this->_result);
        for ($i = 0; $i < $numOfFields; ++$i) {
            array_push($table, mysql_field_table($this->_result, $i));
            array_push($field, mysql_field_name($this->_result, $i));
        }
        if (mysql_num_rows($this->_result) > 0) {
            while ($row = mysql_fetch_row($this->_result)) {
                for ($i = 0; $i < $numOfFields; ++$i) {
                    $tempResults[$table[$i]][$field[$i]] = $row[$i];
                }

                if ($this->_hM == 1 && isset($this->hasMany)) {
                    foreach ($this->hasMany as $aliasChild => $modelChild) {

                        $queryChild = '';
                        $conditionsChild = '';
                        $fromChild = '';

                        $tableChild = strtolower($inflect->pluralize($modelChild));
                        $pluralAliasChild = strtolower($inflect->pluralize($aliasChild));
                        $singularAliasChild = strtolower($aliasChild);

                        $fromChild .= '`' . $tableChild . '` as `' . $aliasChild . '`';

                        $conditionsChild .= '`' . $aliasChild . '`.`' . strtolower($this->_model) . '_id` = \'' . $tempResults[$this->_model]['id'] . '\'';

                        $queryChild = 'SELECT * FROM ' . $fromChild . ' WHERE ' . $conditionsChild;
                        #echo $queryChild;
                        $resultChild = mysql_query($queryChild, $this->_dbHandle);

                        $tableChild = array();
                        $fieldChild = array();
                        $tempResultsChild = array();
                        $resultsChild = array();

                        if (mysql_num_rows($resultChild) > 0) {
                            $numOfFieldsChild = mysql_num_fields($resultChild);
                            for ($j = 0; $j < $numOfFieldsChild; ++$j) {
                                array_push($tableChild, mysql_field_table($resultChild, $j));
                                array_push($fieldChild, mysql_field_name($resultChild, $j));
                            }

                            while ($rowChild = mysql_fetch_row($resultChild)) {
                                for ($j = 0; $j < $numOfFieldsChild; ++$j) {
                                    $tempResultsChild[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
                                }
                                array_push($resultsChild, $tempResultsChild);
                            }
                        }

                        $tempResults[$aliasChild] = $resultsChild;

                        mysql_free_result($resultChild);
                    }
                }


                if ($this->_hMABTM == 1 && isset($this->hasManyAndBelongsToMany)) {
                    foreach ($this->hasManyAndBelongsToMany as $aliasChild => $tableChild) {
                        $queryChild = '';
                        $conditionsChild = '';
                        $fromChild = '';

                        $tableChild = strtolower($inflect->pluralize($tableChild));
                        $pluralAliasChild = strtolower($inflect->pluralize($aliasChild));
                        $singularAliasChild = strtolower($aliasChild);

                        $sortTables = array($this->_table, $pluralAliasChild);
                        sort($sortTables);
                        $joinTable = implode('_', $sortTables);

                        $fromChild .= '`' . $tableChild . '` as `' . $aliasChild . '`,';
                        $fromChild .= '`' . $joinTable . '`,';

                        $conditionsChild .= '`' . $joinTable . '`.`' . $singularAliasChild . '_id` = `' . $aliasChild . '`.`id` AND ';
                        $conditionsChild .= '`' . $joinTable . '`.`' . strtolower($this->_model) . '_id` = \'' . $tempResults[$this->_model]['id'] . '\'';
                        $fromChild = substr($fromChild, 0, -1);

                        $queryChild = 'SELECT * FROM ' . $fromChild . ' WHERE ' . $conditionsChild;
                        #echo '<!--'.$queryChild.'-->';
                        $resultChild = mysql_query($queryChild, $this->_dbHandle);

                        $tableChild = array();
                        $fieldChild = array();
                        $tempResultsChild = array();
                        $resultsChild = array();

                        if (mysql_num_rows($resultChild) > 0) {
                            $numOfFieldsChild = mysql_num_fields($resultChild);
                            for ($j = 0; $j < $numOfFieldsChild; ++$j) {
                                array_push($tableChild, mysql_field_table($resultChild, $j));
                                array_push($fieldChild, mysql_field_name($resultChild, $j));
                            }

                            while ($rowChild = mysql_fetch_row($resultChild)) {
                                for ($j = 0; $j < $numOfFieldsChild; ++$j) {
                                    $tempResultsChild[$tableChild[$j]][$fieldChild[$j]] = $rowChild[$j];
                                }
                                array_push($resultsChild, $tempResultsChild);
                            }
                        }

                        $tempResults[$aliasChild] = $resultsChild;
                        mysql_free_result($resultChild);
                    }
                }

                array_push($result, $tempResults);
            }

            if (mysql_num_rows($this->_result) == 1 && $this->id != null) {
                mysql_free_result($this->_result);
                $this->clear();
                return ($result[0]);
            } else {
                mysql_free_result($this->_result);
                $this->clear();
                return ($result);
            }
        } else {
            mysql_free_result($this->_result);
            $this->clear();
            return $result;
        }

    }

    public function fetchOne()
    {

        global $inflect;

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

        $this->_query = 'SELECT * FROM ' . $from . ' WHERE ' . $conditions;
        //echo '<!--'.$this->_query.'-->';
        $this->_result = mysql_query($this->_query, $this->_dbHandle);

        $result = array();
        $table = array();
        $field = array();
        $tempResults = array();
        $numOfFields = mysql_num_fields($this->_result);

        while ($rows = mysql_fetch_assoc($this->_result)) {
            foreach ($this->_describe as $k => $v) {
                $this->{$v} = $rows[$v];
            }
        }

    }

    /** Custom SQL Query **/
    public function custom($query)
    {

        global $inflect;
        $conditions = '';

        if (isset($this->_limit) && !isset($this->_page)) {
            $this->_page = 1;
        }

        if (isset($this->_page)) {
            $offset = ($this->_page - 1) * $this->_limit;
            //$conditions .= ' LIMIT '.$offset.','.$this->_limit.' OFFSET '.$offset ;
            $conditions .= ' LIMIT ' . $this->_limit . ' OFFSET ' . $offset;
        }

        $this->_query = $query . $conditions;

        $this->_result = mysql_query($this->_query, $this->_dbHandle);

        $result = array();
        $table = array();
        $field = array();
        $tempResults = array();

        if (substr_count(strtoupper($query), "SELECT") > 0) {

            if (mysql_num_rows($this->_result) > 0) {
                $numOfFields = mysql_num_fields($this->_result);
                for ($i = 0; $i < $numOfFields; ++$i) {
                    if (mysql_field_table($this->_result, $i) != '') {
                        array_push($table, mysql_field_table($this->_result, $i));
                    } else {
                        array_push($table, '0');
                    }
                    array_push($field, mysql_field_name($this->_result, $i));
                }

                while ($row = mysql_fetch_row($this->_result)) {
                    for ($i = 0; $i < $numOfFields; ++$i) {
                        $table[$i] = ucfirst($inflect->singularize($table[$i]));
                        $tempResults[$table[$i]][$field[$i]] = $row[$i];
                    }
                    array_push($result, $tempResults);
                }
            }
            mysql_free_result($this->_result);
        } else {
            $this->clear();
            if ($this->_result == 0) {
                return -1;
            } else {
                return true;
            }
        }
        $this->clear();
        return $result;
    }

    /** Describes a Table **/

    protected function _describe()
    {

        if (!$this->_describe) {
            $this->_describe = array();
            $query = 'DESCRIBE ' . $this->_table;
            $this->_result = mysql_query($query, $this->_dbHandle);

            while ($row = mysql_fetch_row($this->_result)) {
                array_push($this->_describe, $row[0]);
            }
            mysql_free_result($this->_result);
        }

        foreach ($this->_describe as $field) {
            $this->$field = null;
        }
    }

    /** Delete an Object **/

    public function delete()
    {
        if ($this->id) {
            $query = 'DELETE FROM ' . $this->_table . ' WHERE `id`=\'' . mysql_real_escape_string($this->id) . '\'';

            $this->_result = mysql_query($query, $this->_dbHandle);
            $this->clear();
            if ($this->_result == 0) {
                /** Error Generation **/
                return -1;
            } else {
                return true;
            }
        } else {
            /** Error Generation **/
            return -1;
        }

    }

    /** Saves an Object i.e. Updates/Inserts Query **/
    public function save($doNoUpdateBlanks=false)
    {
        $query = '';
        if (isset($this->id)) {
            $updates = '';

            if ($doNoUpdateBlanks){
                //remove all fields which are blank as to preserver their values
                //update only fields who have values set
                foreach ($this->_describe as $field) {
                    if ($this->$field) {
                        $updates .= '`'.$field.'` = \''.mysql_real_escape_string($this->$field).'\',';
                    }
                }
            } else {
                //force update all fields
                //esp. during edits
                foreach ($this->_describe as $field) {
                    if (!in_array($field,array('date_added','date_modified'))) {
                        $updates .= '`' . $field . '` = \'' . mysql_real_escape_string($this->$field) . '\',';
                    }
                }
            }

            $updates = substr($updates, 0, -1);
            $query = 'UPDATE ' . $this->_table . ' SET ' . $updates . ' WHERE `id`=\'' . mysql_real_escape_string($this->id) . '\'';
        } else {
            $fields = '';
            $values = '';

            foreach ($this->_describe as $field) {
                if ($this->$field) {
                    $fields .= '`' . $field . '`,';
                    $values .= '\'' . mysql_real_escape_string($this->$field) . '\',';
                }
            }
            $values = substr($values, 0, -1);
            $fields = substr($fields, 0, -1);

            $query = 'INSERT INTO ' . $this->_table . ' (' . $fields . ') VALUES (' . $values . ')';
        }

        //echo $query;

        $this->_result = mysql_query($query, $this->_dbHandle);
        $this->clear();
        if ($this->_result == 0) {
            /** Error Generation **/
            return false;
        } else {
            $this->insert_id = mysql_insert_id($this->_dbHandle);
            return true;
        }
    }

    /** Clear All Variables **/

    public function clear()
    {
        foreach ($this->_describe as $field) {
            $this->$field = null;
        }

        $this->_orderby = null;
        $this->_extraConditions = null;
        $this->_hO = null;
        $this->_hM = null;
        $this->_hMABTM = null;
        //$this->_page = null;
        $this->_order = null;
    }

    /** Pagination Count **/
    protected function totalPages()
    {

        if ($this->_query && $this->_limit) {
            $pattern = '/SELECT (.*?) FROM (.*)LIMIT(.*)/i';
            $replacement = 'SELECT COUNT(*) FROM $2';
            $countQuery = preg_replace($pattern, $replacement, $this->_query);
            //echo "Count Query ".$countQuery; exit;
            $this->_result = mysql_query($countQuery, $this->_dbHandle);
            $count = mysql_fetch_row($this->_result);
            $totalPages = ceil($count[0] / $this->_limit);
            $this->_totalpages = $totalPages;
        } else {
            /* Error Generation Code Here */
            return false;
        }
    }

    /**
     * TODO: Update this method to give pager info without HTML
     * @return bool
     */

    protected function createPaginationLink()
    {

        if (!$this->_totalpages) {
            $this->totalPages();
        }

        if ($this->_totalpages <= 1) {
            return false;
        }

        $link = '<ul id="pagination-clean">';

        #addl page Link
        /*
        //$prev_page = ($this->_page -1 > 1) ? $this->_page - 1 : 1;
        //$next_page = ($this->_page + 1 >= $this->_totalpages) ? $this->_totalpages : $this->_page + 1;

        if ($this->_page > 1){
            $link .= '<li class="previous" id="page-'.$prev_page.'">«Previous</li>';
        } else {
            $link .= '<li class="previous off" id="page-'.$prev_page.'">«Previous</li>';
        }*/

        #loop over all the pages
        for ($i = 1; $i <= $this->_totalpages; $i++) {
            if ($this->_page == $i) {
                $link .= '<li class="active" id="page-' . $i . '">' . $i . '</li>';
            } else {
                $link .= '<li class="" id="page-' . $i . '">' . $i . '</li>';
            }
        }

        #create next link
        /*if ($this->_page >= $this->_totalpages){
                $link .= '<li class="next off" id="page-'.$next_page.'">Next »</li>';
        } else {

            $link .= '<li class="next" id="page-'.$next_page.'">Next »</li>';
        }*/
        $link .= "</ul>";

        $this->setPaginationLink($link);
    }


    /** Get error string **/
    function getError()
    {
        return mysql_error($this->_dbHandle);
    }
}