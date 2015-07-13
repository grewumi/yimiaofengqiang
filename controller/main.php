<?php
class main extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		//echo $agent;
                
                $this->procats = spClass("m_procat")->findAll('isshow=1','type asc');
                
              
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
        public function test(){
           import("tbapi.php");
           var_dump(get_TbkItems($this->spArgs("page")));
        }


        public function mailindex($mailindex=1){
		$this->index($mailindex);
	}
        public function desktop(){
            $Shortcut = "[InternetShortcut]  
            URL=http://www.yimiaofengqiang.com/
            IDList=[{000214A0-0000-0000-C000-000000000046}]
            IconFile=http://www.yimiaofengqiang.com/favicon.ico
            IconIndex=1
            HotKey=0  
            Prop3=19,2  
            ";  
            Header("Content-type: application/octet-stream");  
            header("Content-Disposition: attachment; filename=一秒疯抢.url;");  
            echo $Shortcut;
        } 

        public function deal(){
		$id = $this->spArgs('id');
		$pros = spClass("m_pro");
		$pro = $pros->find(array('id'=>$id));
                $dealsync = $this->spArgs('dealsync');
                // 获取seller_id
//                import("tbapi.php");
//                $seller_id = getItemDetail($pro['iid']);
//                $pro['sid'] = $seller_id['slink'];
                // END 获取seller_id
                if($pro){
                    $frompt = spClass("m_actfrom")->find(array('id'=>$pro['act_from']));
                    $ptname = $frompt['name'];
                    if(strpos($pro['link'],'item.taobao.com')||strpos($pro['link'],'detail.tmall.com'))
                        $this->single = 1;
                    $pro['ptname'] = $ptname;
                    $this->pro = $pro;
                }else{
                    header("Location:/");
                }
                $dujia = json_decode(file_get_contents("http://www.yimiaofengqiang.com/?jsonp=1&othersync=1"),1);
                for($i=0;$i<count($dujia);$i++){
                    $dujia[$i]['title'] = iconv('utf-8','gbk',urldecode($dujia[$i]['title']));
                    $dujia[$i]['shopname'] = iconv('utf-8','gbk',urldecode($dujia[$i]['shopname']));
                }
                $this->dujia = $dujia;
                if($dealsync){
                    $pro['title'] = urlencode(iconv('gbk','utf-8',$pro['title']));
                    $pro['ptname'] = urlencode(iconv('gbk','utf-8',$pro['ptname']));
                    $pro['nick'] = urlencode(iconv('gbk','utf-8',$pro['nick']));
                    $pro['shopname'] = urlencode(iconv('gbk','utf-8',$pro['shopname']));
                    $pro['ww'] = urlencode(iconv('gbk','utf-8',$pro['ww']));
                    echo json_encode($pro);
                }else{
                    $this->display("front/deal.html");
                }
	}
        public function shopdeal(){
            $id = $this->spArgs('id');
            $pros = spClass("m_pro");
            $pro = $pros->find(array('id'=>$id));
            // 获取seller_id
            if($pro['slink']){
                $pro['sid'] = $pro['slink'];
            }else{
                import("tbapi.php");
                $seller_id = getItemDetail($pro['iid']);
                $pro['sid'] = $seller_id['slink'];
            }
            // END 获取seller_id
            if(!$pro['shopname']){
                import("tbapi.php");
                $shipinfo = getShopDetail($pro['sid']);
                $pro['shopname'] = iconv('utf-8','gbk',$shipinfo['shop_title']); 
            }
            
            $this->pro = $pro;
            $dujia = json_decode(file_get_contents("http://www.yimiaofengqiang.com/?jsonp=1&othersync=1"),1);
            for($i=0;$i<count($dujia);$i++){
                $dujia[$i]['title'] = iconv('utf-8','gbk',urldecode($dujia[$i]['title']));
                $dujia[$i]['shopname'] = iconv('utf-8','gbk',urldecode($dujia[$i]['shopname']));
            }
            $this->dujia = $dujia;
            $this->display("front/shopdeal.html");
        }
        public function daren(){
            $this->daren = 1;
            $this->display("front/daren.html");
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
                $othersync = $this->spArgs('othersync');
                $procatsync = $this->spArgs('procatsync');
                $touz = $this->spArgs('touz');
		// 搜索
		$searchKey = $this->spArgs('searchKey');
		$q = urldecode($this->spArgs('q'));
		$this->q_unencode = $q;
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
				'200_9999'=>'nprice>=200',
                                '40_9999'=>'nprice>40',    
		);
		// end - 构造Key和值对应的数组,方便取值 
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
		$baseSqlYu = 'st<=curdate() and et>=curdate() and ischeck=1';
		$order = 'rank asc,postdt desc';				
		
		$procat = $this->spArgs('procat');
		$page = $this->spArgs('page',1);
		$type = $this->spArgs('type');
		$price = $this->spArgs('price');
		$act_from = $this->spArgs('act_from');		
		$pros = spClass("m_pro");
                $ads = spClass("m_ad");
                
                $siderads = $ads->findAll('st<=curdate() and et>=curdate() and cat=0','rank desc');
                $sideradsync = $this->spArgs('sideradsync');
			
		if($procat || $type || $price || $act_from){
			if($procat)
				$where = $baseSql.' and cat='.$procat;
			if($type)
				$where = $baseSqlYu.' and type='.$type;
			if($price)
				$where = $baseSql.' and '.$sqlPrice[$price];
                        if($act_from)
                                $where = $baseSql.' and act_from='.$act_from;
		}else{
			$where = $baseSql;
		}
		
		// 搜索
		if($q){
			$where = $q.' and '.$baseSql;
		}
		if($price || $procat || $type )
                    $itemsTemp = $pros->spPager($page,56)->findAll($where,$order);
                else
                    $itemsTemp = $pros->spPager($page,56)->findAll($where.' and classification=1',$order);
                $itemsC1 = $pros->findAll($where.' and classification=2',$order);//$pros->spPager($page,56)->findAll($where,$order);
                $itemsC2 = $pros->findAll($where.' and classification=3',$order);//$pros->spPager($page,56)->findAll($where,$order);
		
                // 这里用foreach & 改变数组的值的时候最后一个数据带有 & 符号,导致最后一条数据重复
		for($i=0;$i<count($itemsC1);$i++){
			$itemsC1[$i]['title'] = preg_replace('/【.+?】/i','',$itemsC1[$i]['title']);
			$itemsC1[$i]['title'] = preg_replace('/开心赚宝/i','',$itemsC1[$i]['title']);
			$itemsC1[$i]['oprice'] = number_format($itemsC1[$i]['oprice'],2);
                        $itemsC1[$i]['zk'] = number_format($itemsC1[$i]['nprice']/$itemsC1[$i]['oprice']*10,1);
			$temp_npriceTail = explode('.',strval(number_format($itemsC1[$i]['nprice'],2)));
			$itemsC1[$i]['nprice_tail'] = $temp_npriceTail[1];
		}
                
                for($i=0;$i<count($itemsC2);$i++){
			$itemsC2[$i]['title'] = preg_replace('/【.+?】/i','',$itemsC2[$i]['title']);
			$itemsC2[$i]['title'] = preg_replace('/开心赚宝/i','',$itemsC2[$i]['title']);
			$itemsC2[$i]['oprice'] = number_format($itemsC2[$i]['oprice'],2);
                        $itemsC2[$i]['zk'] = number_format($itemsC2[$i]['nprice']/$itemsC2[$i]['oprice']*10,1);
			$temp_npriceTail = explode('.',strval(number_format($itemsC2[$i]['nprice'],2)));
			$itemsC2[$i]['nprice_tail'] = $temp_npriceTail[1];
		}
                
		// 这里用foreach & 改变数组的值的时候最后一个数据带有 & 符号,导致最后一条数据重复
		for($i=0;$i<count($itemsTemp);$i++){
			$itemsTemp[$i]['title'] = preg_replace('/【.+?】/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['title'] = preg_replace('/开心赚宝/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['oprice'] = number_format($itemsTemp[$i]['oprice'],2);
                        $itemsTemp[$i]['zk'] = number_format($itemsTemp[$i]['nprice']/$itemsTemp[$i]['oprice']*10,1);
			$temp_npriceTail = explode('.',strval(number_format($itemsTemp[$i]['nprice'],2)));
			$itemsTemp[$i]['nprice_tail'] = $temp_npriceTail[1];
		}	
		
		//var_dump($itemsTemp);
		$itemList = $itemsTemp;
		
		$smarty = $this->getView();
		//$smarty->caching = true; // 开启缓存
		//$smarty->cache_lifetime = 480; // 页面缓存8分钟
		 $smarty->assign("siderads",$siderads);//$this->procat = $procat;
		//var_dump($itemList);
		if(!$procat && !$type && !$price && !$act_from)
			$smarty->assign("index",'index');//$this->index = "index";
                $smarty->assign("act_from",$act_from);//$this->procat = $procat;
		$smarty->assign("procat",$procat);//$this->procat = $procat;
		$smarty->assign("type",$type);//$this->type = $type;
		$smarty->assign("price",$price);//$this->price = $price;
		$smarty->assign("pager",$pros->spPager()->getPager());//$this->pager = $pros->spPager()->getPager();
                $pagersync = $this->spArgs('pagersync');
		$smarty->assign("items",$itemList);//$this->items = $itemList;
                $smarty->assign("itemsC1",$itemsC1);//$this->items = $itemList;
                $smarty->assign("itemsC2",$itemsC2);//$this->items = $itemList;
                if(!$itemList && !$itemsC1 && $q )
                    $this->searchnull = 1;
		$smarty->assign("admin",$_SESSION['admin'],true);//$this->admin = $_SESSION['admin'];
		
		// 输出静态页面
		/* $content = $this->getView()->fetch("front/index.html");
		$fp = fopen("front/day/update.html","w");
		fwrite($fp, $content);
		fclose($fp); */
		//spClass('spHtml')->make(array('main','index'));
		// END 输出静态页面
		if($mode){
			$smarty->display("front/mailindex.html");
                }else{
                    if($jsonp){ 
                        if($othersync){// 一区json数据
                            for($i=0;$i<count($itemsC1);$i++){
                                $itemsC1[$i]['title'] = urlencode(iconv('gbk','utf-8',$itemsC1[$i]['title']));
                                $itemsC1[$i]['nick'] = urlencode(iconv('gbk','utf-8',$itemsC1[$i]['nick']));
                                $itemsC1[$i]['ww'] = urlencode(iconv('gbk','utf-8',$itemsC1[$i]['ww']));
                                $itemsC1[$i]['shopname'] = urlencode(iconv('gbk','utf-8',$itemsC1[$i]['shopname']));
//                                if($touz)
//                                     $itemsC1[$i][$j]['uzid'] = getidfromiidforuz($itemsC1[$i][$j]['iid']);
                                
                            }
                            echo json_encode($itemsC1);
                        }else{// 二区json数据
                            for($i=0;$i<count($itemList);$i++){
                                $itemList[$i]['title'] = urlencode(iconv('gbk','utf-8',$itemList[$i]['title']));
                                $itemList[$i]['nick'] = urlencode(iconv('gbk','utf-8',$itemList[$i]['nick']));
                                $itemList[$i]['ww'] = urlencode(iconv('gbk','utf-8',$itemList[$i]['ww']));
                                $itemList[$i]['shopname'] = urlencode(iconv('gbk','utf-8',$itemList[$i]['shopname']));
                            }
                            echo json_encode($itemList);
                        }
                    }elseif($procatsync){
                        echo json_encode($this->procats);
                    }elseif($sideradsync){
                        echo json_encode($siderads);
                    }elseif($pagersync){
                        echo json_encode($pros->spPager()->getPager());
                    }else{
                        $smarty->display("front/index.html");
                    }
                }
	}
	public function user($mode='pro'){//用户报名 && 搜索
                $users = spClass("m_u");
		if(!$this->supe_uid)
			header("Location:/?c=user&a=login&refer=".urlencode($_SERVER['REDIRECT_URL']));
		else
			$uinfo = $users->find(array('uid'=>$this->supe_uid));
		$mode = $this->spArgs("mode");
		if($mode=='try')
			$pros = spClass("m_try_items");
		elseif($mode=='pro')
			$pros = spClass("m_pro");
		// 参数
		$this->mode = $mode;
		$this->ac = $this->spArgs("ac");
		if(!$this->ac)
			$this->ac = 'bm';
		
	
		
		if($this->ac=='bm'){ // 用户报名
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
                                        'ww'=>$_POST['ww'],
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
					'cat'=>$_POST['cat'],
                                        'slink'=>$_POST['slink'],
                                        'shopname'=>$_POST['shopname']
				);
                                if($mode=='try')
                                    $item['gailv'] = 1000;
                                if($_COOKIE['ymfq_dpww']==$item['ww']){
                                    if($this->isInThere($item['iid'])){//如果已存在数据库
                                            $iteminfo = $pros->find(array('iid'=>trim($item['iid'])));
                                            $channel = $iteminfo['channel'];
                                            if($channel==1){
                                                    //如果是采集的，设置渠道号为报名渠道,并设置为未审核状态
                                                    $pros->update(array('iid'=>trim($item['iid'])),array('channel'=>2,'ischeck'=>0));
                                                    // 通知审核
                                                    $data = array(
                                                        'smtpemailto'=>'350544519@qq.com',
                                                        'mailsubject'=>'您有新的商品需要审核！',
                                                        'mailbody'=>'您有新商品<a target="_blank" href="'.$item['link'].'">'.$item['title'].'</a>需要审核！<a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'/admin.html">去审核</a>'
                                                    );
//                                                    postData($data,'http://'.$_SERVER['HTTP_HOST'].'/?c=email&a=sendemail');
                                            }elseif($channel==2){
                                                    //如果已经是报名的，检查其审核状态
                                                    if($iteminfo['ischeck']==0){
                                                            $submitTips = '商品已报名,请勿重复报名！';
                                                    }elseif($iteminfo['ischeck']==1){
                                                            $submitTips = '商品已通过审核,请等待排期上线！';
                                                    }elseif($iteminfo['ischeck']==2){
                                                            $submitTips = '商品未通过审核,请联系报名管理！';
                                                    }
                                            }
                                    }else{
                                            $art = $pros->create($item);
                                            if($art){	//修改成功后跳转
                                                    $submitTips = '已成功提交，请耐心等待审核！';
                                                    // 通知审核
                                                    $data = array(
                                                        'smtpemailto'=>'350544519@qq.com',
                                                        'mailsubject'=>'您有新的商品需要审核！',
                                                        'mailbody'=>'您有新商品<a target="_blank" href="'.$item['link'].'">'.$item['title'].'</a>需要审核！<a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'/admin.html">去审核</a>'
                                                    );
                                                    postData($data,'http://'.$_SERVER['HTTP_HOST'].'/?c=email&a=sendemail');
//                                                  header("{spUrl c=main a=user}");
                                            }else
                                                    $submitTips = '提交失败，请刷新页面重新提交！';
                                    }                                    
                                }else{
                                    $submitTips = '报名商品非绑定绑定旺旺所开店铺，请重新报名！';
                                }

			}
		}elseif($this->ac=='cx'){ // 报名搜索
			if($this->spArgs('searchIid')){
				if($mode=='pro')
					$isInThere = $this->isInThere($this->spArgs('sIid'));
				elseif($mode=='try')
					$isInThere = $this->isInThere($this->spArgs('sIid'),'try_items');
				if($isInThere){ // 商品是否存在
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
			
		}
		
		// 提交提示
		$this->searchTips = $searchTips;
		$this->submitTips = $submitTips;
		$this->display("front/user.html");
	}
        public function baoming(){
		if(!$this->supe_uid)
			header("Location:/?c=user&a=login&refer=".urlencode($_SERVER['REDIRECT_URL']));
		$this->display("front/baoming.html");
	}
	public function isInThere($iid,$table='pro',$field=null){
		$pros = spClass("m_".$table);
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
        public function emptyhtml(){
            ;
        }
}