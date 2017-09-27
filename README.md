# php-value-cache
PHP Value Cache
# 安装
通过composer，这是推荐的方式，可以使用composer.json 声明依赖，或者运行下面的命令。SDK 包已经放到这里 hoopower/value
$ composer require hoopower/value
# 运行环境
PHP 版本 >= 7.0
# 使用方法
- 静态调用
```php
class MyConfig extends Config{
    static protected $vals = [       # 定义一些配置，通常这些配置是不能修改的，可以作为项目的全局配置
      'a'=>[
        'b'=>11
      ],
      'abc'=>111
    ]; 
}
var_dump(MyConfig::val('a.b'));      # 通过 val 可以读取值，如果不存在将抛异常
var_dump(MyConfig::var('a.b','22')); # 使用 var 可以添加配置或更改值
var_dump(MyConfig::val('a.b'));
var_dump(MyConfig::abc('222'));      # 可以直接读取 aaa 的值
var_dump(MyConfig::val('abc'));
var_dump(MyConfig::val('abd'));      # 实现未定义的话，抛异常
```

- 动态调用，与静态调用不同的是，动态调用不存在的配置返回null而不抛异常
```php
$config = Config::getInstance();     # 构造一个单一实例
$config->set('a.b.c',33);            # 设置值，中间的点会产生三维数组 =>  ["a" => ["b"=> ["c"=>33] ] ]
$config->set('a.[b.c].c',33);        # 设置值，使用[]包裹中间的点，将不产生多维数组 =>  ["a" => ["b.c"=> ["c"=>33] ] ]
var_dump($config->get('a.b'));       # 读取值
var_dump($config->get('a')['b']);    # 效果同上
var_dump($config->get('a.[b.c]'));   # 读取值，使用[]
$config2 = Config::getInstance(1);   # 构造参数不一样将产生不同的实例
var_dump($config2->get('abc'));
var_dump($config2->has('abc'));      # 判断是否有定义，优先在类实例中判断，如果不存在的话，会在静态类中判断
var_dump($config2->abcd('1234'));
var_dump($config2->get('abcd'));
var_dump($config2->abc);             # 直接使用类的成员变量的形式读取
$config2->abc = 456;                 # 直接使用类的成员变量进行设置值
```

- 数组方式访问
```php
$config = Config::getInstance();
$config['d.e']=88;                   # 设置一个值，相当于 $config->set('d.e', 88)
var_dump($config['abc']);            # 读取一个值，相当于 $config->get('abc')
var_dump($config['d.e']);
var_dump($config['d']['e']);         # 读取一个值，相当于 $config->get('d.e')
```

- 方法调用
```php
$config = new Config;
var_dump($config('abc',888));       # 设置一个值，相当于 $config->set('abc', 888)
var_dump($config('abc'));           # 读取一个值，相当于 $config->get('abc')
```
