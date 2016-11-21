<?php
class Model
{
    public $table;

    /**
     * @var \Mysql
     */
    public $db;

    public $pri_key;

    public function __construct($table,$db_key,$pri_key='')
    {
        $this->table = $table;
        $this->db = \S::getMysql($db_key);
        $this->pri_key = $pri_key;
    }


    /**
     * 获取所有记录
     * @param $fields   //所需字段
     * @param $condition    //查询条件
     * @param $options    //查询选项
     * @return bool
     */
    public function getAllRows($fields='', $condition='', $options=[],$startIndex=0, $length=0)
    {
        $condition = $this->parseCondition($condition);
        $options_sql = $this->parseOptions($options);
        $condition .= $options_sql;
        return $this->db->getRows($this->table,$fields, $condition, $startIndex, $length);
    }

    public function getInsertId()
    {
        return $this->db->getInsertId();
    }

    /**
     * 根据主键,获取单个数据
     *
     * @param $id
     * @return bool
     */
    public function getDataByKey($id,$fields='')
    {
        $condition = $this->parseCondition([$this->pri_key=>$id]);
        return $this->db->getOneRows($this->table,$fields,$condition);
    }


    /**
     * 新增数据
     *
     * @param $data
     * @param bool $return_last_id  如果是true,则返回自增id
     * @return bool|int|string|\   //正确返回true
     */
    public function add($data,$return_last_id=false)
    {
        $ret = $this->db->insert($this->table,$data);
        if($ret===false)
        {
            return false;
        }
        if($return_last_id)
        {
            return $this->db->getInsertId();
        }
        return $ret;
    }


    /**
     * 更新一个数据
     *
     * @param $data
     * @param $condition
     * @return bool
     */
    public function update($data,$condition)
    {
        $condition = $this->parseCondition($condition);
        return $this->db->update($this->table,$data,$condition);
    }


    /**
     * 删除一个数据
     * @param $condition
     * @return bool
     */
    public function remove($condition)
    {
        $condition = $this->parseCondition($condition);
        return $this->db->remove($this->table,$condition);
    }

    /**
     * 分页获取记录
     *
     * @param $field    //所需字段
     * @param $condition    //查询条件
     * @param $options      //查询选项
     * @param int $page         //页面
     * @param int $page_size        //每页个数
     * @param int $last_count       //最后一条数据
     * @return bool
     */
    public function getPageRows($fields='', $condition='', $options=[],$page=1,$length=10,$last_count = 0)
    {
        $condition = $this->parseCondition($condition);
        $options_sql = $this->parseOptions($options);
        $condition .= $options_sql;
        return $this->db->getPageRows($this->table, $fields, $condition, $page, $length, $last_count);
    }

    /**
     * 获取单条数据
     * @param $fields
     * @param $condition
     * @return bool
     */
    public function getOneRow($fields='', $condition='')
    {
        $condition = $this->parseCondition($condition);
        return $this->db->getOneRows($this->table,$fields,$condition);
    }


    public function getRowCount($condition,$count_field='*')
    {
        $condition = $this->parseCondition($condition);
        return $this->db->getRowsCount($this->table, $condition,$count_field);
    }

    public function queryRows($sql)
    {
        return $this->db->queryRows($sql);
    }

    public function query($sql)
    {
        return $this->db->query($sql);
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
                $condition_str .= " `$k`='$v' AND";
            }
            $condition_str = substr($condition_str,0,-3);
        }
        else
        {
            $condition_str = $condition;
        }
        return $condition_str;
    }


    protected function parseOptions($options)
    {
        $sql = '';
        if(!empty($options['order']))
        {
            $sql .= ' ORDER BY '.$options['order'];
        }
        return $sql;
    }

}