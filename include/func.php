<?php
require 'checklogo.php';
require 'checkrate.php';
require 'getvolume.php';
require 'pregcaijicontent.php';
function getapiurl($website){
	$apiIp = '121.199.33.15';
	return 'http://'.$apiIp.'/uzcaiji/type/'.$website.'.html';
}
function get_redirect_url_once($url){
   $header = get_headers($url, 1);
   return $header['Location'][0];
 }
function get_redirect_url($url){
   $header = get_headers($url, 1);
   if (strpos($header[0], '301') !== false || strpos($header[0], '302') !== false) {
     if(is_array($header['Location'])) {
       return $header['Location'][count($header['Location'])-1];
     }else{
       return $header['Location'];
        }
   }else {
     return $url;
   }
 }
function convertUrlQuery($query){
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param){
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}
function getUrlQuery($array_query){
    $tmp = array();
    foreach($array_query as $k=>$param){
        $tmp[] = $k.'='.$param;
    }
    $params = implode('&',$tmp);
    return $params;
}
function get_redirect_url_pro($url, $referer="", $timeout = 10) {
   $redirect_url = false;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_HEADER, TRUE);
   curl_setopt($ch, CURLOPT_NOBODY, TRUE);//不返回请求体内容
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION,TRUE);//允许请求的链接跳转
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
   curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:*/*','User-Agent:Mozilla/4.0 (compatible; Win32; WinHttp.WinHttpRequest.5)','Connection: Keep-Alive'));
   curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
   curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验2
   if($referer){
     curl_setopt($ch, CURLOPT_REFERER, $referer);//设置referer
   }
   $content = curl_exec($ch);
//   var_dump(curl_errno($ch));
   if(!curl_errno($ch)) {
//        var_dump(curl_getinfo($ch));
        $redirect_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);//获取最终请求的url地址
   }
   return $redirect_url;
}
function getidfromiidforuz($iid){
    $uziteminfo = file_get_contents('http://yinxiang.uz.taobao.com/d/getidfromiid?iid='.$iid);
    $uziteminfo = object_to_array(json_decode($uziteminfo));
    $uzid = $uziteminfo['id'];
    if($uzid!='null')
        return $uzid;
    else
        return null;
}
// 分词结果之回调函数 (param: 分好的词组成的数组)
function words_cb($ar)
{
	foreach ($ar as $tmp)
	{
		if ($tmp == "\n")
		{
			echo $tmp;
			continue;
		}
		echo $tmp . ' ';
	}
	flush();
}
function get_url_content($url) {
	$contents=file_get_contents($url);
	if($contents){
		return $contents;
	}elseif(function_exists("curl_init")){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.getRandIp(),'CLIENT-IP:'.getRandIp(),'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11'));
		$contents = curl_exec($ch);
//                echo $contents;
		curl_close($ch);
		return $contents;
	}
}
function post_contents($url,$data){
    // 创建一个新cURL资源
    $proxy = 'http://202.114.144.15:8088';
    $ch = curl_init();  
    // 设置URL和相应的选项
    if($url && $data){
        curl_setopt($ch, CURLOPT_URL,$url);  
        curl_setopt ($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_POST,1);  
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);  
        // 抓取URL并把它传递给浏览器  
        $contents = curl_exec($ch);  
        curl_close($ch);
        return $contents;
    }
    //关闭cURL资源，并且释放系统资源  
    curl_close($ch);
}
function get_contents($url){
//	$contents=file_get_contents($url);
//	if($contents){
//		return $contents;
//	}elseif(function_exists("curl_init")){
//                $loginpassw = 'qq576342340:576342340';
//                $proxy_ip = '107.150.60.66';
//                $proxy_port = '62001';
                $proxy = 'http://101.226.249.237:80';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
                curl_setopt($ch,CURLOPT_PROXY,$proxy);
//                curl_setopt($ch,CURLOPT_HEADER,0) ;
//                curl_setopt($ch,CURLOPT_PROXYPORT,$proxy_port);
//                curl_setopt($ch,CURLOPT_PROXYTYPE,'HTTP');
//                curl_setopt($ch,CURLOPT_PROXY,$proxy_ip);
//                curl_setopt($ch,CURLOPT_PROXYUSERPWD,$loginpassw);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$contents = curl_exec($ch);
                curl_close($ch);
                return $contents;
//                if(is_array(json_decode(iconv('gbk','utf-8',trim($contents)),1))){
//                    return $contents;
//                }else{
//                    $GLOBALS['G_SP']['randip'] = getRandIp();
//                    get_contents($url);
//                }
//	}
}
function getRandIp(){
    $ip_long = array(

            array('607649792', '608174079'), //36.56.0.0-36.63.255.255

            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255

            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255

            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255

            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255

            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255

            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255

            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255

            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255

            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255

        );

        $rand_key = mt_rand(0, 9);

        return long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
}
 function getshorturl($long_url){
     $apiKey = '3780574640';
     $apiUrl = 'http://api.t.sina.com.cn/short_url/shorten.json?source='.$apiKey.'&url_long='.$long_url;
     $response = file_get_contents($apiUrl);
     $json = object_to_array(json_decode($response));
     return $json[0]['url_short'];
}
 function getshorturlclicks($short_url){
     $apiKey = '3780574640';
     $apiUrl = 'http://api.t.sina.com.cn/short_url/clicks.json?source='.$apiKey.'&url_short='.$short_url;
     $response = file_get_contents($apiUrl);
     $json = object_to_array(json_decode($response));
     return $json[0]['clicks'];
}
function object_to_array($obj)
{
		$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
		foreach ($_arr as $key => $val)
		{
			$val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
			$arr[$key] = $val;
		}
		return $arr;
}
// N维数组去空值
function array_no_empty($arr) {
    if (is_array($arr)) {
        foreach ( $arr as $k => $v ) {
            if (empty($v)) unset($arr[$k]);
            elseif (is_array($v)) {
                $arr[$k] = array_no_empty($v);
            }
        }
    }
    return $arr;
}

//cookie设置
function ssetcookie($var, $value, $life=0) {
	setcookie($GLOBALS['G_SP']['SC']['cookiepre'].$var, $value, $life?($GLOBALS['G_SP']['timestamp']+$life):0, $GLOBALS['G_SP']['SC']['cookiepath'], $GLOBALS['G_SP']['SC']['cookiedomain'], $_SERVER['SERVER_PORT']==443?1:0);
}

//清空cookie
function clearcookie() {
	ssetcookie('auth', '', -86400 * 365);
	$GLOBALS['G_SP']['supe_uid'] = '';
}

//字符串解密加密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;	// 随机密钥长度 取值 0-32;
				// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
				// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
				// 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : $GLOBALS['G_SP']['ext']['spUcenter']['UC_KEY']);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/*
*功能：php完美实现下载远程图片保存到本地
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式
*当保存文件名称为空时则使用远程文件原来的名称
*/
function getImage($url,$save_dir='',$filename='',$type=0){
	if(trim($url)==''){
		return array('file_name'=>'','save_path'=>'','error'=>1);
	}
	if(trim($save_dir)==''){
		$save_dir='./';
	}
    if(trim($filename)==''){//保存文件名
        $ext=strrchr($url,'.');
        if($ext!='.gif'&&$ext!='.jpg'){
			return array('file_name'=>'','save_path'=>'','error'=>3);
		}
        $filename=time().$ext;
    }
	if(0!==strrpos($save_dir,'/')){
		$save_dir.='/';
	}
	//创建保存目录
	if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
		return array('file_name'=>'','save_path'=>'','error'=>5);
	}
    //获取远程文件所采用的方法 
    if($type){
		$ch=curl_init();
		$timeout=5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$img=curl_exec($ch);
		curl_close($ch);
		//echo $img;
    }else{
		//echo $url;
		ob_start();
		readfile($url);
		$img = ob_get_contents(); 
		ob_end_clean(); 
    }
    //$size=strlen($img);
	//echo $img;
    //文件大小 
	//echo $save_dir.$filename;
    $fp2=@fopen($save_dir.$filename,'w');
    fwrite($fp2,$img);
    fclose($fp2);
	unset($img,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}
