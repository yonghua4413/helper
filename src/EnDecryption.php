<?php
namespace YYHhelper;

class EnDecryption {
    
    /**
    * 加密一个数组或字符串
    */
    public static function encrypt($array = array()){
        $info = base64_encode( json_encode($array) );
        $num = ceil( strlen($info) / 1.5 );
        $key1 = substr($info, 0, $num);
        $result = strtr($info, array($key1 => strrev($key1)));
        $key2 = substr($result, -$num, $num-2);
        $result = strtr($result, array($key2 => strrev($key2)));
        return $result;
	}
  
  public static function decrypt($str = ''){
        $num = ceil(strlen($str) / 1.5);
        $key2 = substr($str, -$num, $num-2);
        $str = strtr($str, array($key2 => strrev($key2)));
        $key1 = substr($str, 0, $num);
        $result = strtr($str, array($key1 => strrev($key1)));
        $info = json_decode(base64_decode($result), true);
        return $info;
	}
}
