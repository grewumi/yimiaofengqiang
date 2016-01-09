<?php
include_once 'func.php';
include_once 'tbtop/ItemGetRequest.php';
include_once 'tbtop/RequestCheckUtil.php';
include_once 'tbtop/TopClient.php';
include_once 'tbtop/TaobaokeItemsDetailGetRequest.php';
include_once 'tbtop/TaobaokeReportGetRequest.php';
include_once 'tbtop/ItemcatsGetRequest.php';
include_once 'tbtop/ShopGetRequest.php';
include_once 'tbtop/TbkItemsDetailGetRequest.php';
include_once 'tbtop/TbkItemGetRequest.php';
include_once 'tbtop/TbkItemInfoGetRequest.php';
include_once 'tbtop/TbkShopsDetailGetRequest.php';
include_once 'tbtop/TbkShopRecommendGetRequest.php';

header("Content-Type:text/html;charset=gbk");

//$app=array('21463466'=>'91cd273f32da3a640d237595a1e827e0');
$sellsapp = array('23122290'=>'8126f2cf1cca5c073cfeefd9740e86fc');
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
function get_TbkItems($page=1,$cat){
    global $Key,$Secret;
    $c = new TopClient;
    $c->appkey = trim($Key);
    $c->secretKey = trim($Secret);
    $req = new TbkItemGetRequest;
    $req->setFields("num_iid");
    $req->setCat($cat);
    $req->setPageNo($page);
    $req->setPageSize(100);
    $resp = $c->execute($req);
    return object_to_array($resp->results);
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
//	var_dump($resp);
	//$resp = object_to_array($resp->item);
	if($resp)
		return $resp;
}
function getShopNew($sid){
	$resp = get_contents('http://tiangou.uz.taobao.com/top/3.php?sid='.$sid);
//        $resp = file_get_contents('http://www.432gou.com/?c=admin&a=getpcid&cid='.$cid);
        $resp = json_decode(iconv('gbk','utf-8',trim($resp)),1);
        if($resp){
            $resp = array_multi2single($resp);
        }else{
            $resp = null;
        }
        
	if($resp){
            return $resp;
        }else{
            return -1;
        }
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
		$req = new TbkItemInfoGetRequest;
                $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,provcity,item_url,small_images");
                $req->setNumIids($num_iid);
                $resp = $c->execute($req);
		$resp = object_to_array($resp->results->n_tbk_item);
//		var_dump($resp);
	}
	elseif($mode == 'taoke'){
		$req = new TbkItemsDetailGetRequest;
                $req->setFields("num_iid,seller_id,nick,title,price,volume,pic_url,item_url,shop_url");
                $req->setNumIids($num_iid);
                $resp = $c->execute($req);
		$resp = object_to_array($resp->tbk_items->tbk_item);
//		var_dump($resp);
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

function getPcidNew($iid){
    $result = get_url_content("http://item.taobao.com/item.htm?id=".$iid);
    if(preg_match('/"rootCatId":"(\d+)"/i', $result,$matches)){
        return $matches[1];
    }else{
        $matches = null;
        if(preg_match('/rcid=(\d+)/i', $result,$matches)){
            return $matches[1];
        }else{
            return 9999;
        }
    }
    
    return $result;
	
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

function getShopDetail($nick){
        global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	$req = new TbkShopsDetailGetRequest;
        $req->setFields("user_id,seller_nick,shop_title,pic_url,shop_url");
        $req->setSellerNicks($nick);
        $resp = $c->execute($req);
        $resp = object_to_array($resp->tbk_shops->tbk_shop);
        return $resp;
}
function getShopRecommend($sellerid){
    global $Key,$Secret;
    $c = new TopClient;
    $c->appkey = trim($Key);
    $c->secretKey = trim($Secret);
    $req = new TbkShopRecommendGetRequest;
    $req->setFields("user_id,shop_title,shop_type,seller_nick,pict_url,shop_url");
    $req->setUserId($sellerid);
    $resp = $c->execute($req);
    $resp = object_to_array($resp);
//    var_dump($resp);
    return $resp;
}
function getItemDetail($num_iid,$mode=1){
 	if($mode==2){
		$result = getItem($num_iid,'approve_status');
	}else{
		$result = getItem($num_iid,'normal');
                $tkresult = getItem($num_iid,'taoke');
//		$result = getItemNew($num_iid,'normal');
//                var_dump($result);
//                var_dump($tkresult);
                if(!$result){
                    return -1;
                }else{
			$volume = 200;
			$item = array(
				"iid"=>$num_iid,
				"title"=>htmlspecialchars($result['title']),
				"nick"=>htmlspecialchars($tkresult['nick']),
                                "ww"=>htmlspecialchars($tkresult['nick']),
				"pic"=>$result['pict_url'],//'http://img01.taobaocdn.com/bao/uploaded/'                                
				"oprice"=>$result['reserve_price'],
                                "nprice"=>$result['zk_final_price'],
				"st"=>date("Y-m-d"),//商品上架时间
				"et"=>date("Y-m-d",86400*7+time()),//商品下架时间
				"cid"=>0,
				"link"=>'http://item.taobao.com/item.htm?id='.$num_iid,
				"rank"=>500,
				"postdt"=>date("Y-m-d"),
				"ischeck"=>1,
				"volume"=>$tkresult['volume'],
                                "slink"=>$tkresult['seller_id'],
			);
			$item['title'] = preg_replace('/【.+?】/i','',$item['title']);
			//var_dump($item);
			// 运费
			$item['carriage']=1;
			// 淘宝或天猫商品(天猫有抽佣)
			if($result['user_type'])
				$item['shopshow']=0; //天猫
			else 
				$item['shopshow']=1;
			// 是否vip打折商品
			$item['shopv']=1;
                        
                        $item['cat'] = 42;//默认其他分类
                        
//                      $item['commission_rate'] = getCommissionRate($item['iid']);
                        $item['commission_rate'] = -1;
                        
                        $shopinfo = getShopDetail($item['nick']);
                        $item['shopname'] = htmlspecialchars($shopinfo['shop_title']);
//			var_dump($shopinfo);
                        if($mode==3){//图片集
                            if(!$result['small_images']['string']){
                                $item['item_imgs'] = $item['pic'];
                            }else{
                                if(is_array($result['small_images']['string'])){
                                    foreach($result['small_images']['string'] as $v){
                                        $imgsTemp .= $v.',';
                                    }
                                    $imgsTemp = $item['pic'].','.$imgsTemp;
                                    $item['item_imgs'] = substr($imgsTemp,0,strlen($imgsTemp)-1);
                                }else{
                                    $imgsTemp = $item['pic'].','.$result['small_images']['string'];
                                    $item['item_imgs'] = $imgsTemp;
                                }
                                
                                
                            }
                            
                            //echo $item['item_imgs'];
                        }
			return $item;
		}
		
	}
}

function getItemNew($num_iid,$mode='taoke'){
	if($mode == 'normal'){
		$resp = get_contents('http://yinxiang.uz.taobao.com/top/1.php?id='.$num_iid);
//                echo $resp;
//                $resp = file_get_contents('http://www.432gou.com/?c=admin&a=getitem&iid='.$num_iid);
                $resp = json_decode(iconv('gbk','utf-8',trim($resp)),1);
                if($resp){
                    $resp = array_multi2single($resp);
                }else{
                    $resp = null;
                }
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
//        var_dump($resp);
	if($resp){
            return $resp;
	}else{
            return -1;
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
function getcid($iid,$tmall){
    if($tmall){
        $resp = get_contents('http://detail.tmall.com/item.htm?id='.$iid);
        $rule  = '/TShop.Setup(.+?)"categoryId":"(\d+)"/is';
        preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
//        echo $resp;
//        $rule  = '/TShop.Setup(.+?)"categoryId":"(\d+)"/i';
//        preg_match($rule,$resp,$result);
//        var_dump($result);
        $cid = (int)$result[0][2];
    }else{
        $resp = get_contents('http://item.taobao.com/item.htm?id='.$iid);
        $rule  = '/g_config.idata={(.+?)rcid(.+?),(.+?)\'(\d+)\',/is';
        preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
//        var_dump($result);
//	echo trim($result[0][2]);
        $cid = (int)$result[0][4];
    }
    if($cid)
        return $cid;
    else
        return 0;
}
?>