//获取文件列表
function list_dir($dir){
	$result = array();
	if (is_dir($dir)){
		$file_dir = scandir($dir);
		foreach($file_dir as $file){
			if ($file == '.' || $file == '..'){
				continue;
			}
			elseif (is_dir($dir.$file)){
				$result = array_merge($result, list_dir($dir.$file.'/'));
			}
			else{
				array_push($result, $dir.$file);
			}
		}
	}
	return $result;
}

function postData($data,$url){
    foreach($data as $k=>$v){
        $contents = $contents.$k.'='.$v.'&&';
    }
    $contents = substr($contents,0,-2);
//    echo $contents;
    $opts = array(
            'http'=>array(
                'method'=>"POST",
                'content'=>$contents,
                'timeout'=>900,
            ));
    $context = stream_context_create($opts);
    $html = @file_get_contents($url, false, $context);
//    echo $html;
}
//推送图文广播流
function graphicfeedpost($iids,$url){
    foreach($iids as $k=>$v){
        $res = $res.$v.',';
    }
    $res = substr($res,0,-1);
    $contents = "in=in(".$res.")";
//    echo $contents;
    $opts = array(
            'http'=>array(
                'method'=>"POST",
                'content'=>$contents,
                'timeout'=>900,
            ));
    $context = stream_context_create($opts);
    $html = @file_get_contents($url, false,$context);
    echo $html;
}

function tail($file,&$pos) {  
    $buf = "";  
    if(!$pos) $pos = filesize($file);  
    $fd = inotify_init();  
    $watch_descriptor = inotify_add_watch($fd, $file, IN_ALL_EVENTS);  
    while (true) {  
        $events = inotify_read($fd);  
        foreach ($events as $event=>$evdetails) {  
            switch (true) {  
                case ($evdetails['mask'] & IN_MODIFY):  
                    inotify_rm_watch($fd, $watch_descriptor);  
                    fclose($fd);  
                    $fp = fopen($file,'r');  
                    if (!$fp) return false;  
                    fseek($fp,$pos);  
                    while (!feof($fp)) {  
                        $buf .= fread($fp,8192);  
                    }  
                    $pos = ftell($fp);  
                    fclose($fp);  
                    return $buf;  
                    break;  
                case ($evdetails['mask'] & IN_MOVE):  
                case ($evdetails['mask'] & IN_MOVE_SELF):  
                case ($evdetails['mask'] & IN_DELETE):  
                case ($evdetails['mask'] & IN_DELETE_SELF):  
                    inotify_rm_watch($fd, $watch_descriptor);  
                    fclose($fd);  
                    return false;  
                    break;  
            }  
        }  
    }  
}  

function unicode2utf8($str){
        if(!$str) return $str;
        $decode = json_decode($str);
        if($decode) return $decode;
        $str = '["' . $str . '"]';
        $decode = json_decode($str);
        if(count($decode) == 1){
                return $decode[0];
        }
        return $str;
}
?>