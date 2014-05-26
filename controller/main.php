<?php
class main extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		//echo $agent;
		$is_pc = strpos($agent,'windows nt') ? true : false;
		$is_iphone = strpos($agent,'iphone') ? true : false;
		$is_ipad = strpos($agent,'ipad') ? true : false;
		$is_android = strpos($agent,'android') ? true : false;
		if($is_pc){
			;
		}
		if($is_iphone){
			header("Location:http://m.yimiaofengqiang.com".$_SERVER[REQUEST_URI]);
		}
		if($is_ipad){
			header("Location:http://m.yimiaofengqiang.com".$_SERVER[REQUEST_URI]);
		}
		if($is_android){
			 header("Location:http://m.yimiaofengqiang.com".$_SERVER[REQUEST_URI]);
		}
		
	}
	public function view(){
		$this->display("front/index_bak.html");
	}
	
	public function mailindex($mailindex=1){
		$this->index($mailindex);
	}
	
	public function deal(){
		$id = $this->spArgs('id');
		$pros = spClass("m_pro");
		$pro = $pros->find(array('id'=>$id));
		$this->pro = $pro;
		$this->display("front/deal.html");
	}
	
	public function outitems(){
		$pros = spClass("m_pro");
		$pro = $pros->findAll('act_from=20 or type=85 or type=86 or type=87');
		$this->outitems = $pro;
		$this->display("front/outitems.html");		
	}
	
	public function index($mode=false){
		/* $to = "241776039@qq.com";
		$subject = "Test mail";
		$message = "Hello! This is a simple email message.";
		$from = "test@432gou.com";
		$headers = "From: $from";
		mail($to,$subject,$message,$headers);
		echo "Mail Sent."; */
		header("Access-Control-Allow-Origin:*");
		$jsonp = $this->spArgs('jsonp');
		// 搜索
		$searchKey = $this->spArgs('searchKey');
		$q = urldecode($this->spArgs('q'));
		
		// 转成url参数
		if($searchKey)
			header("Location:?q=".$searchKey);
		
		// url参数搜索
		$this->q = urlencode($q);
		if($q)
			$q = "title like '%".$q."%'";
						
		// 价格排序Url参数对应Sql查询串
		$sqlPrice = array(
				'1'=>'nprice<=1',
				'1_10'=>'nprice<10 and nprice>0',
				'10_20'=>'nprice<20 and nprice>10',
				'20_30'=>'nprice<30 and nprice>20',
				'30_40'=>'nprice<40 and nprice>30',
				'10_50'=>'nprice<50 and nprice>=10',
				'50_100'=>'nprice<100 and nprice>=50',
				'100_200'=>'nprice<200 and nprice>=100',
				'200_9999'=>'nprice>=200'
		);
		// end - 构造Key和值对应的数组,方便取值 去除商品过期限制et>=curdate()
		$baseSql = 'st<=curdate() and ischeck=1 and type!=87';
		$baseSqlYu = 'st<=curdate() and ischeck=1';
		$order = 'rank asc,postdt desc';				
		
		$procat = $this->spArgs('procat');
		$page = $this->spArgs('page',1);
		$type = $this->spArgs('type');
		$price = $this->spArgs('price');
		
		$m_procats = spClass("m_procat");
		$procats = $m_procats->findAll('isshow=1','type asc');
		
		$pros = spClass("m_pro");
			
		if($procat || $type || $price){
			if($procat)
				$where = $baseSql.' and cat='.$procat;
			if($type)
				$where = $baseSqlYu.' and type='.$type;
			if($price)
				$where = $baseSql.' and '.$sqlPrice[$price];
		}else{
			$where = $baseSql;
		}
		
		// 搜索
		if($q){
			$where = $q.' and '.$baseSql;
		}
		
		$itemsTemp = $pros->spPager($page,56)->findAll($where,$order);//$pros->spPager($page,56)->findAll($where,$order);
		
		// 这里用foreach & 改变数组的值的时候最后一个数据带有 & 符号,导致最后一条数据重复
		for($i=0;$i<count($itemsTemp);$i++){
			$itemsTemp[$i]['title'] = preg_replace('/【.+?】/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['title'] = preg_replace('/开心赚宝/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['oprice'] = number_format($itemsTemp[$i]['oprice'],2);
			$temp_npriceTail = explode('.',strval(number_format($itemsTemp[$i]['nprice'],2)));
			$itemsTemp[$i]['nprice_tail'] = $temp_npriceTail[1];
		}	
		
		//var_dump($itemsTemp);
		$itemList = array(array(),array(),array(),array());
		if(!empty($itemsTemp)){
			foreach($itemsTemp as $k=>$v){
				array_push($itemList[$k%4],$v);
			}
		}
		
		$smarty = $this->getView();
		//$smarty->caching = true; // 开启缓存
		//$smarty->cache_lifetime = 480; // 页面缓存8分钟
		
		//var_dump($itemList);
		if(!$procat && !$type && !$price)
			$smarty->assign("index",'index');//$this->index = "index";
		$smarty->assign("procat",$procat);//$this->procat = $procat;
		$smarty->assign("type",$type);//$this->type = $type;
		$smarty->assign("price",$price);//$this->price = $price;
		$smarty->assign("procats",$procats);//$this->procats = $procats;
		$smarty->assign("pager",$pros->spPager()->getPager());//$this->pager = $pros->spPager()->getPager();
		$smarty->assign("items",$itemList);//$this->items = $itemList;
		$smarty->assign("admin",$_SESSION['admin'],true);//$this->admin = $_SESSION['admin'];
		
		// 输出静态页面
		/* $content = $this->getView()->fetch("front/index.html");
		$fp = fopen("front/day/update.html","w");
		fwrite($fp, $content);
		fclose($fp); */
		//spClass('spHtml')->make(array('main','index'));
		// END 输出静态页面
		if($mode)
			$smarty->display("front/mailindex.html");
		else
			if($jsonp){
				foreach($itemList as $k=>&$iv){
					foreach($iv as $k=>&$v){
						$v['title'] = iconv('gbk','utf-8',$v['title']);
					}
				}
				echo json_encode($itemList);
			}else
				$smarty->display("front/index.html");
	}
	public function user(){
		$pros = spClass("m_pro");
		$m_procats = spClass("m_procat");
		$procats = $m_procats->findAll('isshow=1','type asc');
		$this->procats = $procats;
		$this->ac = $this->spArgs("ac");
		if(!$this->ac)
			$this->ac = 'bm';
		if($_POST['userReport']){
			$item = array(
				'iid'=>$_POST['iid'],
				'pic'=>$_POST['pic'],
				'oprice'=>$_POST['oprice'],
				'nprice'=>$_POST['nprice'],
				'st'=>$_POST['st'],
				'et'=>date("Y-m-d",86400*7+time()),
				'title'=>$_POST['title'],
				'carriage'=>$_POST['carriage'],
				'num'=>(int)$_POST['num'],//活动数量
				'nick'=>$_POST['ww'],
				'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
				'link'=>'http://item.taobao.com/item.htm?id='.$_POST['iid'],
				'ischeck'=>0,
				'rank'=>500,
				'postdt'=>date('Y-m-d H:i:s'),
				'phone'=>$_POST['phone'],
				'commission_rate'=>$_POST['commissionrate'],
				'volume'=>$_POST['volume'],
				'channel'=>2,//此项为用户报名渠道，采集的渠道为1(默认也是采集的渠道号)
				//以下类别暂时用未定
				'cat'=>$_POST['cat']
			);
			if($this->isInThere($item['iid'])){//如果已存在数据库
				$iteminfo = $pros->find(array('iid'=>trim($item['iid'])));
				$channel = $iteminfo['channel'];
				if($channel==1){
					//如果是采集的，设置渠道号为报名渠道,并设置为未审核状态
					$pros->update(array('iid'=>trim($item['iid'])),array('channel'=>2,'ischeck'=>0));
				}elseif($channel==2){
					//如果已经是报名的，检查其审核状态
					if($iteminfo['ischeck']==0){
						$submitTips = '商品已报名,请勿重复报名！';
					}elseif($iteminfo['ischeck']==2){
						$submitTips = '商品未通过审核,请联系报名管理！';
					}
				}
			}else{
				$art = $pros->create($item);
				if($art){	//修改成功后跳转
					$submitTips = '已成功提交，请耐心等待审核！';
					header("{spUrl c=main a=user}");
				}else
					$submitTips = '提交失败，请刷新页面重新提交！';
			}
		}
		// 报名搜索
		if($this->spArgs('searchIid')){
			if($this->isInThere($this->spArgs('sIid'))){ // 商品是否存在
				$pro = $pros->find(array('iid'=>trim($this->spArgs('sIid'))));
				// 只有在商品为报名渠道的时候才会显示审核状态，如果是采集渠道则显示为未报名
				if($pro['channel']==2){
					if($pro['ischeck']){
						if($pro['ischeck']==1)
							$searchTips = '审核通过！';
						elseif($pro['ischeck']==2)
							$searchTips ='审核不通过！('.$pro['reason'].')';						
					}else{
						$searchTips = '正在审核...';
					}
				}else{
					$searchTips = '该商品暂未报名！';
				}
			}else // 如果没在数据库查到也是未报名
				$searchTips = '该商品暂未报名！';
		}
		// 提交提示
		$this->searchTips = $searchTips;
		$this->submitTips = $submitTips;
		$this->display("front/user.html");
	}
	
	public function isInThere($iid,$table='pro',$field=null){
		$pros = spClass("m_pro");
		if($field){
			$count = 0;
			foreach (self::$dao->query('select * from '.self::$dbconfig['DBPREFIX'].$table.' where enunick='.$field) as $row) {
				$count += 1;
			}
		}else{
			$count = 0;
			foreach ($pros->find(array('iid'=>$iid)) as $row) {
				$count += 1;
			}
		}
		return $count;
	}
}