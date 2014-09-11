<?php
include_once 'func.php';
include_once 'tbtop/ItemGetRequest.php';
include_once 'tbtop/RequestCheckUtil.php';
include_once 'tbtop/TopClient.php';
include_once 'tbtop/TaobaokeItemsDetailGetRequest.php';
include_once 'tbtop/TaobaokeReportGetRequest.php';
include_once 'tbtop/ItemcatsGetRequest.php';
include_once 'tbtop/ShopGetRequest.php';
header("Content-Type:text/html;charset=gbk");

//$app=array('21463466'=>'91cd273f32da3a640d237595a1e827e0');
$sellsapp = array('21823676'=>'eb0df7a66c853b0b9b077d0981f20ef1');
$app = $sellsapp;
foreach($app as $k=>$v){
	global $Key,$Secret;
	$Key = $k;
	$Secret = $v;
}
function gettkreport($page=1){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	$req = new TaobaokeReportGetRequest;
	$req->setFields("trade_id,pay_time,pay_price,num_iid,outer_code,real_pay_fee,commission_rate,commission,item_num,seller_nick,pay_time,app_key");
	$req->setDate("20130710");
	$req->setPageNo($page);
	$req->setPageSize(100);
	$resp = $c->execute($req);	
	$resp = object_to_array($resp->taobaoke_report->taobaoke_report_members);
	return $resp['taobaoke_report_member'];
	
}
function getShop($nick){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	$req = new ShopGetRequest;
	$req->setFields("sid,cid,title,nick,desc,bulletin,pic_path,created,modified");
	$req->setNick($nick);
	$resp = $c->execute($req);
	//var_dump($resp);
	//$resp = object_to_array($resp->item);
	if($resp)
		return $resp;
}
function getItem($num_iid,$mode='taoke')
{
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	//$c->appkey = trim($Key);
	//$c->secretKey = trim($Secret);
	if($mode == 'normal'){
		$req = new ItemGetRequest;
		$req->setFields("title,num_iid,nick,pic_url,cid,list_time,detail_url,approve_status,delist_time,price,nick,freight_payer,post_fee,express_fee,ems_fee,auction_point,has_discount");
		$req->setNumIid($num_iid);
		$resp = $c->execute($req);
//		var_dump($resp);
		$resp = object_to_array($resp->item);
		//var_dump($resp);
	}
	elseif($mode == 'taoke'){
		$req = new TaobaokeItemsDetailGetRequest;
		$req->setFields("iid,title,detail_url,nick,cid,price,pic_url,seller_credit_score,click_url,shop_click_url");
		$req->setNumIids($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->taobaoke_item_details->taobaoke_item_detail);
		//var_dump($resp);
	}elseif($mode == 'approve_status'){
		$req = new ItemGetRequest;
		$req->setFields("approve_status");
		$req->setNumIid($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->item);
	}
	//var_dump($resp);
	if($resp)
		return $resp;
}
function getPcid($cid){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	//$c->appkey = trim($Key);
	//$c->secretKey = trim($Secret);
	$req = new ItemcatsGetRequest;
	$req->setFields("cid,parent_cid,name");
	$req->setCids($cid);
	$resp = $c->execute($req);
	//var_dump($resp);
	
	$pcid = $resp->item_cats->item_cat->parent_cid;
	if($pcid==0){
		$resp = object_to_array($resp->item_cats->item_cat);
		//$resp['name'] = iconv('utf-8','gbk',$resp['name']);
		//var_dump($resp);
		return $resp;
	}else{
		// 递归返回切记这里也要返回,不然只能得到null,杯具了半天
		return getPcid($pcid);
	}
	
}
function getPcidNew($cid){
	$resp = file_get_contents('http://mplife.uz.taobao.com//top/getpcid.php?id='.$cid);
	$rule  = '/class="J_TScriptedModule taeapp(.+?)>(.+?)<\/div>/is';
	preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
//	echo trim($result[0][2]);
	$resp = json_decode(iconv('gbk','utf-8',trim($result[0][2])),1);
	$resp = array_multi2single($resp);
	if($resp){
		return $resp;
	}
}
/* if($_GET['mode']=='ajaxprocat'){
	include 'dbconfig.php';
	$sql = 'select * from '.$DBconfig['DBPREFIX'].'pro';
	$result = $legouPDO->query($sql);
	$result->setFetchMode(PDO::FETCH_ASSOC);
	$Pros = $result->fetchAll();
	//var_dump($Pros);
	foreach($Pros as $k=>$v){
		$pcid = getPcid($v['cid']);
		$pcid = $pcid['cid'];
		
		// 查询fstk_catmap对应类目
		$sql='select * from fstk_catmap where cid='.$pcid;
		$result= $legouPDO->query($sql);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result = $result->fetchAll();

		$catMap = $result[0];
		//var_dump($catMap);
		if($catMap){ //如果商品类目有映射
			$v['cat'] = $catMap['type'];
		}else{
			$v['cat'] = 42;
		}
		
		$item = $v;
		$value = "";
		// 更新入数据库
		foreach($item as $k =>$v){
			$value.=$k."='".$v."',";
		}
		$value=substr($value,0,strlen($value)-1);
		$sql='update fstk_pro set '.$value.' where iid='.$item['iid'];
		
		$legouPDO->query($sql);
		//var_dump($v);
		//$sql='select * from fstk_catmap where cid='.$pcid;
		// end - 查询fstk_catmap对应类目
	}
	echo 'update complate!';
} */
function creatCids(){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	$req = new ItemcatsGetRequest;
	$req->setFields("cid,parent_cid,name");
	$req->setParentCid(0);
	$resp = $c->execute($req);
	$resp = object_to_array($resp->item_cats);	
	//var_dump($resp);
	return $resp;
}
//foreach($)
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
function getShopDetail($nick){
	
}
function getItemDetail($num_iid,$mode=1){
 	if($mode==2){
		$result = getItem($num_iid,'approve_status');
	}else{
//		$result = getItem($num_iid,'normal');
		$result = getItemNew($num_iid,'normal');
//		var_dump($result);
//		echo $result['sub_code'];
		if($result){
			//var_dump($result);
			//$url = 'http://detailskip.taobao.com/json/ifq.htm?id='.$num_iid.'&sid=842397175&opt=&q=1';
			//$result2 = get_url_content($url);
			//preg_match_all('/quanity: (.*?),/is', $result2, $match2);
			//$volume=$match2[1][0]; //取得销量				
			//if(!$volume)
			$volume = 200;
			$item = array(
				"iid"=>$num_iid,
				"title"=>htmlspecialchars($result['title']),
				"nick"=>htmlspecialchars($result['nick']),
				"pic"=>htmlspecialchars($result['pic_url'].'_310x310.jpg'),
				"oprice"=>$result['price'],			
				"st"=>$result['list_time'],//商品上架时间
				"et"=>$result['delist_time'],//商品下架时间
				"cid"=>$result['cid'],
				"link"=>'http://item.taobao.com/item.htm?id='.$num_iid,
				"rank"=>500,
				"postdt"=>date("Y-m-d"),
				"ischeck"=>1,
				"volume"=>$volume			
			);
			$item['title'] = preg_replace('/【.+?】/i','',$item['title']);
			//var_dump($item);
			// 运费
			if($result['freight_payer']=='seller')
				$item['carriage']=1;
			elseif($result['freight_payer']=='buyer'){
				if($result['express_fee'])
					if($result['express_fee']*1<=0.1)
						$item['carriage']=1;
					else 
						$item['carriage']=0;
				if($result['ems_fee'])
					if($result['ems_fee']*1<=0.1)
						$item['carriage']=1;
					else 
						$item['carriage']=0;
				if($result['post_fee'])
					if($result['post_fee']*1<=0.1)
						$item['carriage']=1;
					else
						$item['carriage']=0;
			}
			//echo $result['auction_point'];
			// 淘宝或天猫商品(天猫有抽佣)
			if($result['auction_point'])
				$item['shopshow']=0; //天猫
			else 
				$item['shopshow']=1;
			// 是否vip打折商品
			if($result['has_discount'])
				$item['shopv']=1;
			else 
				$item['shopv']=0;
//			var_dump($item);
			return $item;
		}else{
			return -2;
		}
		/* $result = getItem($num_iid);
		//var_dump($result);
		//获取淘客
		if($result){
			$item['link'] = $result['click_url'];
			$item['slink'] = $result['shop_click_url'];
			$item['ischeck'] = 1;
			//var_dump($item);
			return $item; //获取淘客信息
		}else{
			return 2; //没有淘客
		} */
		
	}
}

