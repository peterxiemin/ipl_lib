<?php
/**
 * Created by PhpStorm.
 * User: wyf
 * Date: 2015/4/7
 * Time: 21:01
 */



class MutilProcessMongo {
    public $mongo;
    public $cur_collect;
    public $db = "imcms";
    public $collection = "doc_1361";
    public function __construct($conf)
    {
        $host = $conf['mongo']['host'];
        $port = $conf['mongo']['port'];
        $this->mongo = new MongoClient("mongodb://$host:$port");
        $this->cur_collect = $this->getCollection($this->db, $this->collection);
    }

    public function __destruct()
    {

    }

    public function setCollection($db, $collection)
    {
        $this->db = $db;
        $this->collection = $collection;
    }

    public function getCollection($db, $collection)
    {
        return $this->mongo->selectCollection($db, $collection);
    }

    public function insert($arr)
    {
        $ret_arr = $this->cur_collect->insert($arr);
        if (is_array($ret_arr) || $ret_arr === true) {
            return true;
        }
        return false;
    }

    public function update($where, $content, $tag = null)
    {
        if ($tag === null) {
            return $this->cur_collect->update($where, $content);
        }
        else {
            return $this->cur_collect->update($where, $content, $tag);
        }
    }

    public function findOne($where)
    {
        return $this->cur_collect->findOne($where);
    }



}