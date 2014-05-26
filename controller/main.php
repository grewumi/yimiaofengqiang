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
		// ����
		$searchKey = $this->spArgs('searchKey');
		$q = urldecode($this->spArgs('q'));
		
		// ת��url����
		if($searchKey)
			header("Location:?q=".$searchKey);
		
		// url��������
		$this->q = urlencode($q);
		if($q)
			$q = "title like '%".$q."%'";
						
		// �۸�����Url������ӦSql��ѯ��
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
		// end - ����Key��ֵ��Ӧ������,����ȡֵ ȥ����Ʒ��������et>=curdate()
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
		
		// ����
		if($q){
			$where = $q.' and '.$baseSql;
		}
		
		$itemsTemp = $pros->spPager($page,56)->findAll($where,$order);//$pros->spPager($page,56)->findAll($where,$order);
		
		// ������foreach & �ı������ֵ��ʱ�����һ�����ݴ��� & ����,�������һ�������ظ�
		for($i=0;$i<count($itemsTemp);$i++){
			$itemsTemp[$i]['title'] = preg_replace('/��.+?��/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['title'] = preg_replace('/����׬��/i','',$itemsTemp[$i]['title']);
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
		//$smarty->caching = true; // ��������
		//$smarty->cache_lifetime = 480; // ҳ�滺��8����
		
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
		
		// �����̬ҳ��
		/* $content = $this->getView()->fetch("front/index.html");
		$fp = fopen("front/day/update.html","w");
		fwrite($fp, $content);
		fclose($fp); */
		//spClass('spHtml')->make(array('main','index'));
		// END �����̬ҳ��
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
				'num'=>(int)$_POST['num'],//�����
				'nick'=>$_POST['ww'],
				'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
				'link'=>'http://item.taobao.com/item.htm?id='.$_POST['iid'],
				'ischeck'=>0,
				'rank'=>500,
				'postdt'=>date('Y-m-d H:i:s'),
				'phone'=>$_POST['phone'],
				'commission_rate'=>$_POST['commissionrate'],
				'volume'=>$_POST['volume'],
				'channel'=>2,//����Ϊ�û������������ɼ�������Ϊ1(Ĭ��Ҳ�ǲɼ���������)
				//���������ʱ��δ��
				'cat'=>$_POST['cat']
			);
			if($this->isInThere($item['iid'])){//����Ѵ������ݿ�
				$iteminfo = $pros->find(array('iid'=>trim($item['iid'])));
				$channel = $iteminfo['channel'];
				if($channel==1){
					//����ǲɼ��ģ�����������Ϊ��������,������Ϊδ���״̬
					$pros->update(array('iid'=>trim($item['iid'])),array('channel'=>2,'ischeck'=>0));
				}elseif($channel==2){
					//����Ѿ��Ǳ����ģ���������״̬
					if($iteminfo['ischeck']==0){
						$submitTips = '��Ʒ�ѱ���,�����ظ�������';
					}elseif($iteminfo['ischeck']==2){
						$submitTips = '��Ʒδͨ�����,����ϵ��������';
					}
				}
			}else{
				$art = $pros->create($item);
				if($art){	//�޸ĳɹ�����ת
					$submitTips = '�ѳɹ��ύ�������ĵȴ���ˣ�';
					header("{spUrl c=main a=user}");
				}else
					$submitTips = '�ύʧ�ܣ���ˢ��ҳ�������ύ��';
			}
		}
		// ��������
		if($this->spArgs('searchIid')){
			if($this->isInThere($this->spArgs('sIid'))){ // ��Ʒ�Ƿ����
				$pro = $pros->find(array('iid'=>trim($this->spArgs('sIid'))));
				// ֻ������ƷΪ����������ʱ��Ż���ʾ���״̬������ǲɼ���������ʾΪδ����
				if($pro['channel']==2){
					if($pro['ischeck']){
						if($pro['ischeck']==1)
							$searchTips = '���ͨ����';
						elseif($pro['ischeck']==2)
							$searchTips ='��˲�ͨ����('.$pro['reason'].')';						
					}else{
						$searchTips = '�������...';
					}
				}else{
					$searchTips = '����Ʒ��δ������';
				}
			}else // ���û�����ݿ�鵽Ҳ��δ����
				$searchTips = '����Ʒ��δ������';
		}
		// �ύ��ʾ
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