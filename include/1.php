<?php
function loginTaobaoCode($user = '', $pass = '', $code = '')
{
	if(empty($user))
	{
    	$user = ALIMAMA_ACCOUNT;
    	$pass = ALIMAMA_PASS;
	}
        
    $cookie = getCookie('file');
	$content = openhttp_login('http://www.alimama.com/index.htm', "", $cookie);

	if(strstr($content['content'], '您正在使用的阿里妈妈帐号是'))
	{
		return json_encode(array('status' => 1));
	}
        
    $url = 'https://login.taobao.com/member/login.jhtml';


//	$data_souce_code = 'ua=207YlJgThc3UTYWOBgodVppX2xZaFphU2hdfSJ9%7CYVJ8T3lKeU19S3hOeU15Qxw%3D%7CYFB%2BJwdDKFg%2FWzZMbEJiU2FBb08acR17FmMSMm0y%7CZ1RkSmpZb1xvW2tdblhvW29VbV9zRXBKfk50TnVEc0BzRHFHfFwD%7CZlVuQBkoBjUOORcsAiIbbwx8XjRZP1t7JHs%3D%7CZV9xUXEu%7CZF9xUXEu%7Ca1hrRRw8ViBWJFVtQG0DbgtgDCBWNVg4WzYaexZ5VDtcM1M0RGkHag9kCCRMJlA%2FUWwdaxB%2BGSZJIk4lAWUVeBcoSyVOIUItTmoafRtwAGcGcCd3OQZsGmweOQk%2BD0xrW2xcGD8POAhMIk8qQS0BdxR5GXoXO1o3WH9PeEgMYwRrC2wcOws8DEg%2BXTBQM143UHdHcEAEagdiCWVJI1U6HS0aK28EdShGK04lSW5eaVg%2BDSlNOlQ6ZxdwFn0Nagt9QjREM1RwFn0Mbw9hBlUiSShBD2IHbAA%2FSTlOKQknByd4Jw%3D%3D%7Calt1LBwmHTMGNwEvHi0YNgY3AC4dLRclCzgKOgwiESEbKQc8CD5hPg%3D%3D%7CaVprRRw8aQJuCGUQYUMZS2tFZTB7NxUmET8NIxE%2FDDsLK3Qr%7CaF1zKgoqBDYYKRNMEw%3D%3D%7Cb1xuQGA5CDMDOA8hf19sWmlabl5vVWBTYVtpFyUJOQo4AjkLPAoxCz4OPQ46DDxiQmxfACB%2F%7Cblt1LAwsAjEfLxgiGUYZ%7CbVh2Lw9ZC0UYbx55CWUGaQ5TYEBuXXNDd0R2KXY%3D%7CbFl3Lg5YCkQZbh94CGQHaA9SYUFvXXNCcUZ1KnU%3D%7Cc0ZoMRFZBFU2UjVSNF8pZQh%2BNXJBd0R3Q3NFd0JyQ3BFZUt4VmdUY1kGWQ%3D%3D%7CckVrMhJaB1Y1UTZ4FXAbdzZcO1ozEz1kVW5Ac0Z0KwU3GTkZNwI2BzVqNQ%3D%3D%7CcURqMxNbBlc0UDdQNl0rZwp8N3BDdUZ1QXFHdUBwQXJHZ0l7VWBUZVINUg%3D%3D%7CcEVrMhJaB1Y1UTZ4FXAbdzZcO1ozEz0OIBUhECp1Kg%3D%3D%7Cd0JsNRVdAFEyVjF%2FEncccDFbPF00FDoIJhwtFiZ5Jg%3D%3D%7CdkNtNBQ0GigGPA02AF8A%7CdUJsNRVDEV8CcBNiE2YLex1Ac1N9JBMkCjEHWHZEakpqRHdEdUVxLnE%3D%7CdEFvNhY2GCsFNgU0BTdoNw%3D%3D%7Ce05gORlPHVMOfB9uH2oHdxFMf19xQmxfbF1sXQJd%7Cek1jOho6FE16TWNQYVMMIhA%2BHj4QIxIgFS5xLg%3D%3D%7CeUxiOxtNH1EMfh1sHWgFdRNOfV1zQW9cbV9lVQpV%7CeE1jOhpSD04jRSJpBXcAditAYE59U2BRYlliPWI%3D%7Cf0tlPBxUCUglQyRvA3EGcC1GZkh9SGZUekl%2BSHJBHkE%3D%7CfkpkPR1VCEkkQiVuAnAHcSxHZ0lzQmxecEN0QHdNEk0%3D%7CfUlnPh5WC0onQSZtAXMEci9EZEpwR2lbdUZxSnxPEE8%3D%7CfEhmPx9XCksmQCdsAHIFcy5FZUtxR2lbdUZyQXpLFEs%3D%7CQ3ZYASFpNHUYfhlSPkw7TRB7W3VHaVpvXGxeAV4%3D%7CQndZACAALhwyATQHNwBfAA%3D%3D%7CQXdZACBoNWQTcxx3AVAmRTNYORk3BDAGKBgrGjQPOw49CFcI%7CQHdZACBoNWQTcxx3AVAmRTNYORk3blVuQHNIeSYIOhQ0FDoJOwkzAjBvMA%3D%3D%7CR3JcBSUFKxg2BTcFPw45Zjk%3D%7CRnNdBCRsMWAXdxhzBVQiQTdcPR0zAC4dLx0nFi1yLQ%3D%3D%7CRXBeBydvMmMUdBtwBlchQjRfPh4wAiwfLR8kEiZ5Jg%3D%3D%7CRHFfBiYGKBo0BzUHPAoxbjE%3D%7CS31PfVNhT39RYlRnSXhPelRjWHZDdFppWmxCcV9sWGpEdU5gWmFPfktlVGBOf0hmV2FPdFprW3VEd1loWnREf1FnSXlMYlJmSHtVZVN9TXxSaUd9U2ZIfFJhW3VGc11yQXRadU5gU2VLeElnVGRKeUoV&TPL_username=#user#&TPL_password=#pass#&TPL_checkcode=#code#&loginsite=0&newlogin=0&TPL_redirect_url=http%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3D1&from=alimama&fc=default&style=mini&css_style=&tid=XOR_1_000000000000000000000000000000_652B335441047A030A07057F&support=000001&CtrlVersion=1%2C0%2C0%2C7&loginType=3&minititle=&minipara=&umto=T7a10d75a93843f78e22eaa5aac2fada1&pstrong=2&llnick=&sign=&need_sign=&isIgnore=&full_redirect=true&popid=&callback=&guf=&not_duplite_str=&need_user_id=&poy=&gvfdcname=10&gvfdcre=687474703A2F2F7777772E616C696D616D612E636F6D2F6D656D6265722F6C6F67696E2E68746D&from_encoding=&sub=&TPL_password_2=&loginASR=0&loginASRSuc=0&allp=&oslanguage=zh-CN&sr=1280*1024&osVer=windows%7C5.1&naviVer=firefox%7C30';
	$data_souce_code = 'ua=019YlJgThc3UTYWOBgodVppX2xZaFphU2hdfSJ9%7CYVJ8T3lKeUx%2BTntLe0x2QR4%3D%7CYFB%2BJwdDKFg%2FWzZMbEJiU2FBb08acR17FmMSMm0y%7CZ1RkSmpZb1xvWmhYbV1tWmBXb11xRXdCdkR3QXNEdUVwR3BEdFQL%7CZlVuQBkqBDcFNwMtGzUVJg1SLAxTDA%3D%3D%7CZV9xUXEu%7CZF9xUXEu%7Ca1hrRRw8ViBWJFVtQG0DbgtgDCBWNVg4WzYaexZ5VDtcM1M0RGkHag9kCCRMJlA%2FUWwdaxB%2BGSZJIk4lVD9QIkwrD2sbdhkmRStAL0wjQGQUcxV%2BDmkIfil5NwhiFGIQNwcwAUJlVWJSFjEBNgZCLEEkTyMPeRp3F3QZNVQ5VnFBdkYCbQplBWISNQUyAkYwUz5ePVA5XnlJfk4KZAlsB2tHLVs0EyMUJWEKeyZIJUArR2BQZ1YwAydDNFo0aRl%2BGHMDZAVzTDpKPVp%2BGHMCYQFvCFssRyZPAWwJYg4xRzdAJwcpCSl2KQ%3D%3D%7Calt1LBwpHjICOQI5AzYDOAgzBz0IPxEiGCoENQYzHS0fLwEyAjgKJBclFSMNPg40BigTJxFOEQ%3D%3D%7CaVprRRw8aQJuCGUQYUMZS2tFZTB7NxUmET8NIxE%2FDDsLK3Qr%7CaF1zKgoqBDYYLhpFGg%3D%3D%7Cb1xuQGA5CDMDOA8hf19sWmlabl5vVWBTYVtpFyUJOQo4AjkLPAoxCz4OPQ46DDxiQmxfACB%2F%7Cblt1LAwsAjEfLhwsGUYZ%7CbVh2Lw9ZC0UYbx55CWUGaQ5TYEBuXXNCcER3KHc%3D%7CbFt1LAxaCEYbaQp7Cn8SYgRZakpkPQ46DCIRIxFOYFJ8XHxSY1lpXQJd%7Cc0ZoMRFHFVsGcQBnF3sYdxBNfl5wQmxdZ1ZkO2Q%3D%7CckdpMBBGFFoHdRZnFmMOfhhFdlZ4S2VUbl9pNmk%3D%7CcURqMxNFF1kEdhVkFWANfRtGdVV7SWdQZ1xqNWo%3D%7CcEVrMhIyHC4ANwA7AV4B%7Cd0JsNRU1GygGMAYzATZpNg%3D%3D%7CdkNtNBRCEF4DcRJjEmcKehxBclJ8T2FXYVRiUg1S%7CdUJsNRVdAFEmRilCNGUTcAZtDCwCW29ecEN0Rxg2BCoKKgQyBTMBMG8w%7CdEFvNhZAElwBcxBhEGUIeB5DcFB%2BTGJUY1VnXANc%7Ce05gORlRDF0qSiVOOGkffAphACAOPRMlEiQXI3wj%7Cek9hOBhQDVwrSyRPOWgefQtgASEPPRMlEiUXJHsk%7CeUxiOxs7FScJPwg%2FDTtkOw%3D%3D%7CeE58TmBSfExiUWdUekt8SWdQa0VwR2laaV9xQmxfa1l3Rn1TaVJ8TXhWZ1N9Tn1TYlR6S3pUZVV7SnlXZlR6SnFfaUd3QmxcaEZ2QW9faTY%3D&TPL_username=#user#&TPL_password=#pass#&TPL_checkcode=#code#&loginsite=0&newlogin=0&TPL_redirect_url=http%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3D1&from=alimama&fc=default&style=minisimple&css_style=&tid=XOR_1_000000000000000000000000000000_652B335441047A030A07057F&support=000001&CtrlVersion=1%2C0%2C0%2C7&loginType=3&minititle=&minipara=&umto=Tceccdb7f5e96fab98914f8950e9c4b54&pstrong=2&llnick=&sign=&need_sign=&isIgnore=&full_redirect=true&popid=&callback=&guf=&not_duplite_str=&need_user_id=&poy=&gvfdcname=10&gvfdcre=687474703A2F2F7777772E616C696D616D612E636F6D2F&from_encoding=&sub=&TPL_password_2=&loginASR=0&loginASRSuc=0&allp=&oslanguage=zh-CN&sr=1280*1024&osVer=windows%7C5.1&naviVer=firefox%7C30';
        

	$cookie_info_login_pri = cookie2array(getCookie('file', 'cookieTemp.txt'));
	if(count($cookie_info_login_pri) <= 0)
	{
		$login_url = 'https://login.taobao.com/member/login.jhtml?style=minisimple&from=alimama&redirectURL=http%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3d1&full_redirect=true&disableQuickLogin=true';
		$html = openhttp_login($login_url, '', '', 'http://www.alimama.com/member/login.htm', 'login.taobao.com', 1);
		$cookie_info_login_pri = get_taobao_header_cookie($html['content']);

   		saveCookie(array2cookie($cookie_info_login_pri), 'file', 'cookieTemp.txt');
	}
        
	//获取临时文件里面的cookie 重新登录
//    $user = iconv('utf-8', 'gbk', $user);

   	$data = str_replace(array('#user#', '#pass#', '#code#'), array(urlencode($user), $pass, $code), $data_souce_code);
   	$html = openhttp_login($url, $data, array2cookie($cookie_info_login_pri), 'https://login.taobao.com/member/login.jhtml', 'login.taobao.com', 1);
//   	$html['content'] = iconv('gbk', 'utf-8', $html['content']);
//        echo $html['content'];
	if(strstr($html['content'], '请输入验证码'))
	{

		$ccurl = get_items($html['content'], array('"ccurl":"'), array('"'));
		$ccurl = get_items($html['content'], array('data-src="'), array('"'));
		$message = get_items($html['content'], array('"message":"'), array('"'));

		//获取验证码图片
		$code_img = openhttp_login($ccurl[0].'&_r_=1389602545444', '', array2cookie($cookie_info_login_pri), $url, 'pin.aliyun.com', 0);
//		$img_file = md5(microtime(true)).'.jpg';
                $img_file = '../tmp/Checkcode.jpg';
		$size = file_put_contents($img_file, $code_img['content']);
        if($size <= 0)
        {
		    return json_encode(array('status' => -1, 'msg' => 'code 目录没有写入权限!'));
        }
	   
		if(true)
		{
			$code = authCode($img_file);
//			$codeinfo = authCode($img_file);
//			if($codeinfo['status'] == 1)
//			{
//				$code = $codeinfo['code'];
//			}
		}
		else
		{
		    return json_encode(array('status' => -2, 'codefilename' => $img_file, 'ccurl' => $ccurl[0], 'msg' => 'qing shu ru yan zheng ma!'));
		}
   		$data = str_replace(array('#user#', '#pass#', '#code#'), array(urlencode($user), $pass, $code), $data_souce_code);
   		$html = openhttp_login($url, $data, array2cookie($cookie_info_login_pri), 'https://login.taobao.com/member/login.jhtml', 'login.taobao.com', 1);
//   		$html['content'] = iconv('gbk', 'utf-8', $html['content']);
	}

	$error = get_items($html['content'], array('<p class="error">'), array('<'));
	if(count($error))
	{
		return json_encode(array('status' => -1, 'msg' => $error[0]));
	}

    	$cookie_real = get_taobao_header_cookie($html['content']);
    	$cookie_real = array_merge($cookie_info_login_pri, $cookie_real);

	$gotoURL = get_items($html['content'], array('gotoURL:"'), array('"'));
	if(strstr($gotoURL[0], 'login_unusual'))
	{
		echo $gotoURL[0];
		//return json_encode(array('status' => -1, 'msg' => '请去除淘宝的手机异常登录验证!'));
	}

   	$html = openhttp_login($gotoURL[0], '', array2cookie($cookie_real), $url, 'www.alimama.com', 1, 0);
    	$cookie_reals = get_taobao_header_cookie($html['content']);
    	$cookie_real = array_merge($cookie_real, $cookie_reals);

	$gotoURL_1 = get_items($html['content'], array('Location: '), array("\r\n"));

   	$html = openhttp_login($gotoURL_1[0], '', array2cookie($cookie_real), $url, 'www.alimama.com', 1, 0);
    	$cookie_reals = get_taobao_header_cookie($html['content']);
    	$cookie_real = array_merge($cookie_real, $cookie_reals);

	$gotoURL_1 = get_items($html['content'], array('Location: '), array("\r\n"));
   	$html = openhttp_login($gotoURL_1[0], '', array2cookie($cookie_real), $url, 'www.alimama.com', 1, 0);
    	$cookie_reals = get_taobao_header_cookie($html['content']);
    	$cookie_real = array_merge($cookie_real, $cookie_reals);

    	saveCookie(array2cookie($cookie_real), 'file');
	saveCookie('', 'file', 'cookieTemp.txt');

	unlink($img_file);

	return json_encode(array('status' => 1, 'msg' => '登陆成功!'));

}

