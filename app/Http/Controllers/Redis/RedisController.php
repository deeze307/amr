<?php
namespace IAServer\Http\Controllers\Redis;

use IAServer\Events\RedisSend;
use IAServer\Http\Controllers\Controller;

class RedisController extends Controller
{
    public $redis;

    public function __construct($connectionName="default") {
        $this->redis = \LRedis::connection($connectionName);
    }

    public function putAndExpire($canal,$message,$expireInSeg=60)
    {
        $this->redis->set($canal,$message);
        $this->redis->expire($canal,$expireInSeg);
    }

    public function cached($canal)
    {
        try
        {
            $result = json_decode($this->redis->get($canal));
            if($result==null) {
                return null;
            } else {
                return (object) $result;
            }

        } catch( \Exception $ex)
        {
            return (object) ['error' => $ex->getMessage()];
        }
    }

    public function publish($canal,$message)
    {
        $this->redis->publish($canal,$message);
    }
/*
    public function publish($canal,$message)
    {
        return event(new RedisSend($canal,json_encode($message)));
    }*/
}
?>