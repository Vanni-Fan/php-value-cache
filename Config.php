<?php
namespace Power;

class Config implements \ArrayAccess{
    use InstanceTrait,ValuesTrait;

    const null = "\01\02";

    protected $vars = [];
    static protected $vals = [];
    public function __construct($name='config'){ $this->name=$name;}

    # 静态方法
    static public function val($index_key){
        $value = self::getValue('.'.$index_key, static::$vals, self::null);
        if($value == self::null) throw new \Exception("[no $index_key index]");
        return $value;
    }
    static public function var($index_key, $value=null){
        self::val($index_key);
        self::setValue('.'.$index_key, static::$vals, $value);
        return $value;
    }
    static public function __callStatic($index_key, $params){
        array_unshift($params, $index_key); 
        return call_user_func_array('static::var', $params);
    }

    # 对象方法
    public function get($index_key, $default=null) {
        return $this->getValue('.'.$index_key, $this->vars) ?? $this->getValue('.'.$index_key, static::$vals) ?? $default;
    }
    public function set($index_key, $value) { 
        $this->setValue('.'.$index_key, $this->vars, $value);
        return $value;
    }
    public function has($index_key) { 
        $object = !($this->getValue('.'.$index_key, $this->vars, self::null) == self::null); 
        $class  = !($this->getValue('.'.$index_key, static::$vals, self::null) == self::null); 
        return $object || $class;
    }

    # 对象方法调用
    # 成员变量的形式访问
    public function __call($index_key, $params) { 
        $method = $params ? [$this, 'set'] : [$this, 'get'];
        array_unshift($params, $index_key); 
        return call_user_func_array($method, $params);
    }
    public function __get($index_key)           { return $this->get($index_key) ?? static::val($index_key);                                   }
    public function __set($index_key,$value)    { return $this->set($index_key, $value);                                                    }

    # 数组方式访问
    public function offsetExists($index_key)     { return $this->has($index_key);               }
    public function offsetGet($index_key)        { return $this->get($index_key);               }
    public function offsetSet($index_key,$value) { return $this->set($index_key,$value);        }
    public function offsetUnset($index_key)      { unset($this->vars[$index_key]); return true; }

    # 对象当方法
    public function __invoke($index_key, $value=null){ 
        return $value ? $this->set($index_key, $value) : $this->get($index_key); 
    }
}

#class MyConfig extends Config{
#    static protected $vals = ['a'=>['b'=>11],'abc'=>111];
#}

# 1 静态调用
#var_dump(MyConfig::val('a.b'));
#var_dump(MyConfig::var('a.b','22'));
#var_dump(MyConfig::val('a.b'));
#var_dump(MyConfig::abc('222'));
#var_dump(MyConfig::val('abc'));
#var_dump(MyConfig::val('abd')); // 报错
#exit;

# 2 动态调用
#$config = Config::getInstance();
#$config->set('a.b.c',33);
#var_dump($config->get('a.b'));
#$config2 = Config::getInstance(1);
#var_dump($config2->get('abc'));
#var_dump($config2->has('abc'));
#var_dump($config2->abcd('1234'));
#var_dump($config2->get('abcd'));
#var_dump($config2->abc);
#exit;

# 3 数组方式访问
#$config = Config::getInstance();
#$config['d.e']=88;
#var_dump($config['abc']);
#var_dump($config['d.e']);
#var_dump($config['d']['e']);
#var_dump($config->get('d.e'));
#exit;

# 4 方法调用
#$config = new Config;
#var_dump($config('abc',888));
#var_dump($config('abc'));
#exit;