function getItemNew($num_iid,$mode='taoke'){
	if($mode == 'normal'){
		$resp = file_get_contents('http://mplife.uz.taobao.com/top/userproInfo.php?id='.$num_iid);
		$rule  = '/class="J_TScriptedModule taeapp(.+?)>(.+?)<\/div>/is';
		preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
//		echo trim($result[0][2]);
		$resp = json_decode(iconv('gbk','utf-8',trim($result[0][2])),1);
		$resp = array_multi2single($resp);
//		var_dump($resp);
	}elseif($mode == 'taoke'){
		$req = new TaobaokeItemsDetailGetRequest;
		$req->setFields("iid,title,detail_url,nick,cid,price,pic_url,seller_credit_score,click_url,shop_click_url");
		$req->setNumIids($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->taobaoke_item_details->taobaoke_item_detail);
		//var_dump($resp);
	}elseif($mode == 'approve_status'){
		$req = new ItemGetRequest;
		$req->setFields("approve_status");
		$req->setNumIid($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->item);
	}
	if($resp['approve_status']){
		unset($resp['code']);
		unset($resp['msg']);
		unset($resp['sub_code']);
		unset($resp['sub_msg']);
	}
//	var_dump($resp);
//	echo '<br />';
	if($resp['approve_status']){
		return $resp;
	}else{
		return null;
	}
		
}
function array_multi2single($array){
	static $result_array = array();
	foreach($array as $k=>$value){
		if(is_array($value)){
			array_multi2single($value);
		}else{
			$result_array[$k] = $value;
		}
	}
	return $result_array;
}
?>
