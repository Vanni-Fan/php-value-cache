<?php
namespace Power;

trait ValuesTrait{
    /**
     * 获取数组指定下标的值 或 生成/替换数组指定下标的值
     * $index_key 如果是带 . 前缀，将进行多层数组生成，否则当普通字符串处理
     *
     * # 生成数组，并填充默认值
     * $data = [];
     * setValue('.a.b.c', $data, 123);
     * $data = ["a"=>["b"=>["c"=>123]]]
     *
     * $data = [];
     * setValue('a.b.c', $data, 123);
     * $data = ["a.b.c"=>123]
     *
     * @param string $index like as a.b.c.d or .a.b.c
     * @param array $variable
     * @param mixed $value 
     * @return void
     */
    static function setValue($index_key, &$variable, $value){
        if('.' != $index_key{0}){ #不是以点开头的，直接有下标表示
            if(is_array($variable)) $variable[$index_key] = $value;
            else                    $variable->$index_key = $value;
        }else{
            $current_variable = &$variable; # 用于循环中的地址保存
            preg_match_all('#\.(\[[^\[\]]+\]|[^\.]+)#',$index_key,$matched); # 前缀有.此处必定匹配
            if(empty($matched[1])) throw new \Exception("Wrong index_key[$index_key] format!");
            $last = trim(array_pop($matched[1]),'[]');
            foreach($matched[1] as $index_part){
                $index_part = trim($index_part,'[]'); 
                if(!isset($current_variable))    $current_variable = [];//new \StdClass; // 默认生成的是数组
                if(is_object($current_variable)) $current_variable = &$current_variable->$index_part;
                else                             $current_variable = &$current_variable[$index_part];
            }
            if(is_object($current_variable)) $current_variable->$last = $value;
            else                             $current_variable[$last] = $value;
        }
    }

	/**
	 * 返回指定变量的下标的值
	 *   
	 *   比如     $value = getValue('items.0.id', $_GET, 'default_value');
	 *   相当于   $value = isset($_GET['items'][0]->id) ? $_GET['items'][0]->id : 'default_value';
	 * 
	 * @param string $index_key 字符串下标
	 */
	static public function getValue($index_key, $variable, $default=null){
        if('.' != $index_key{0}){
            if(is_array($variable)) return $variable[$index_key] ?? $default;
            else                    return $variable->$index_key ?? $default;
        }else{
            preg_match_all('#\.(\[[^\[\]]+\]|[^\.]+)#',$index_key,$matched); # 前缀有.此处必定匹配
            if(empty($matched[1])) throw new \Exception("Wrong index_key[$index_key] format!");
            foreach($matched[1] as $index_part){
                $index_part = trim($index_part,'[]'); 
                if(!isset($variable))                  return $default;
                if(is_object($variable)){
                    if(!isset($variable->$index_part)) return $default;
                    $variable = $variable->$index_part;
                }else{
                    if(!isset($variable[$index_part])) return $default;
                    $variable = $variable[$index_part];
                }
            }
            return $variable;
        }
	}
}
