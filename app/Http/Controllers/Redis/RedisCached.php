<?php
namespace IAServer\Http\Controllers\Redis;

use IAServer\Events\RedisSend;
use IAServer\Http\Controllers\Controller;

class RedisCached extends RedisController
{
    private $channel = null;
    public $result = null;
    public $exist = false;

    public function __construct($channel,$connectionName="default")
    {
        parent::__construct($connectionName);

        $this->channel = $channel;
        $this->result = $this->cached($channel);

        if($this->result==null) {
            $this->exist = false;
        } else
        {
            $this->exist = true;
        }
    }

    public function put($newCache,$seg=60)
    {
        $this->putAndExpire($this->channel,json_encode($newCache),$seg);
    }


    public function get($db)
    {
        return $this->cached($db);
    }
}
?>