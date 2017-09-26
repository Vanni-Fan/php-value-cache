<?php

namespace Power;

trait InstanceTrait{
	static public function getInstance(){
        static $instance = [];
		$params = func_get_args();
		$key    = md5(static::class.json_encode($params));
		if(!isset($instance[$key])){
            $instance[$key] = (new \ReflectionClass(static::class))->newInstanceArgs($params);
		}
		return $instance[$key];
	}
}
