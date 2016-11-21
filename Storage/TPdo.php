<?php
class TPdo
{
    /**
     * @var \PDO
     */
    protected $pdo_obj;
    public function __construct($config)
    {
        $this->pdo_obj = new \PDO( $config['dsn'], $config['username'], $config['password'],$config['params']);
    }

    protected function parseCondition($condition)
    {
        $condition_str = '';
        if(empty($condition))
        {
            return ' 1 ';
        }
        if(is_array($condition))
        {
            foreach($condition as $k=>$v)
            {
                $condition_str .= " $k='$v' AND";
            }
            $condition_str = substr($condition_str,0,-3);
        }
        else
        {
            $condition_str = $condition;
        }
        return $condition_str;
    }

    public function getRows($table,$fields='',$condition)
    {
        if(empty($fields))
        {
            $fields = '*';
        }
        $condition = $this->parseCondition($condition);
        $sql = "SELECT $fields FROM $table WHERE $condition";
        $stmt = $this->pdo_obj->prepare($sql);
        $ret = $stmt->execute();
        $ret_rows = [];
        while ($row = $stmt->fetch()) {
            $ret_rows[] = $row;
        }
        return $ret_rows;
    }

    public function getPageRows($table, $fields='', $condition='', $page=1,$length=10,$last_count = 0)
    {
        $condition = $this->parseCondition($condition);
        $startIndex=($page-1)*$length;
        $count = $this->getRowsCount($table,$condition);
        if($count === false)
        {
            return false;
        }

        var_dump($count);
        if($last_count > 0 && $count > $last_count)
        {
            $startIndex += $count - $last_count;
        }

        if(empty($fields))
        {
            $fields = '*';
        }

        $sql = 'SELECT '.$fields.' FROM '.$table;
        if (!empty($condtion))
        {
            $sql .= ' WHERE '.$condition;
        }
        if ($startIndex >= 0 && $length > 0)
        {
            $sql .=' LIMIT '.$startIndex.','.$length;
        }


        $stmt = $this->pdo_obj->prepare($sql);
        $ret = $stmt->execute();
        $ret_rows = [];
        while ($row = $stmt->fetch()) {
            $ret_rows[] = $row;
        }
        return [
            'count'=>intval($count),
            'list'=>$ret_rows
        ];
    }


    public function getRowsCount($table, $condtion,$count_field='*')
    {
        $sql = 'SELECT count('.$count_field.') as c FROM '.$table;
        if (!empty($condtion))
        {
            $sql .= ' WHERE '.$condtion;
        }
        $stmt = $this->pdo_obj->prepare($sql);
        $stmt->execute();
        $ret_rows = [];
        while ($row = $stmt->fetch()) {
            $ret_rows[] = $row;
        }
        return ((count($ret_rows)<=0) ? 0 : $ret_rows[0]['c']);
    }

    public function getOneRows($table,$fields='',$condition)
    {
        $rows = self::getRows($table,$fields='',$condition);
        if(isset($rows[0]))
        {
            return $rows[0];
        }
        else
        {
            return [];
        }
    }

    public function update($table, $data, $condition)
    {
        $condition = $this->parseCondition($condition);
        $sql = $this->getUpdateString($table,$data,$condition);
        $stmt = $this->pdo_obj->prepare($sql);
        return $stmt->execute();
    }

    public function add($table,$data)
    {
        $sql = $this->getInsertString($table,$data);

        $stmt = $this->pdo_obj->prepare($sql);
        return $stmt->execute();
    }


    public function getInsertString($table, $data)
    {
        $n_str = '';
        $v_str = '';
        foreach ($data as $k => $v)
        {
            $n_str .= $k.',';
            $v_str .= "'".$v."'".',';
        }
        $n_str = substr($n_str,0,-1);
        $v_str = substr($v_str,0,-1);
        $str = 'INSERT INTO '.$table.' ('.$n_str.')  VALUES ('.$v_str.')';
        return $str;
    }



    public function getUpdateString($table, $data, $condition)
    {
        $str = '';
        foreach ($data as $k => $v)
        {
            $str .= "$k='$v',";
        }
        $str = substr($str,0,-1);
        $sql = 'UPDATE '.$table.' SET '.$str;
        $sql .= ' WHERE '.$condition;
        return $sql;
    }



}