function openhttp_login($url, $post='', $cookie='', $referfer='', $host='', $show_header = 1, $follow = 1)
{
    $header = array();

	if(!empty($host))
	{
		$header[] = "Host: ".$host;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIE,$cookie);

	if($follow == 1)
	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	}

	//登录阿里妈妈添加
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);

	if(!empty($referfer)) curl_setopt($ch, CURLOPT_REFERER, $referfer);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0");
	if(count($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
	if($show_header ==1)
	{
		curl_setopt ($ch, CURLOPT_HEADER, 1);
	}
    if(!empty($post)) curl_setopt($ch, CURLOPT_POST, 1);
    if(!empty($post)) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

	$return['content'] = curl_exec($ch);

    $return['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //最后跳转的链接
    $return['effective_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

	curl_close($ch);

	return $return;
}

function getCookie($type = 'db', $filename = 'cookie.txt')
{
    if($type != 'db')
    {
        return read_log($filename);
    }
}
function read_log($filename = ''){
    return trim(file_get_contents('../tmp/cookie/'.$filename));
}

function get_items($string, $start=array(), $end=array(), $fiter_tag=1)	{
	$item_result = array();
	
	preg_match_all('#'.$start[0].'(.*?)'.$end[0].'#s', $string, $item);
	$item_result = $item[1];
	
	if(count($start)==2) {
		$item_result_arr = array();
		if(count($item_result)>1){
			foreach ($item_result as $key => $value){
				preg_match_all('#'.$start[1].'(.*?)'.$end[1].'#s', $value, $items);
				$item_result_arr[] = $items[1][0];
			}
		} else {
			preg_match_all('#'.$start[1].'(.*?)'.$end[1].'#s', $item_result[0], $items);
			$item_result_arr[] = $items[1];
		}
		
		 $item_result = $item_result_arr;
	}
	
	if(count($start)==3) {
		$item_result_arr = array();
		//if(strstr($string, $start[2]) && strstr($string, $end[2])){
		if(count($item_result)){
			foreach ($item_result as $key => $value){
				preg_match_all('#'.$start[2].'(.*?)'.$end[2].'#s', $value, $itemss);
				$item_result_arr = $itemss[1][0];
			}
			
			$item_result = $item_result_arr;
		}
		//}
	}
	
	return $item_result;
}
function cookie2array($cookie_str)
{
	$cookie_ary = explode(';', $cookie_str);

    foreach($cookie_ary as $k=>$val)
    {
		if(empty($val))
		{
			continue;
		}

		$d = explode('=', trim($val));
    	$cookies[$d[0]] = trim($d[1]).(isset($d[2]) ? '='.$d[2] : '');
    }
    return $cookies;
}
function get_taobao_header_cookie($html_content = '')
{
	$cookies = array();

	$cookie_item = get_items($html_content, array('Set-Cookie: '), array(";"));

	foreach($cookie_item as $key => $val)
	{

		$len = strpos($val, '=');
		
		$d = explode('=', trim($val));

    	$cookies[$d[0]] = trim(substr($val, $len+1));
	}

	return $cookies;
}
function array2cookie($cookie_ary)
{
    $cookie = '';

    foreach($cookie_ary as $k=>$v)
    {
        $cookie .= $k.'='.$v.';';
    }

    return rtrim($cookie, ';');
}
function saveCookie($cookie, $type = 'db', $filename = 'cookie.txt')
{
    if($type != 'db')
    {
        write_cookie($cookie,$filename);
        return ;
    }
}
function write_cookie($string, $filename = 'cookie.txt')
{
//	if (is_writable($filename)) {
		if(!$handle = fopen('../tmp/cookie/'.$filename,"w")) {
         	die("open $filename error.");
		}
//	} else {
//    	die("$filename No Write.");
//	}

    $string = trim($string);

    if(!empty($string))
    if(!fwrite($handle, $string, strlen($string))) 
    {
        die("fwrite 1 $filename");
    }

	fclose($handle);
}
function authCode($filename = '')
{
    $post_data = array (
        "type" => "recognize",
//        "softID" => "96821",
//        "softKey" => "af21e117eb4a4befb55ab75d16ffa997",
//        "userName" => DAMA_ACCOUNT,
//        "passWord" => DAMA_PASSWORD,
//        "codeType" => "1004",
//        "imageName" => $imgname,
//        // 要上传的本地文件地址
//        //"imagePath" => "@/wwwroot/luhuang.sinaapp.com/1/taobaoke/dama/20140105115517.jpg;type=image/jpeg" //7.19 以上
//        "imagePath" => "/include/".$filename //7.19 以上
    );

//    $url = 'http://10.207.27.242:81/taobaoke/dama/server.php';
//    $url = rtrim(WEBSITE, '/').'/dama/server.php';
    $url = rtrim('http://'.$_SERVER['HTTP_HOST'], '/').'/?c=virtualapi&a=getCheckcode';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);
    curl_close($ch);
//    echo $output;
//    $output = json_decode($output, true);
//    if($output['code'] == -6)
//    {
//        die("打码网站用户点数不足，请及时充值\n\n");
//    }

    return $output;
}

echo loginTaobaoCode('二手飞车人','rouqiu_5876$.');