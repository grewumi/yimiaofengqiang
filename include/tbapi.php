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
function get_clickurl($iid){
    global $Key,$Secret;
    $c = new TopClient;
    $c->appkey = trim($Key);
    $c->secretKey = trim($Secret);
    $req = new TbkItemsDetailGetRequest;
    $req->setFields("click_url,discount_price");
    $req->setNumIids($iid);
    $resp = object_to_array($c->execute($req));
    var_dump($resp);
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
		// �ݹ鷵���м�����ҲҪ����,��Ȼֻ�ܵõ�null,�����˰���
		return getPcid($pcid);
	}
	
}

function getPcidNew($cid){
	$resp = get_contents('http://tiangou.uz.taobao.com/top/getpcid.php?jsonp=1&id='.$cid);
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
		
		// ��ѯfstk_catmap��Ӧ��Ŀ
		$sql='select * from fstk_catmap where cid='.$pcid;
		$result= $legouPDO->query($sql);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result = $result->fetchAll();

		$catMap = $result[0];
		//var_dump($catMap);
		if($catMap){ //�����Ʒ��Ŀ��ӳ��
			$v['cat'] = $catMap['type'];
		}else{
			$v['cat'] = 42;
		}
		
		$item = $v;
		$value = "";
		// ���������ݿ�
		foreach($item as $k =>$v){
			$value.=$k."='".$v."',";
		}
		$value=substr($value,0,strlen($value)-1);
		$sql='update fstk_pro set '.$value.' where iid='.$item['iid'];
		
		$legouPDO->query($sql);
		//var_dump($v);
		//$sql='select * from fstk_catmap where cid='.$pcid;
		// end - ��ѯfstk_catmap��Ӧ��Ŀ
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
	
}
function getItemDetail($num_iid,$mode=1){
 	if($mode==2){
		$result = getItem($num_iid,'approve_status');
	}else{
//		$result = getItem($num_iid,'normal');
		$result = getItemNew($num_iid,'normal');
//                var_dump($result);
                if($result<0){
                    return -1;
                }else{
			$volume = 200;
			$item = array(
				"iid"=>$num_iid,
				"title"=>htmlspecialchars($result['title']),
				"nick"=>htmlspecialchars($result['nick']),
				"pic"=>'http://img01.taobaocdn.com/bao/uploaded/'.$result['pic_url'],                                
				"oprice"=>$result['price'],			
				"st"=>date("Y-m-d"),//��Ʒ�ϼ�ʱ��
				"et"=>date("Y-m-d",86400*7+time()),//��Ʒ�¼�ʱ��
				"cid"=>$result['cid'],
				"link"=>'http://item.taobao.com/item.htm?id='.$num_iid,
				"rank"=>500,
				"postdt"=>date("Y-m-d"),
				"ischeck"=>1,
				"volume"=>$volume,
                                "slink"=>$result['seller_id'],
			);
			$item['title'] = preg_replace('/��.+?��/i','',$item['title']);
			//var_dump($item);
			// �˷�
			$item['carriage']=1;
			// �Ա�����è��Ʒ(��è�г�Ӷ)
			if($result['tmall'])
				$item['shopshow']=0; //��è
			else 
				$item['shopshow']=1;
			// �Ƿ�vip������Ʒ
			$item['shopv']=1;
                        
                        $item['cat'] = 42;//Ĭ����������
                        
//                      $item['commission_rate'] = getCommissionRate($item['iid']);
                        $item['commission_rate'] = -1;
//			var_dump($item);
                        if($mode==3)//ͼƬ��
                            $item['item_imgs'] = $result['item_imgs'];
			return $item;
		}
		
	}
}

function getItemNew($num_iid,$mode='taoke'){
	if($mode == 'normal'){
		$resp = get_contents('http://tiangou.uz.taobao.com/top/1.php?id='.$num_iid);
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
