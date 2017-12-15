<?php
namespace YYHhelper;
class Http {
    /**
     * 发起一个HTTP/HTTPS的请求
     * 
     * @param $url 接口的URL            
     * @param $params 接口参数 array('content'=>'test', 'format'=>'json');
     * @param $method 请求类型 GET|POST
     * @param $multi 图片信息            
     * @param $extheaders 扩展的包头信息            
     * @return string
     */
    public static function Request ($url, $params = array(), $method = 'GET',$cookies=array(), $multi = false, $extheaders = array()) {
        if (! function_exists('curl_init')){
            exit('Need to open the curl extension');
        }
        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);
        $headers = (array) $extheaders;
        switch ($method) {
            case 'POST' :
                if (! empty($params)) {
                    curl_setopt($ci, CURLOPT_POST, TRUE);
                    if ($multi) {
                        foreach ( $multi as $key => $file ) {
                            $params[$key] = '@' . $file;
                        }
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                        $headers[] = 'Expect: ';
                    } else {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
                    }
                }
                break;
            case 'DELETE' :
            case 'GET' :
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (! empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $url);
        
        if ($headers) {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        }
        if ($cookies) {
            foreach ($cookies as $key => $value){
                $cookie = $key.'='.urlencode($value).'; ';//这里一定要做urlencode，否则会出错
            }
            curl_setopt($ci, CURLOPT_COOKIE, trim($cookie,'; '));
        }
       
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }
    
    /**
     * 获取客户端ip
     */
    public static function get_client_ip($type = 0) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type]; 
    }
    
    public static function downloadFile($filepath = ''){
        if($filepath)
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filepath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0′);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0′);
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }
}
