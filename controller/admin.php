<?php
class admin extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		import('public-data.php');
		import("function_login_taobao.php");
		global $caijiusers,$website;
		$this->caijiusers = $caijiusers;
		$this->mode = $this->spArgs('mode');
		// postdt>=curdate()Ϊ������ӣ�����������
		$pros = spClass('m_pro');
		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
		
                foreach($website as $k=>&$v){
			if($k!='none')
				$v['tcounts'] = count($pros->findAll('act_from='.$v['actType'].' and '.$where));
		}
		
		$this->website = $website; 
	} 
        public function updateRank(){
            spClass('m_pro')->runSql('update fstk_pro set rank=500 where iid in (select t.iid from (select iid from fstk_pro where rank=499 and st<=curdate() and et>=curdate() and ischeck=1 and type!=87 and classification=1 and last_modify<curdate() order by rank asc,postdt desc limit 112,18446744073709551615)as t)');
        }
        public function ymfqzs_getshopstatus(){
            $seller_nick = $this->spArgs("shopww");
            $seller_nick = urldecode($seller_nick);
            
            $shopinfo = spClass("m_ymfqzs")->find(array("shopww"=>iconv('utf-8','gb2312',$seller_nick)));
            
            header("Content-type: application/json");
            if($shopinfo){
                echo '{"ok":true,"data":{"status":'.$shopinfo['status'].',"seller_nick":"'.$seller_nick.'"}}';
            }else{
                echo '{"ok":true,"data":{"status":0,"seller_nick":"'.$seller_nick.'"}}';
            }
        }
        public function ymfqzs_setshopstatus(){
            $seller_nick = $this->spArgs("seller_nick");
            $status = $this->spArgs("status");
            //ת��������ҳ�������ͬ
//            $seller_nick = iconv('utf-8','gb2312',$seller_nick);
            $shopinfo = spClass("m_ymfqzs")->find(array("shopww"=>iconv('utf-8','gb2312',$seller_nick)));
            
            if($shopinfo){
                header("Content-type: application/json");
                // ����status
                if(spClass("m_ymfqzs")->update(array('shopww'=>iconv('utf-8','gb2312',$seller_nick)),array('status'=>(int)$status)))
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"���³ɹ���").'"}}';
                else
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"����ʧ�ܣ�").'"}}';
            }else{
                //����������
                import("tbapi.php");
                $shop = getShopNew($seller_nick);
                // �ַ�ת��
                //ת��������ҳ�������ͬ
                $shop['shop_title'] = iconv('utf-8','gb2312',$shop['shop_title']);
                $shop['seller_nick'] = iconv('utf-8','gb2312',$shop['seller_nick']);
                header("Content-type: application/json");
                if(spClass("m_ymfqzs")->create(array('shopww'=>$shop['seller_nick'],'status'=>(int)$status,'shopid'=>$shop['user_id'],'shopname'=>$shop['shop_title'])))
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"�����ɹ���").'"}}';
                else
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"����ʧ�ܣ�").'"}}';
            }
        }
        public function login(){		
		$cmd = $this->spArgs('cmd');
		if($cmd=='out'){
			if($_SESSION['admin'] || $_SESSION['iscaijiuser']){
				if($_SESSION['admin'])
					$_SESSION['admin'] = null;
				else 
					$_SESSION['iscaijiuser'] = null;
			}
		}
			
		
		// ��¼�ж�
		if($this->spArgs()){
			if($this->spArgs('username')=='admin' && $this->spArgs('password')=='bingqiling7788'){
				$_SESSION['admin'] = 1;
				header("Location:/admin.html");
			}elseif($this->spArgs('username') && $this->spArgs('password')){
				foreach($this->caijiusers as $k=>$v){
					if($this->spArgs('username') == $v['username'] && $this->spArgs('password') == $v['password']){
						$_SESSION['iscaijiuser'] = $this->spArgs('username');
					}
				}
				// ������֤
				//$url_last = '.uz.taobao.com/view/front/getusernick.php';
				//$url = 'http://'.$_SESSION['iscaijiuser'].$url_last;
				// END - ������֤
				if($_SESSION['iscaijiuser'])
					header("Location:/dbselect.html");
			} 
		}
		else{
			header("Location:/login.html");
		} 
		
		$this->display("admin/login.html");
	}
	
	
	// ��ȡ��Ʒ��Ϣ
	public function getiteminfo(){
		/* if(!$_SESSION['admin'])
			header("Location:/login.html"); */
		
		$iid = trim($this->spArgs('iid'));
		
		$catmaps = spClass("m_catmap");
		import('tbapi.php');
		$item = getItemDetail($iid,3);
//                var_dump($item);
//                echo $item;
                if($item<0){
                    echo '{"iid":"-1"}';
                }else{
                    // �ݹ�ȡ���Ա������ڵ�
                    if($GLOBALS['G_SP']['autocat']){
                        $pcid = getPcidNew($item['iid']);
//                        echo 'cid:'.$item['cid'];
//                        echo ',pcid:'.$pcid;

                        // ��ѯfstk_catmap��Ӧ��Ŀ
                        $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                        //var_dump($catMap);
                        if($catMap){ //�����Ʒ��Ŀ��ӳ��
                                $item['cat'] = (int)$catMap['type'];
                        }else{
                                $item['cat'] = 42;
                        }
//                        echo ',cat:'.$item['cat'];
                    }
                    // end - �ݹ�ȡ���Ա������ڵ�


        //	    echo $pcid;
                    // end - ��ѯfstk_catmap��Ӧ��Ŀ

                    // �ַ�ת��
                    $item['title'] = iconv('utf-8','gb2312',$item['title']);
                    $item['title'] = preg_replace('/��.+?��/i','',$item['title']);
                    $item['nick'] = iconv('utf-8','gb2312',$item['nick']);
                    $item['shopname'] = iconv('utf-8','gb2312',$item['shopname']);
//                    $item['volume'] = getvolume($iid,$item['shopshow']);
                    if(!$item['volume'])
                            $item['volume'] = -1;
                    
                    // end - �ַ�ת��
                    //$item['sid'] = getShop($item['nick']);
                    //var_dump($item);
                    echo '{"iid":"'.$item['iid'].'","title":"'.$item['title'].'","slink":"'.$item['slink'].'","nick":"'.$item['nick'].'","shopname":"'.$item['shopname'].'","pic":"'.$item['pic'].'","oprice":"'.$item['oprice'].'","nprice":"'.$item['nprice'].'","st":"'.$item['st'].'","et":"'.$item['et'].'","cid":"'.$item['cid'].'","link":"'.$item['link'].'","rank":'.$item['rank'].',"postdt":"'.$item['postdt'].'","ischeck":'.$item['ischeck'].',"volume":'.$item['volume'].',"carriage":'.$item['carriage'].',"shopshow":'.$item['shopshow'].',"shopv":'.$item['shopv'].',"cat":'.$item['cat'].',"item_imgs":"'.$item['item_imgs'].'","commission_rate":'.$item['commission_rate'].'}';

                }
	}
        function yqtout(){
            // �ļ�����
            header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header('Content-disposition: attachment; filename='.basename('./tmp/yqtdata/yqtout.txt')); //�ļ���   
            header("Content-Type: application/text"); //text��ʽ�� 
            header("Content-Transfer-Encoding: binary"); //��������������Ƕ������ļ�    
            header('Content-Length: '. filesize('./tmp/yqtdata/yqtout.txt')); //������������ļ���С   
            @readfile('./tmp/yqtdata/yqtout.txt');
        }
        public function adenter($outs,$guanggao,$step){
            $outs_zu = array_chunk($outs,$step);
            foreach($guanggao as $k=>$v){
                if($k==0){//��һ�����λ��β�����
                    foreach($outs_zu as &$iv){//β��׷�ӹ��λ
                        array_push($iv,$v);
                    }
                }elseif($k<=4){//ֵ�滻
                    foreach($outs_zu as &$iv){
                        $iv[$k-1] = $v;
                    }
                }
            }
            // �������� 
            $outs = array();
            foreach($outs_zu as &$v){
                if(empty($outs))
                    $outs = $v;
                else
                    $outs = array_merge($outs,$v);
            }
            return $outs;
        }
        public function yqtswitch(){
            //����ļ���
            $datalist=list_dir('./tmp/yqtdata/');
            foreach($datalist as $k=>$val){
                    unlink($val);
            }
            $pros = spClass("m_pro");
            $where = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
            $order = 'rank asc,postdt desc';
//            $where .= ' and classification=2';
            $outs = $pros->findAll($where,$order,'','480');
            $guanggao = $pros->findAll($where.' and type=85',$order);
            $outs = $this->adenter($outs, $guanggao, 5);
//            var_dump($outs);
//            var_dump($outs_zu);
            for($i=0;$i<count($outs);$i++){
                $yqtout[$i]['imglnk'] = $outs[$i]['pic'];
                $yqtout[$i]['goodid'] = $outs[$i]['iid'];
                $yqtout[$i]['title'] = $outs[$i]['title'];
                $yqtout[$i]['address'] = "";
                $yqtout[$i]['rebate'] = $outs[$i]['nprice'];
                $yqtout[$i]['price'] = $outs[$i]['oprice'];
                $yqtout[$i]['count'] = $outs[$i]['volume'];
                $yqtout[$i]['forward'] = "";
                $yqtout[$i]['buylnk'] = 'http://www.yimiaofengqiang.com/main/deal/id/'.$outs[$i]['id'].'.html';
//                $yqtout[$i]['buylnk'] = getshorturl($yqtout[$i][7]);
                $yqtout[$i]['shortlnk'] = "";
                $yqtout[$i]['zktype'] = "";
                $yqtout[$i]['commission'] = "";
                $yqtout[$i]['commissionRete'] = $outs[$i]['commission_rate'];
                if($outs[$i]['shopshow'])
                    $yqtout[$i]['isTmall'] = 'False';
                else
                    $yqtout[$i]['isTmall'] = 'True';
                $yqtout[$i]['isJu'] = 'False';
                $yqtout[$i]['userNumberId'] = "";
                $yqtout[$i]['nickname'] = "";
                $yqtout[$i]['sign'] = '0';
                $yqtout[$i]['eventid'] = "";
                $yqtout[$i]['leftdays'] = "0";
                $yqtout[$i]['shareRate'] = "";
                $yqtout[$i]['eventStatusStr'] = "";
                $yqtout[$i]['eventTitle'] = "";
                $yqtout[$i]['eventlnkPromo'] = "";
                $yqtout[$i]['remarkWord'] = "";
            }
//            var_dump($yqtout);
            $fp=fopen("tmp/yqtdata/yqtout.txt",'a');
            if($fp){
                echo 'tmp/yqtdata/yqtout.txt�����ɹ�.<br />';
                foreach($yqtout as $k=>$iv){
                   $str = '';
                   $str = "{'imglnk':'".$iv['imglnk']."','goodid':'".$iv['goodid']."','title':'".$iv['title']."','address':'".$iv['address']."','rebate':'".$iv['rebate']."',"
                           . "'price':'".$iv['price']."','count':'".$iv['count']."','forward':'".$iv['forward']."','buylnk':'".$iv['buylnk']."','shortlnk':'".$iv['shortlnk']."',"
                           . "'zktype':'".$iv['zktype']."','commission':'".$iv['commission']."','commissionRete':'".$iv['commissionRete']."','isTmall':'".$iv['isTmall']."',"
                           . "'isJu':'".$iv['isJu']."','userNumberId':'".$iv['userNumberId']."','nickname':'".$iv['nickname']."','sign':'".$iv['sign']."','eventid':'".$iv['eventid']."',"
                           . "'leftdays':'".$iv['leftdays']."','shareRate':'".$iv['shareRate']."','eventStatusStr':'".$iv['eventStatusStr']."','eventTitle':'".$iv['eventTitle']."',"
                           . "'eventlnkPromo':'".$iv['eventlnkPromo']."','remarkWord':'".$iv['remarkWord']."'}";
                   $contents = fwrite($fp,$str."\r\n"); 
               }   
            }else{
                echo '����ʧ�ܣ�';
            }
            fclose($fp);
            header("Location:/yqtout.html");
        }
	public function getCommissionRate($iid){
		if(getCommissionRate('38510058624')=='-2'){//cookieģ���¼ʧ��
			if(loginTaobao('liushiyan8','liujun987'))//���µ�¼(��֤���¼),����cookie
				$this->loginalimama = 1;
			else
				$this->loginalimama = 0;
			
			if($this->loginalimama)//��¼�ɹ�
				return getCommissionRate($iid);
			else
				return -2;
		}else{//cookieģ���½
			return getCommissionRate($iid);
		}
	}
	// ��̨��ҳ
	public function index(){
		ini_set('memory_limit','128M');
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass("m_pro");
               
                
		// ����û���¼ܵ���Ʒͳ��
		$this->allPros = $pros->spCache(480)->findCount('st<=curdate() and et>=curdate()');
		
		// �����ύ��û���¼ܵ���Ʒͳ��
		$this->todayPros = $pros->spCache(480)->findCount('st<=curdate() and et>=curdate() and postdt>=curdate()');
		
		// ������Ʒ
		$this->guoqis = $pros->spCache(480)->findCount('et<curdate()');
		
		$this->indexCur = 1;
		$this->display("admin/index.html");
	}
	
	// ��Ʒ����
	public function xuqi(){
		$id = $this->spArgs("id");
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
		}else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
		}
		$pros->update(array('id'=>$id),array('et'=>date("Y-m-d",time()+24*60*60*7)));
		header("Location:".$referUrl);
	}
	
	// ��Ʒ����
	public function pro(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$type = $this->spArgs('type');
		$sh = $this->spArgs('sh');
		$q = $this->spArgs('q');
		$status = $this->spArgs('status');
                $classification = $this->spArgs('classification');
		$this->procats = spClass("m_procat")->findAll('isshow=1','type asc');
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$this->tryItemCur = 1;
		}
		else{
			$pros = spClass("m_pro");
			$this->proCur = 1;
		}
		
		$page = $this->spArgs('page',1);

		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
		$order = 'rank asc,postdt desc';
		
                if($type){
                    if($type==87)
                        $where = 'st<=curdate() and et>=curdate() and ischeck=1 and type='.$type;
                    else
                        $where .= ' and type='.$type;
                }
		
                if($classification)
                    $where .= ' and classification='.$classification;
                                
		if($sh=='no')
			$where = 'ischeck=0';
		elseif($sh=='ck2')
			$where = 'ischeck=2';
		
		if($status=='ygq')
			$where = 'ischeck=1 and et<curdate()';
		
		if($q){
                    if(eregi('^[0-9]*$',$q)){
                        $where = 'iid='.$q;
                    }else{
                        $q = urldecode($this->spArgs('q'));
                        $where = "title like '%".urldecode($q)."%'";
                    }
                    $itemsTemp['data'] = $pros->spPager($page,56)->findAll($where,$order);
                    $itemsTemp['pager'] = $pros->spPager()->getPager();
//                    $itemsTemp = $pros->spCache(-1)->getmypage($where,$order,$page,56);
                    
                }else{
//                echo $where;
//                    $itemsTemp = $pros->spPager($page,56)->findAll($where,$order);
                    $itemsTemp = $pros->spCache(480)->getmypage($where,$order,$page,56);
                }
		$this->items = $itemsTemp['data'];
		$this->pager = $itemsTemp['pager'];
                $this->type = $type;
		$this->sh = $sh;
                $this->classification = $classification;
                
		$this->display("admin/pro.html");
	}
	// ��Ʒ���
	public function checkpro(){
		$id = $this->spArgs("id");
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
		}else{
			$pros = spClass("m_pro");
		}
		$pro = $pros->find(array('id'=>$id));
                
                $userinfo = spClass("m_u")->find(array('ww'=>$pro['ww']));
		$uinfo =spClass('spUcenter')->uc_get_user($userinfo['username']);
		$uemail = $uinfo[2];//var_dump($uinfo);
                
		if($_POST['checkit']){
			if($_POST['checkpro']==1){
                                if($pros->update(array('id'=>$id),array('ischeck'=>1,'type'=>87))){
//                                    echo $pros->dumpSql();
                                    $mailsubject = '��ͨ����ˣ�';
                                    echo '�����ɹ�,��Ʒ��ͨ����ˣ�';
                                }					
			}elseif($_POST['checkpro']==2){
				if($_POST['reason'] || $_POST['reasonSelect']){
					if($_POST['reasonSelect']){
						foreach($_POST['reasonSelect'] as $v){
							$reason .= $v;
						}
					}
					if($_POST['reason']){
						$reason .= $_POST['reason'];
					}
					if($pros->update(array('id'=>$id),array('ischeck'=>2,'reason'=>'�� '.$reason))){
                                                $mailsubject = '��ͨ����ˣ�('.$reason.')';
						echo '�����ɹ�,��Ʒ��ͨ����ˣ�';
                                        }
				}else
					echo '����ʧ��,����д��ע��';
			}
                        
                        $mailbody = "<h1>����������Ʒ</h1><br />"
                        . "<a target='_blank' href='".$pro[link]."'>".$pro[title]."</a><h2><span style='color:red'>".$mailsubject."</span></h2><br />"
                        . "��ϵQQ:350544519";
                
                        if($uemail){
                            import("email.class.php");
                            $smtpserver = "smtp.163.com";//SMTP������
                            $smtpserverport =25;//SMTP�������˿�
                            $smtpusermail = "yimiaofengqiang@163.com";//SMTP���������û�����
                            $smtpuser = "yimiaofengqiang@163.com";//SMTP���������û��ʺ�
                            $smtppass = "z123456";//SMTP���������û�����

                            $smtpemailto = $uemail;//���͸�˭
                            $mailsubject = $mailsubject;//�ʼ�����
                            $mailbody = $mailbody;//�ʼ�����
                            $mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
                            ##########################################
                            $smtp = spClass("smtp");
                            $smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
                            $smtp->debug = FALSE;//�Ƿ���ʾ���͵ĵ�����Ϣ
                            $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
                        }
                
//				header("Location:/pro/sh/no.html");
		}

                               
		$this->pro = $pro; 
		$this->display('admin/checkpro.html');
	}
	
	// �����Ƿ����
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
	
	// ��Ʒ���
	public function addpro($mode='pro'){
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
			$this->tryItemCur = 1;
		}else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
			$this->proCur = 1;
		}
		
//		$pros = spClass("m_pro");
		$actfrom = spClass("m_actfrom");
                $classification = spClass("m_classification");
		$proCat = spClass("m_procat");
		
		if($_POST['modPro']){
			$item = array(
					'pic'=>$_POST['pic'],
					'iid'=>$_POST['iid'],
					'oprice'=>$_POST['oprice'],
					'nprice'=>$_POST['nprice'],
					'st'=>$_POST['st'],
					'et'=>$_POST['et'],
					'cat'=>$_POST['cat'],
					'act_from' =>$_POST['act_from'],
                                        'classification' =>$_POST['classification'],
					'rank'=>(int)$_POST['rank'],
					'title'=>$_POST['title'],
					'link'=>$_POST['link'],
					'volume'=>(int)$_POST['volume'],
					'remark'=>$_POST['remark'],
					'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
					'carriage'=>(int)$_POST['carriage'],
					'last_modify'=>date('Y-m-d H:i:s'),
					'ischeck'=>1,
					'postdt'=>date("Y-m-d"),
					'type'=>$_POST['type'],
					'shopshow'=>$_POST['shopshow'],
					'shopv'=>$_POST['shopv'],
					'ww'=>$_POST['ww'],
					'nick'=>$_POST['ww'],
                                        'slink'=>$_POST['slink'],
                                        'shopname'=>$_POST['shopname'],
                                        'quan'=>$_POST['quan'],
			);
			if($_POST['commissionrate'])
				$item['commission_rate'] = $_POST['commissionrate'];
			else
				$item['commission_rate'] = -1;
			if($_POST['forward'])
				$item['postdt'] = date("Y-m-d H:i:s");
			if($this->mode!='try'){// ������Ʒ���
                                if($this->isInThere($item['iid'])){
					$submitTips = '��Ʒ�Ѵ���,�����ظ����';
				}else{
					$art = $pros->create($item);
					if($art){	//�޸ĳɹ�����ת
						$submitTips = '��ӳɹ�';
                                                if($GLOBALS['G_SP']['ajaxToUz']['addpro']){
                                                    $this->postDateToEachUz($item);
                                                }
//						header("Location:".$referUrl);
					}else
						$submitTips = '���ʧ��';
				}
			}else{// ������Ʒ���
                                unset($item['classification']);
				if($this->isInThere($item['iid'],'try_items')){
					$submitTips = '������Ʒ�Ѵ���,�����ظ����';
				}else{
					$item['istry'] = 1;
					$item['gailv'] = $_POST['gailv'];
					$art = $pros->create($item);
					if($art){	//�޸ĳɹ�����ת
						$submitTips = '��ӳɹ�';
						header("Location:".$referUrl);
					}
					else
						$submitTips = '���ʧ��';
				}
			}
		}
	
		if($mode=='try')
			$this->tryadd = "tryadd";
		$this->st = date("Y-m-d");
		$this->et = date("Y-m-d",86400*7+time());
		
		$actfroms = $actfrom->findAll();
		$proCats = $proCat->findAll();
                $classifications = $classification->findAll();
		// ��Ʒ���
		$this->actfroms = $actfroms;
		$this->proCats = $proCats;
                $this->classifications = $classifications;
		// �ύ��ʾ
		$this->submitTips = $submitTips;
		$this->display("admin/addpro.html");
	}
	
	// ɾ��������Ʒ
	public function delgq(){
	//	import('tbapi.php');
		$pros = spClass("m_pro");
		$gqPros = $pros->findAll('et<curdate()');
	//	foreach($gqPros as $k=>$v){
	//		$info = getItemDetail($v['iid']);
	//		$pros->update(array('iid'=>$v['iid']),array('et'=>$info['et']));
	//	}
                if($pros->delete('et<curdate()')){
                    $this->guoqis = $pros->spCache(-1)->findCount('et<curdate()');
                    header("Location:/admin.html");
                }
			
	}
	// ��Ʒɾ��
	public function delpro(){
                if(!$_SESSION['admin'])
			header("Location:/login.html");
                
		$id = $this->spArgs('id');
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
                }else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
                }
                $iteminfo = $pros->find(array('id'=>$id));
                if($pros->delete(array('id'=>$id))){
                    $item = array('iid'=>$iteminfo['iid'],'del'=>1);
                    if($GLOBALS['G_SP']['ajaxToUz']['delpro']){
                        $this->postDateToEachUz($item);
                    }
//                    header("Location:".$referUrl);
                }
		
	}
        public function postDateToEachUz($item){
            
//            $item['title'] = iconv('gbk','utf-8',$item['title']);
//            $item['nick'] = iconv('gbk','utf-8',$item['nick']);
//            $item['ww'] = iconv('gbk','utf-8',$item['ww']);
//            
//            $itemEncode = urlencode(json_encode($item));
//            echo "��������<br />";
//            echo $itemEncode;
//            echo "��������<br />";
//            $itemDecode = json_decode(urldecode($itemEncode),1); 
//            $itemDecode['title'] = iconv('utf-8','gbk',$itemDecode['title']);
//            $itemDecode['nick'] = iconv('utf-8','gbk',$itemDecode['nick']);
//            $itemDecode['ww'] = iconv('utf-8','gbk',$itemDecode['ww']);
//            var_dump($itemDecode);
            
            foreach($GLOBALS['G_SP']['ajaxToWhich'] as $k=>$v){
                if($v){
                    $this->postDataToUzPhp($item,$k);
//                    $url = "http://www.432gou.com/?c=admin&a=postDataToUzPhpForYmfq&item=".$itemEncode."&uz=".$k;
//                    echo file_get_contents($url);
                }
            }
//            $this->postDataToUzPhp($item,'admin');
//            if($GLOBALS['G_SP']['ajaxToWhich']['youpinba'])
//                 $this->postDataToUzPhp($item,'youpinba');
//            if($GLOBALS['G_SP']['ajaxToWhich']['okbuy'])
//                 $this->postDataToUzPhp($item,'okbuy');
//            if($GLOBALS['G_SP']['ajaxToWhich']['mplife'])
//                 $this->postDataToUzPhp($item,'mplife'); 
//            if($GLOBALS['G_SP']['ajaxToWhich']['viphuiyuan'])
//                $this->postDataToUzPhp($item,'viphuiyuan');
//            if($GLOBALS['G_SP']['ajaxToWhich']['tiangou'])
//                $this->postDataToUzPhp($item,'tiangou');
//            if($GLOBALS['G_SP']['ajaxToWhich']['jumei'])
//                $this->postDataToUzPhp($item,'jumei');
        }
	// ��Ʒ�޸�
	public function modpro($mode='pro'){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
			$this->tryItemCur = 1;
		}else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
			$this->proCur = 1;
		}
		
		$actfrom = spClass("m_actfrom");
                $classification = spClass("m_classification");
		$proCat = spClass("m_procat");
		
		$id = $this->spArgs('id');
		$pro = $pros->find(array('id'=>$id));
                $tags_show = unserialize($pro['tags']);
                foreach($tags_show as $v){
                    $pro['tags_show'] .= $v.' ';
                }
                $pro['tags_show'] = trim($pro['tags_show']);
                
		if($_POST['modPro']){
			$item = array(
					'pic'=>$_POST['pic'],
					'iid'=>$_POST['iid'],
					'oprice'=>$_POST['oprice'],
					'nprice'=>$_POST['nprice'],
					'st'=>$_POST['st'],
					'et'=>$_POST['et'],
					'cat'=>$_POST['cat'],
					'act_from' =>$_POST['act_from'],
                                        'classification' =>(int)$_POST['classification'],
					'rank'=>(int)$_POST['rank'],
					'title'=>$_POST['title'],
					'link'=>$_POST['link'],
					'volume'=>(int)$_POST['volume'],
					'remark'=>$_POST['remark'],
					'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
					'carriage'=>(int)$_POST['carriage'],
					'last_modify'=>date('Y-m-d H:i:s'),
					'ischeck'=>1,
					'type'=>$_POST['type'],
					'shopshow'=>$_POST['shopshow'],
					'shopv'=>$_POST['shopv'],
					'ww'=>$_POST['ww'],
					'nick'=>$_POST['ww'],
                                        'slink'=>$_POST['slink'],
                                        'shopname'=>$_POST['shopname'],
                                        'quan'=>$_POST['quan'],
			);
                        if($_POST['tags']){
                            $tags = $var=explode(" ",$_POST['tags']);
                            $item['tags'] = serialize($tags);
                        }
			if($_POST['commissionrate'])
				$item['commission_rate'] = $_POST['commissionrate'];
			else
				$item['commission_rate'] = -1;
			if($_POST['forward']){
				$item['st'] = date('Y-m-d');
				$item['postdt'] = date('Y-m-d H:i:s');
                        }else{
                            $item['postdt'] = $pro['postdt'];
                        }
			if($this->mode!='try'){
				$art = $pros->update(array('id'=>$id),$item);
			}else{
                                unset($item['classification']);
				$item['istry'] = 1;
				$item['gailv'] = $_POST['gailv'];
				$art = $pros->update(array('id'=>$id),$item);
			}
			if($art){ // �޸ĳɹ�����ת
				$submitTips = '�޸ĳɹ�';
//                                var_dump($item);
                                if($GLOBALS['G_SP']['ajaxToUz']['modpro']){
                                    $this->postDateToEachUz($item);
                                }
//				if($this->mode!='try')
//					header("Location:".$referUrl);
//				else
//					header("Location:".$referUrl);
			}else
				$submitTips = '�޸�ʧ��';
		}
		
		
		
		$actfroms = $actfrom->findAll();
                $classifications = $classification->findAll();
		$proCats = $proCat->findAll();
		
		$this->submitTips = $submitTips;
		$this->pro = $pro;
		$this->actfroms = $actfroms;
                $this->classifications = $classifications;
		$this->proCats = $proCats;
		$this->display("admin/modpro.html");
	}
	
	// �û�����
	public function yonghu(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		$users = spClass("m_u");
		$page = $this->spArgs('page',1);
                $q = trim($this->spArgs('q'));
                $this->paixu = trim($this->spArgs('paixu'))?trim($this->spArgs('paixu')):'jf';
                if($q){
                    $usersinfo = $users->findAll(array('username'=>$q));
                    if(empty($usersinfo))
                        $usersinfo = $users->findAll(array('ww'=>$q));
                }else{
                    $usersinfo = $users->spPager($page,56)->findAll($where,$this->paixu.' desc');
                }
		$this->usersinfo = $usersinfo;
		if($_POST['submit']){
			$username = $this->spArgs("username");
                        $SAlljf = $users->find(array('username'=>$username));
                        if($SAlljf){
                            $Njf = $this->spArgs("jf")?$this->spArgs("jf"):0;
                            $Nhyjf = $this->spArgs("hyjf")?$this->spArgs("hyjf"):0;
                            $Ndeposit = $this->spArgs("deposit")?$this->spArgs("deposit"):0;
                            $NAlljf = array(
                                'jf'=>$SAlljf['jf'] + $Njf,
                                'hyjf'=>$SAlljf['hyjf'] + $Nhyjf,
                                'deposit'=>$SAlljf['deposit'] + $Ndeposit
                            );
                            $art = $users->update(array('username'=>$username),$NAlljf);
                            if($art)
                                    $this->tips = "��ֵ�ɹ���,��ˢ��ҳ��鿴";
                            else
                                    $this->tips = "��ֵʧ�ܣ�";
                        }else{
                            $this->tips = "�鲻�����û���";
                        }
		}
		if($_POST['super']){
			if(md5($this->spArgs("mima"))=='918d06b0e3b05da224cfdf3223f37e23')
                            $_SESSION['admin_yonghu'] = true;
                            
		}
                if($_SESSION['admin_yonghu'])
                    $this->superadmin = 1;
		//$pros = spClass("m_pro");
                $this->pager = $users->spPager()->getPager();
                $this->yonghuCur =1;
		$this->display("admin/yonghu.html");
	}
	
	// ��������
	public function link(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//$pros = spClass("m_pro");
                $this->linkCur =1;
		$this->display("admin/link.html");
	}
	
	// ������
	public function ad(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
                
                $adtype = $this->spArgs("adtype");
                $this->adtype = $adtype;
                if($adtype==1){
                    $ads = spClass("m_ad");
                    $cmd = $this->spArgs("cmd");
                    $id = $this->spArgs("id");
                    $allads = $ads->findAll('','rank desc');
                    $this->allads = $allads;
                    $this->adCur =1;
                    switch($cmd){
                        case 'mod':
                            $ad = $ads->find(array('id'=>$id));
                            $this->ad = $ad;
                            if($this->spArgs("modAd")){
                                $res = array(
                                   "src"=>$this->spArgs("src"),
                                   "link"=>$this->spArgs("link"),
                                   "st"=>$this->spArgs("st"),
                                   "et"=>$this->spArgs("et"),
                                   "remark"=>$this->spArgs("remark"),
                                   "rank"=>$this->spArgs("rank"),
                                   "cat"=>$this->spArgs("cat")
                                );
                                if($ads->update(array('id'=>$id),$res))
                                    echo '�޸ĳɹ�';
                                else
                                    echo '�޸�ʧ��';
                            }
                            break;
                        case 'del':
                            if($ads->delete(array('id'=>$id)))
                                echo 'ɾ���ɹ�';
                            else
                                echo 'ɾ��ʧ��';
                            break;
                        default:
                            if($this->spArgs("modAd")){
                                $res = array(
                                   "src"=>$this->spArgs("src"),
                                   "link"=>$this->spArgs("link"),
                                   "st"=>$this->spArgs("st"),
                                   "et"=>$this->spArgs("et"),
                                   "remark"=>$this->spArgs("remark"),
                                   "rank"=>$this->spArgs("rank"),
                                   "cat"=>$this->spArgs("cat")
                                );
                                if($ads->create($res))
                                    echo '��ӳɹ�';
                                else
                                    echo '���ʧ��';
                            }
                            break;
                    }
                }elseif($adtype==2){
                    $this->ggws = spClass("m_ggw")->findAll();
                    $cmd = $this->spArgs("cmd");
                    $id = $this->spArgs("id");
                    switch($cmd){
                        case 'mod':
                            $ad = spClass("m_ggw")->find(array('id'=>$id));
                            $this->ad = $ad;
                            if($this->spArgs("modAd")){
                                $res = array(
                                   "dh"=>$this->spArgs("dh")
                                );
                                if(spClass("m_ggw")->update(array('id'=>$id),$res))
                                    echo '�޸ĳɹ�';
                                else
                                    echo '�޸�ʧ��';
                            }
                            break;
                        default:
                            break;
                    }
                }
               
                $this->curdate = date("Y-m-d");
                $this->curdate_et = date("Y-m-d",time()+24*60*60*30);
                
		$this->display("admin/ad.html");
	}
	
	// ��̨��Ʒ�ɼ�ҳ
	public function proget(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		
		//import('public-data.php');
		$pros = spClass('m_pro');		
		$where = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noyj = $this->spArgs('noyj');
		$date = $this->spArgs('date');
		if($noyj=='yes'){
			if($date=='today')
				$items = $pros->findAll($where.'  and postdt>=curdate() and commission_rate=-1');
			elseif($date=='all')
				$items = $pros->findAll($where.' and commission_rate=-1');
		}
		$this->items = $items;
		$this->itemCounts = count($items);
                
		foreach($items as $k=>$v){
			$iidarr[] = array(iid=>$v['iid']);
		}	
				
		$this->iidarr = $iidarr;
		$this->yjdate = $date;			
		//$timestamp=time()."000";
		//$app_key = '12636285';
		//$secret = '63e664fafc1f3f03a7b8ad566c42819d';
		//$app_key = '21511111';
		//$secret = '4b7df3004e66b43f4632e2a85fe3f308';
		//$message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
		//$mysign=strtoupper(hash_hmac("md5",$message,$secret));
		//setcookie("timestamp",$timestamp);
		//setcookie("sign",$mysign);
		$this->progetCur = 1;
		$this->display("admin/proget.html");
	}
	
	public function updateyj(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		set_time_limit(0);
		// �ɼ�������
		ini_set('memory_limit', '64M'); // �ڴ泬��
		ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
		ini_set('pcre.recursion_limit', 99999); // ��Դ�������
		// end - �ɼ�������
        $date = $this->spArgs('date');        
		$pros = spClass('m_pro');
		$where = 'st<=curdate() and et>=curdate() and ischeck=1';
		if($date=='today')
			$items = $pros->findAll($where.' and postdt>=curdate() and commission_rate=-1');
		elseif($date=='all')
			$items = $pros->findAll($where.' and commission_rate=-1');
		
		foreach($items as $k=>$v){
			$yj = getCommissionRate($v['iid']);
			$itemTemp = array('commission_rate'=>$yj);
			$pros->update(array('iid'=>$v['iid']),$itemTemp);
		}	 
		$this->display("admin/uzcaiji.html");
		header("Location:/proget.html");
	}
	
	// �ɼ�
	public function uzcaijiapi(){
		set_time_limit(0);
		// �ɼ�������
		ini_set('memory_limit', '64M'); // �ڴ泬��
		ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
		ini_set('pcre.recursion_limit', 99999); // ��Դ�������
		// end - �ɼ�������
		
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
			
		$type = $this->spArgs('type');
		$actType = $this->website[$type]['actType'];
		
		
		// �ɼ��ӿ����
		if($actType){
			if($actType == 15){
				$caijiarr = array();
				for($page=1;$page<=1;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 4 || $actType == 11){ 
				//$pages = $xiaiCaiji->Caiji($type,'',3);
				//$pages = @ceil($pages/45);
				
				$caijiarr = array();
				for($page=1;$page<=2;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 10 || $actType == 16){ // ��ɱͨ�ɼ�5ҳ
				$caijiarr = array();
				for($page=1;$page<=5;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 20){
				$dateTemp = date("Y-m-d",time()-3*24*60*60);
				$pros = spClass("m_pro");
				$data = $pros->findAll('act_from=20 and postdt>='.$dateTemp);
//				var_dump($data);
				foreach($data as $k =>$v){
					$all[] = array('iid'=>$v['iid'],'nprice'=>$v['nprice'],'pic'=>$v['pic']);
				}
				$jsonData['all'] = $all;
				echo json_encode($jsonData);
			}else{
				//echo $actType;
				$xiaiCaiji->Caiji($type,'',2);
			}
		}else{
			echo 'û��ѡ��ɼ�վ��!';
		}
			
		//$this->website = $website;
		$this->display("admin/uzcaiji.html");
	}
	
	public function getitems($items,$actType){
		import('tbapi.php');
		$pros = spClass("m_pro");
		$catmaps = spClass("m_catmap");
		foreach($items as $k=>$iv){
			foreach($iv as $v){
				//echo $v['iid'].'<br/>';
				$item = getItemDetail($v['iid']);
//                                var_dump($item);
                                if($item<0){
                                    echo $v['iid'].' ��ȡ��Ϣʧ��!<br/>';
                                }else{
//                                    echo $v['iid'].' ��ȡ��ϢCG!<br/>';
                                    // �ּ�  && ͼƬ
                                    if($v['nprice'])
                                        $item['nprice'] = $v['nprice'];
                                    if($v['pic'])
                                        $item['pic'] = $v['pic'];
                                    // end - �ּ�  && ͼƬ

                                    // �ݹ�ȡ���Ա������ڵ�
                                    if($GLOBALS['G_SP']['autocat']){
//                                        $pcid = getPcidNew($item['iid']);

                                        // ��ѯfstk_catmap��Ӧ��Ŀ
                                        $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                                        //var_dump($catMap);
                                        if($catMap){ //�����Ʒ��Ŀ��ӳ��
                                                $item['cat'] = (int)$catMap['type'];
                                        }else{
                                                $item['cat'] = 42;
                                        }
                                        // end - ��ѯfstk_catmap��Ӧ��Ŀ
                                    }
                                    // end - �ݹ�ȡ���Ա������ڵ�

                                    if($v['cat'])
                                        $item['cat'] = $v['cat'];

                                    // �ַ�ת��
                                    $item['title'] = iconv('utf-8','gb2312',$item['title']);
                                    $item['title'] = preg_replace('/��.+?��/i','',$item['title']);
                                    $item['nick'] = iconv('utf-8','gb2312',$item['nick']);
                                    $item['ww'] = iconv('utf-8','gb2312',$item['ww']);
                                    $item['shopname'] = iconv('utf-8','gb2312',$item['shopname']);
                                    // end - �ַ�ת��

                                    if($actType)
                                            $item['act_from'] = $actType;
                                    else
                                            $item['act_from'] = 1;
                                    $item['last_modify'] = date("Y-m-d H:i:s");
//                                    $item['volume'] = getvolume($v['iid'],$item['shopshow']);
                                  
                                    //var_dump($item);
                                    if(!$pros->find(array('iid'=>$v['iid']))){ //û�ҵ�
                                            $item['postdt'] = date("Y-m-d H:i:s");

                                            if(!$pros->create($item)){
                                                    echo $v['iid'].' ���ʧ��,���ݿ����ʧ��!<br/>';
                                            }else{
                                                    //$this->upyjscript($v['iid'],$actType);
                                                    //$this->updateyjPhp($v['iid']);
                                                    echo $v['iid'].' ��ӳɹ�!<br/>';
                                            }
                                    }else{
                                            unset($item['act_from']);
                                            unset($item['rank']);
                                            unset($item['cat']);
                                            unset($item['pic']);
                                            unset($item['link']);
                                            unset($item['ischeck']);
                                            //$item['et'] = date("Y-m-d",86400*7+time());
                                            //$itemPostdt = $pros->find(array('iid'=>$v['iid']));
                                            //$item['postdt'] = $itemPostdt['postdt'];

                                            if(!$pros->update(array('iid'=>$v['iid']),$item)){
                                                    echo $v['iid'].' ����ʧ��,���ݿ����ʧ��!<br/>';
                                            }else{
                                                    //$this->upyjscript($v['iid'],$actType);
                                                    //$this->updateyjPhp($v['iid']);
                                                    echo $v['iid'].' ���³ɹ�!<br/>';
                                            }

                                    }
				}	
				
			}
		}
		$this->display("admin/uzcaiji.html");
	}
	
	
	// һ���ɼ�
	public function yjuzcaiji(){
//            set_time_limit(0);
            // �ɼ�������
//            ini_set('memory_limit', '64M'); // �ڴ泬��
//            ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
//            ini_set('pcre.recursion_limit', 99999); // ��Դ�������
            // end - �ɼ�������
            $file = "./tmp/output"; 
//            $file = "/var/log/messages"; 
            $lastpos = 0;  
//            exec("rm -f ".$file);
//            exec("uzcaiji.sh >> ".$file." &");
            while(true){  
                echo tail($file,$lastpos);  
            } 
	}
        
        //�Կ���Ʒ�ɼ�
        public function tkitemscaiji(){
            set_time_limit(0);
              
            // �ɼ�������
            ini_set('memory_limit', '64M'); // �ڴ泬��
            ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
            ini_set('pcre.recursion_limit', 99999); // ��Դ�������
            // end - �ɼ�������
            import("tbapi.php");
//            var_dump(get_TbkItems($this->spArgs("page")));
            $items = get_TbkItems($this->spArgs("page"),$this->spArgs("caijicat"));
            foreach($items['n_tbk_item'] as $v){
                $tkitem[] = array('iid'=>$v['num_iid'],'cat'=>$this->spArgs("cat"));
            }
            $tkitems['all'] = $tkitem;
//            var_dump($tkitems);
            $this->getitems($tkitems,'');      
        }
        
	// uz�ɼ�
	public function uzcaiji($type=null){
		
//		if(!$_SESSION['admin'])
//			header("Location:/login.html");
		
		set_time_limit(0);
              
                
		// �ɼ�������
		ini_set('memory_limit', '64M'); // �ڴ泬��
		ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
		ini_set('pcre.recursion_limit', 99999); // ��Դ�������
		// end - �ɼ�������
		
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
				
		//�ɼ�
		if(!$type)
			$type = $this->spArgs('type');
		
			
		$actType = $this->website[$type]['actType'];
		$pros = spClass("m_pro");
		/* $catmaps = spClass("m_catmap");
		import('tbapi.php'); */
		//echo $caiji.'<br/>';
//                elseif($actType == 11 || $actType == 4){ // ��Ƥ  && �ſ���  
//				//$pages = $xiaiCaiji->Caiji($type,'',3);
//				//$pages = @ceil($pages/45);
//				$pages = 2;
//				for($page=1;$page<=$pages;$page++){
//					$xiaiCaiji->Caiji($type,$page);
//					$items = $xiaiCaiji->getitems();
//					//var_dump($items);
//					$this->getitems($items, $actType);
//				}
//			}
		if($actType && $GLOBALS['G_SP']['autocat']){
			if($actType == 15){ // ׬��
                                 
                            for($page=1;$page<=1;$page++){
                               //$xiaiCaiji->Caiji($type,$page);
                               $xiaiCaiji->Caiji($type);
                               $items = $xiaiCaiji->getitems();
                               $this->getitems($items, $actType);
                           }
                                
			}elseif($actType == 10 || $actType == 16){ // ��ɱͨ,�ؼ۷����ɼ�5ҳ
				for($page=1;$page<=5;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$items = $xiaiCaiji->getitems();
					$this->getitems($items, $actType);
				}
			}elseif($actType == 20){
				$dateTemp = date("Y-m-d",time()-3*24*60*60);
				$pros->runSql('update fstk_pro set postdt=curdate() where act_from=20 and postdt>='.$dateTemp);
			}else{
				$xiaiCaiji->Caiji($type);
				$items = $xiaiCaiji->getitems();
				//var_dump($items);
				$this->getitems($items, $actType);
			}
		}else{
                    if(!$GLOBALS['G_SP']['autocat'] && $actType){
                        if($actType == 20){
                            $dateTemp = date("Y-m-d",time()-3*24*60*60);
                            $pros->runSql('update fstk_pro set postdt=curdate() where act_from=20 and postdt>='.$dateTemp);
			}
                        $xiaiCaiji->Caiji($type);
                        $items = $xiaiCaiji->getitems();
                        //var_dump($items);
                        $this->getitems($items, $actType);
                    }else
			echo 'û��ѡ��ɼ�վ��!';
		}
                $this->todayPros = $pros->spCache(-1)->findCount('st<=curdate() and et>=curdate() and postdt>=curdate()');
		//$this->website = $website;
		$this->display("admin/uzcaiji.html");
	}
	public function postDataToUzPhp($item,$uz){
//		var_dump($item);
		if($uz=='admin'){
			$url = 'http://yinxiang.uz.taobao.com/d/getdata';
		}elseif($uz=='cong'){
			$url = 'http://zhekouba.uz.taobao.com/d/getdata';
		}else{
			$url = 'http://'.$uz.'.uz.taobao.com/d/getdata';
		}
                $item['link'] = 'http://item.taobao.com/item.htm?id='.$item['iid'];
                if(!$item[del])
                    $contents = "pic=$item[pic]&&cat=$item[cat]&&iid=$item[iid]&&oprice=$item[oprice]&&nprice=$item[nprice]&&st=$item[st]&&et=$item[et]&&act_from=$item[classification]&&rank=$item[rank]&&title=$item[title]&&link=$item[link]&&slink=$item[slink]&&volume=$item[volume]&&postdt=$item[postdt]&&xujf=$item[xujf]&&remark=$item[remark]&&type=$item[type]&&content=$item[content]&&zk=$item[zk]&&carriage=$item[carriage]&&commission_rate=$item[commission_rate]&&ischeck=$item[ischeck]&&last_modify=$item[last_modify]&&ww=$item[ww]&&shopshow=$item[shopshow]&&shopv=$item[shopv]";
		else
                    $contents = "iid=$item[iid]&&del=$item[del]";
                $opts = array(
			'http'=>array(
					'method'=>"POST",
					'content'=>$contents,
					'timeout'=>900,
                                        'proxy'=>'tcp://222.88.236.235:80',
                                        'request_fulluri' => true
			));
//		echo $contents.'<br />';
		$context = stream_context_create($opts);
		
		$html = @file_get_contents($url, false, $context);
                echo $html;
                if($uz=='admin'){
                    $url = 'http://yinxiang.ai.taobao.com/d/getdata';
                    $html = @file_get_contents($url, false, $context);
                    echo $html;
                }
		
	}
	
	public function postDataToUzScripts($q,$uz){
		echo '<script language="javascript">';
		echo '$(function(){';
		
		if($uz=='126789'){
			echo "$.post('http://".$uz.".uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'ak':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}elseif($uz=='admin'){
			echo "$.post('http://yinxiang.uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'ak':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}elseif($uz=='cong'){
			echo "$.post('http://zhekouba.uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'q':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}else{
			echo "$.post('http://".$uz.".uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'q':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}
		
		
		echo '});';	
		echo '</script>';
	}
	public function postDataToUz($mode='php'){//����POST���ݵ���վ
		set_time_limit(0);
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		header("Content-Type: text/html; charset=gbk");
		
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // �����Ԥ�����
		
		$control = spClass('m_control');
		$caiji_control = $control->find(array('type'=>1));
		if($caiji_control['isuse'])
			exit();
		else
			$control->update(array('type'=>1),array('isuse'=>1));
		
		
		//var_dump($control->find());
		$pros = spClass('m_pro');
		
		// һ��������������
		foreach($this->website as $k=>$v){
			if($k!='none'){
				if(COMISSIONRATESORT)
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate() and commission_rate>=5';
				else
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate()';
			}
			$items_zu['actfrom'.$v['actType']] = $pros->findAll($where,'commission_rate asc');//Ӷ���->����ϣ������ʱ��ͷ���������postdtʱ��Ϊnow(),
		}
		//var_dump($items_zu);
			
		foreach($items_zu as $k=>$iv){
			foreach($iv as $k=>$v){
				$itemsTemp[] = $v;
			}
		}
		
//		var_dump($itemsTemp);
		$itemsReal = $itemsTemp;
		
		if(count($itemsReal)>1000)
			$item_zu_tmp = array_chunk($itemsReal,1000);
		else 
			$item_zu_tmp[0] = $itemsReal;
		
		//var_dump($item_zu_tmp);
		//$items = $pros->findAll($where); //,'commission_rate asc'
		foreach($item_zu_tmp as $k=>$iv){
			foreach($iv as $k=>$v){
				if(is_numeric($v['iid'])){
					if(empty($v['phone']))
						$v['phone'] = '123456789';
					$v['rank'] = 500;
					if(!$v['shopshow'])
						$v['shopshow']=1;
					if(!$v['shopv'])
						$v['shopv']=0;
					$v['title'] = preg_replace('/��.+?��/i','',$v['title']);
					if($_SESSION['iscaijiuser']=='zhe800w' || $_SESSION['iscaijiuser']=='55128' || $_SESSION['iscaijiuser']=='haowo' || $_SESSION['iscaijiuser']=='xinxin' || $_SESSION['iscaijiuser']=='cong' || $_SESSION['iscaijiuser']=='lijie' || $_SESSION['iscaijiuser']=='x0123' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='xx0123' || $_SESSION['iscaijiuser']=='ugou'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif($v['nprice']>20 && $v['nprice']<30)
							$v['type'] = 5;
						elseif($v['nprice']>30 && $v['nprice']<40)
							$v['type'] = 7;
						elseif($v['nprice']>=40)
							$v['type'] = 4;
					}elseif($_SESSION['iscaijiuser']=='shiyonglianmeng'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif(($v['nprice']/$v['oprice'])*10<=3)
							$v['type'] = 6;
						else
							$v['type'] = 8;

					}
					if($_SESSION['iscaijiuser']=='ifengqiang' || $_SESSION['iscaijiuser']=='9kuaigou'){
						if($v['nprice']<10)
							$v['act_from'] = 2;
						else
							$v['act_from'] = 3;
					}else{
						$v['act_from'] = 1;
					}
					if($_SESSION['iscaijiuser']=='cong'){
						$v['act_from'] = 3;
					}
					if($_SESSION['iscaijiuser']=='9kuaigou'){
						if($v['cat']==27)
							$v['cat']=22;
						$sqlout_sec .= $sqlout_fir." ('".$v['title']."','".$v['oprice']."','".$v['nprice']."','".$v['pic']."','".$v['st']."','".$v['et']."','".$v['type']."','".$v['cat']."','".$v['ischeck']."','http://item.taobao.com/item.htm?id=".$v['iid']."','".$v['rank']."','".$v['num']."','".$v['slink']."','".$v['ww']."','".$v['snum']."','".$v['xujf']."','".date("Y-m-d H:i:s")."','".$v['zk']."','".$v['iid']."','".$v['volume']."','".$v['content']."','".$v['remark']."','".$v['nick']."','".$v['reason']."','".$v['carriage']."','".$v['commission_rate']."','".date("Y-m-d H:i:s")."','".$v['click_num']."','".$v['phone']."','".$v['act_from']."','".$v['shopshow']."','".$v['shopv']."')  ON DUPLICATE KEY UPDATE last_modify=now(),cat=".$v['cat'].",et='".$v['et']."',commission_rate=".$v['commission_rate'].";";
					}
					else
						$sqlout_sec .= $sqlout_fir." ('".$v['title']."','".$v['oprice']."','".$v['nprice']."','".$v['pic']."','".$v['st']."','".$v['et']."','".$v['type']."','".$v['cat']."','".$v['ischeck']."','http://item.taobao.com/item.htm?id=".$v['iid']."','".$v['rank']."','".$v['num']."','".$v['slink']."','".$v['ww']."','".$v['snum']."','".$v['xujf']."','".date("Y-m-d H:i:s")."','".$v['zk']."','".$v['iid']."','".$v['volume']."','".$v['content']."','".$v['remark']."','".$v['nick']."','".$v['reason']."','".$v['carriage']."','".$v['commission_rate']."','".date("Y-m-d H:i:s")."','".$v['click_num']."','".$v['phone']."','".$v['act_from']."','".$v['shopshow']."','".$v['shopv']."')  ON DUPLICATE KEY UPDATE last_modify=now(),cat=".$v['cat'].",et='".$v['et']."',commission_rate=".$v['commission_rate'].";";
					if($_SESSION['iscaijiuser']=='yuansu')
						$v['pic'] = preg_replace('/_310x310.jpg/i','',$v['pic']);

					if($_SESSION['iscaijiuser']){
						if($mode=='php')
							$this->postDataToUzPhp($v,$_SESSION['iscaijiuser']);
						else 
							$this->postDataToUzScripts($sqlout_sec,$_SESSION['iscaijiuser']);
					}else{
						if($mode=='php')
							$this->postDataToUzPhp($v,'admin');
						else 
							$this->postDataToUzScripts($sqlout_sec,'admin');
					}
					$sqlout_sec = null;
				}

			}
			echo date("H:i:s").'��ͣ';
			sleep(210);
			echo date("H:i:s").'����';
		}
		$control->update(array('type'=>1),array('isuse'=>0));
	}
	
	public function upyjscript($iid,$actType){
		echo '<script language="javascript">';
		echo "TOP.api('rest', 'get',{
		method:'taobao.taobaoke.widget.items.convert',
		fields:'commission_rate,promotion_price,volume',
		num_iids:$iid,
		
		},function(resp){
		if(resp.error_response){
				alert('taobao.taobaoke.widget.items.convert�ӿڻ�ȡ����ϢƷʧ��!'+resp.error_response.msg);
				//console.log($iid);
				return false;
		}else{
				//console.log($iid);
				var item = resp.taobaoke_items.taobaoke_item;
				//console.log(item[0].commission_rate / 100);
				var zk = item[0].commission_rate / 100;
				//console.log(zk);
				$.ajax({
				type:'get',
				url:'/updateyjonce.html',
				data:'zk='+zk+'&actType=$actType&iid=$iid',
				dataType:'text',
		});
		}
		})";
		
		echo '</script>';
	}
	
	// ����Ӷ����PHP��
	public function updateyjPhp($iid,$cookie=''){
                $pros = spClass('m_pro');
		$yj = $this->getCommissionRate($iid);
		$item['commission_rate'] = $yj;
		$pros->update(array('iid'=>$iid),$item);
			
	}
	public function updatevolume(){
		$pros = spClass('m_pro');
		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and volume=0 or volume=200';
		$items = $pros->findAll($where);
		foreach($items as $k=>$v){
			$volume = getvolume($v['iid'],$v['shopshow']);
			if($volume>=0){
				$itemTemp = array('volume'=>$volume);
				if($pros->update(array('iid'=>$v['iid']),$itemTemp))
					echo '���³ɹ�.<br />';
				else
					echo '����ʧ��.<br />';
			}else{
				echo '��ȡʧ��.<br />';
			}
			
		}
	}
        public function updatecat(){
                $pros = spClass('m_pro');
                $catmaps = spClass("m_catmap");
                import('tbapi.php');
		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and cat=42 and act_from!=43';
		$items = $pros->findAll($where,'','','100');
		foreach($items as $k=>$v){
                    $pcid = getPcidNew($v['iid']);
                    // ��ѯfstk_catmap��Ӧ��Ŀ
                    $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                    //var_dump($catMap);
                    if($catMap){ //�����Ʒ��Ŀ��ӳ��
                        $itemTemp = array('cat'=>(int)$catMap['type']);
                    }else{
                        $itemTemp = array('cat'=>43);
                    }
                    if($pros->update(array('iid'=>$v['iid']),$itemTemp))
                        echo $v['iid'].' �ӷ���'.$v['cat'].'���·��ൽ '.$itemTemp['cat'].' �ɹ�.<br />';
                    else
                        echo $v['iid'].' �ӷ���'.$v['cat'].'���·��ൽ '.$itemTemp['cat'].' ʧ��.<br />';
		}
	}
        public function updatetags(){
            $pros = spClass('m_pro');
            $where = 'st<=curdate() and et>=curdate() and ischeck=1';
            $items = $pros->findAll($where,'','','100');
            foreach($items as $k=>$v){
                if(!$v['tags']){
                    echo $v['tags'].' û����ȡ��ǩ <br/>';
                }
            }
        }
        public function updateshopname(){
            set_time_limit(0);
            
            // �ɼ�������
            ini_set('memory_limit', '64M'); // �ڴ泬��
            ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
            ini_set('pcre.recursion_limit', 99999); // ��Դ�������
            // end - �ɼ�������
            
            //����slink
            $pros = spClass('m_pro');
            import('tbapi.php');
            $where = 'slink=""';
            $items = $pros->findAll($where);
            if(!empty($items)){
                foreach($items as $k=>$v){
                    $iteminfo = getItemDetail($v['iid']);
                    if($iteminfo<0){
                        echo $v['iid'].' ��ȡ��Ϣʧ��!<br/>';
                    }else{
                        $itemTemp['slink'] = $iteminfo['slink'];
                        if($pros->update(array('iid'=>$v['iid']),$itemTemp))
                            echo $v['iid'].' ����slinkΪ'.$itemTemp['slink'].' �ɹ�.<br />';
                        else
                            echo $v['iid'].' ����slinkΪ'.$itemTemp['slink'].' ʧ��.<br />';
                    }
                }
            }else{
                echo 'slink������£�';
            }
            //����shopname,ww,nick
            $where = 'ww="" or nick="" or shopname=""';
            $items = $pros->findAll($where);
            if(!empty($items)){
                foreach($items as $k=>$v){
                    $iteminfo = getItemDetail($v['iid']);
                    if($iteminfo<0){
                        echo $v['iid'].' ��ȡ��Ϣʧ��!<br/>';
                    }else{
                        $itemTemp['ww'] = iconv('utf-8','gbk',$iteminfo['ww']);
                        $itemTemp['nick'] = iconv('utf-8','gbk',$iteminfo['nick']);
                        $itemTemp['shopname'] = iconv('utf-8','gbk',$iteminfo['shopname']);
                        if($pros->update(array('iid'=>$v['iid']),$itemTemp))
                            echo $v['iid'].' ����shopnameΪ'.$itemTemp['shopname'].' �ɹ�.<br />';
                        else
                            echo $v['iid'].' ����shopnameΪ'.$itemTemp['shopname'].' �ɹ�.<br />';
                    }
                }
            }else{
                echo 'shopname,ww,nick������£�';
            }
        }
	// ����Ӷ����
	public function updateyjonce(){
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass('m_pro');
		$yj = $this->spArgs('zk');
		$iid = $this->spArgs('iid');
		$item['commission_rate'] = $yj;
		//echo $iid.' Ӷ��'.$yj.'<br/>';
		if($iid && $yj){
			$pros->update(array('iid'=>$iid),$item);
		}
		$this->display("admin/uzcaiji.html");
	}
	// END ����Ӷ����
	// ���ݵ���
	public function dbselect(){
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
			
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // �����Ԥ�����
		
		
		$pros = spClass('m_pro');
		
		// һ���������
		if(SETAJAXTOUZ){
			$control = spClass("m_control");
			$caiji_control = $control->find(array('type'=>1));
			$this->caijiisuse = $caiji_control['isuse'];
		}else{
			$this->caijiisuse = 1;
		}
		
		// SQL�ļ��������
		if(SETFILETOUZ){
			$control = spClass("m_control");
			$getsql_control = $control->find(array('type'=>2));
			$this->getsqlisuse = $getsql_control['isuse'];
		}
		
		// ����ɼ�������
//		if(COMISSIONRATESORT){
//			$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
//		}
//		else{    
//			$where = $baseSql.' and postdt>=curdate()';
//		}
		
		
		// ����ƽ̨�����ݵ�����
		$type = $this->spArgs('type');
		$actfrom = $this->website[$this->spArgs('type')]['actType'];
		if($actfrom){ // ÿ��ƽ̨ѡ��			
			$page = $this->spArgs('page',1);
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// ��Ա��,VIP�ػݣ�VIP���Żݲ�����Ӷ��
				$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			else{
				if(COMISSIONRATESORT)
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()  and commission_rate>=5';
				else
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			}
		}else{ // ȫ��
			$page = $this->spArgs('page',1);
			if(COMISSIONRATESORT)
				$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
			else
				$where = $baseSql.' and postdt>=curdate()';
		}
		
		$itemsTemp = $pros->spPager($page,50)->findAll($where);
		
		
		// �ɼ��û�����Ϣ
		if($_SESSION['iscaijiuser']){
			$this->iscaijiuser = $_SESSION['iscaijiuser'];
			$this->username = $this->caijiusers[$_SESSION['iscaijiuser']]['nick'];
		}
		
		$this->type = $type;
		$this->actfrom = $actfrom;
		$this->pager = $pros->spPager()->getPager();
        $this->todayoutsql = $filename = $_SESSION['iscaijiuser'].'-'.date("Y-m-d").'.sql';        
        $this->dbselectCur = 1;
		$this->display("admin/dbselect.html");
	}
	
	public function sqloutone(){
		$iid = $this->spArgs('iid');
		$pros = spClass('m_pro');
		$type = $this->spArgs('type');
		$rank = $this->spArgs('rank');
		$v = $pros->find(array('iid'=>$iid));
		if($type){
			$v[type] = $type;
			$v[rank] = $rank;
		}
		//var_dump($item);
		$sqlout_fir = "INSERT INTO `fstk_pro` (`title`, `oprice`, `nprice`, `pic`, `st`, `et`, `type`, `cat`, `ischeck`, `link`, `rank`, `num`, `slink`, `ww`, `snum`, `xujf`, `postdt`, `zk`, `iid`, `volume`, `content`, `remark`, `nick`, `reason`, `carriage`, `commission_rate`, `last_modify`, `click_num`, `phone`, `act_from`,`shopshow`,`shopv`) VALUES ";
		$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',pic='.$v['pic'].',et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
		echo $sqlout_sec;
		
	}
	
	//�����������ݴ洢Ϊsql�ļ�
	public function savesqltouz(){
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		$filename = $_SESSION['iscaijiuser'].date("Y-m-d");
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // �����Ԥ�����
	
		$pros = spClass('m_pro');
		
		// ��������
		$control = spClass('m_control');
		$getsql_control = $control->find(array('type'=>2));
		if($getsql_control['isuse'])
			exit();
		else
			$control->update(array('type'=>2),array('isuse'=>1));
		
		// һ��������������
		foreach($this->website as $k=>$v){
			if($k!='none'){
				if(COMISSIONRATESORT)
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate() and commission_rate>=5';
				else
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate()';
			}
			$items_zu['actfrom'.$v['actType']] = $pros->findAll($where,'commission_rate asc');//Ӷ���->����ϣ������ʱ��ͷ���������postdtʱ��Ϊnow(),
			//echo $pros->dumpSql().'<br/>';
		}
		//var_dump($items_zu);
			
		foreach($items_zu as $k=>$iv){
			foreach($iv as $k=>$v){
				$itemsTemp[] = $v;
			}
		}
		
		$itemsReal = $itemsTemp;
		
		if(count($itemsReal)>2500)
			$item_zu_tmp = array_chunk($itemsReal,2500);
		else 
			$item_zu_tmp[0] = $itemsReal;
		
		header("Content-Type:text/html;charset=UTF-8");
		
		$sqlout_fir = 'INSERT INTO fstk_pro(title,oprice,nprice,pic,st,et,type,cat,ischeck,link,rank,num,slink,ww,snum,xujf,postdt,zk,iid,volume,content,remark,nick,reason,carriage,commission_rate,last_modify,click_num,phone,act_from,shopshow,shopv) VALUES ';
		
		//����ļ���
		$datalist=list_dir('./tmp/sqlout/');
		foreach($datalist as $k=>$val){   
			unlink($val);
		}   
			
		foreach($item_zu_tmp as $k=>$iv){
			$i += 1;
			$file = fopen('./tmp/sqlout/'.$filename.'-part'.$i.'.sql',"w+");
			fclose($file);
			foreach($iv as $k=>$v){
				if(is_numeric($v['iid'])){
					if(empty($v['phone']))
						$v['phone'] = '123456789';
					$v['rank'] = 500;
					if(!$v['shopshow'])
						$v['shopshow']=1;
					if(!$v['shopv'])
						$v['shopv']=0;
					$v['title'] = preg_replace('/��.+?��/i','',$v['title']);
					if($_SESSION['iscaijiuser']=='jumei' || $_SESSION['iscaijiuser']=='tiangou' || $_SESSION['iscaijiuser']=='haowo' || $_SESSION['iscaijiuser']=='xinxin' || $_SESSION['iscaijiuser']=='cong' || $_SESSION['iscaijiuser']=='lijie' || $_SESSION['iscaijiuser']=='x0123' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='xx0123' || $_SESSION['iscaijiuser']=='ugou'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif($v['nprice']>20 && $v['nprice']<30)
							$v['type'] = 5;
						elseif($v['nprice']>30 && $v['nprice']<40)
							$v['type'] = 7;
						elseif($v['nprice']>=40)
							$v['type'] = 4;
					}elseif($_SESSION['iscaijiuser']=='shiyonglianmeng'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif(($v['nprice']/$v['oprice'])*10<=3)
							$v['type'] = 6;
						else
							$v['type'] = 8;

					}
					if($_SESSION['iscaijiuser']=='ifengqiang' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='chuang'){
						if($v['nprice']<10)
							$v['act_from'] = 2;
						else
							$v['act_from'] = 3;
					}else{
						$v['act_from'] = 1;
					}
					if($_SESSION['iscaijiuser']=='cong'){
						$v['act_from'] = 3;
					}
					$sqlout_sec = $sqlout_fir.' ("'.$v["title"].'","'.$v["oprice"].'","'.$v["nprice"].'","'.$v["pic"].'","'.$v["st"].'","'.$v["et"].'","'.$v["type"].'","'.$v["cat"].'","'.$v["ischeck"].'","http://item.taobao.com/item.htm?id='.$v["iid"].'","'.$v["rank"].'","'.$v["num"].'","'.$v["slink"].'","'.$v["ww"].'","'.$v["snum"].'","'.$v["xujf"].'","'.date("Y-m-d H:i:s").'","'.$v["zk"].'","'.$v["iid"].'","'.$v["volume"].'","'.$v["content"].'","'.$v["remark"].'","'.$v["nick"].'","'.$v["reason"].'","'.$v["carriage"].'","'.$v["commission_rate"].'","'.date("Y-m-d H:i:s").'","'.$v["click_num"].'","'.$v["phone"].'","'.$v["act_from"].'","'.$v["shopshow"].'","'.$v["shopv"].'")  ON DUPLICATE KEY UPDATE last_modify=now(),et="'.$v["et"].'",commission_rate="'.$v["commission_rate"].'";';
					
                                        //echo $sqlout_sec;
					$file = fopen('./tmp/sqlout/'.$filename.'-part'.$i.'.sql',"a+");
					if(!$file)
                                            echo '�ļ���ʧ��';
					//echo $sqlout_sec.'<br />';
					fwrite($file,iconv('gbk','utf-8',$sqlout_sec."\n"));
//                                        fwrite($file,substr($sqlout_sec,0,-1));
					$sqlout_sec = null;
				}
			}
			fclose($file);
		}
		
		//��ȡ�б� 
		$datalist=list_dir('./tmp/sqlout/');
		//var_dump($datalist);
		$zipfilename = "./tmp/".$filename.".zip"; //�������ɵ��ļ�������·����   
		unlink($zipfilename);
		if(!file_exists($zipfilename)){   
			//���������ļ�   
			$zip = new ZipArchive();//ʹ�ñ��࣬linux�迪��zlib��windows��ȡ��php_zip.dllǰ��ע��   
			if ($zip->open($zipfilename, ZIPARCHIVE::CREATE)!==TRUE) {   
				exit('�޷����ļ��������ļ�����ʧ��');
			}   
			foreach($datalist as $k=>$val){   
				if(file_exists($val)){   
					$zip->addFile($val,basename($val));//�ڶ��������Ƿ���ѹ�����е��ļ����ƣ�����ļ����ܻ����ظ�������Ҫע��һ��   
				}   
			}   
			$zip->close();//�ر�   
		}   
		if(!file_exists($zipfilename)){   
			exit("�޷��ҵ��ļ�"); //��ʹ���������п���ʧ�ܡ�������   
		}   
		header("Cache-Control: public"); 
		header("Content-Description: File Transfer"); 
		header('Content-disposition: attachment; filename='.basename($zipfilename)); //�ļ���   
		header("Content-Type: application/zip"); //zip��ʽ��   
//		header('Content-disposition: attachment; filename='.basename('./tmp/sqlout/'.$filename.'-part1.sql')); //�ļ���   
//		header("Content-Type: application/text"); //text��ʽ�� 
		header("Content-Transfer-Encoding: binary"); //��������������Ƕ������ļ�    
		header('Content-Length: '. filesize($zipfilename)); //������������ļ���С   
//		header('Content-Length: '. filesize('./tmp/sqlout/'.$filename.'-part1.sql')); //������������ļ���С   
		@readfile($zipfilename); 
//		@readfile('./tmp/sqlout/'.$filename.'-part1.sql');
		$control->update(array('type'=>2),array('isuse'=>0));
	}
	
	// ���ݵ���
	public function sqlout(){
		
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		
		$actfrom =	$this->website[$this->spArgs('type')]['actType'];
		$pros = spClass('m_pro');
		//echo $actfrom.'best<br/>';
		if($actfrom){		
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// ��Ա��,VIP�ػݣ�VIP���Żݲ�����Ӷ��
				$where = 'act_from='.$actfrom.' and st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
			else{
				 if(COMISSIONRATESORT)
					 $where = 'act_from='.$actfrom.' and st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate() and commission_rate>=5';
				 else
					 $where = 'act_from='.$actfrom.' and st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
			}
                            
		}else{	
			if(COMISSIONRATESORT)
				$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate() and commission_rate>=5';  
			else
				$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
		}
		$page = $this->spArgs('page',1);
		if($_SESSION['iscaijiuser']=='yuansu')// ������ӿ�
			$sqlout_fir = "INSERT INTO `items` (`iid`,`title`,`picurl`,`itemurl`,`price`,`prom`,`nick`,`categoryid`,`partid`,`status`,`top`,`gg`,`report`,`freeshipping`,`stock`,`sorts`,`starttime`,`endtime`) VALUES ";
		else // fstk_���ݿ�����
			$sqlout_fir = "INSERT INTO `fstk_pro` (`title`, `oprice`, `nprice`, `pic`, `st`, `et`, `type`, `cat`, `ischeck`, `link`, `rank`, `num`, `slink`, `ww`, `snum`, `xujf`, `postdt`, `zk`, `iid`, `volume`, `content`, `remark`, `nick`, `reason`, `carriage`, `commission_rate`, `last_modify`, `click_num`, `phone`, `act_from`,`shopshow`,`shopv`) VALUES ";
		//echo $where.'<br/>';
		if(COMISSIONRATESORT)
			$items = $pros->spPager($page,50)->findAll($where,'commission_rate asc');
		else
			 $items = $pros->spPager($page,50)->findAll($where);
		foreach($items as $k=>$v){  
			if(is_numeric($v['iid'])){
				if(empty($v['phone']))
					$v['phone'] = '123456789';
				$v['rank'] = 500;
				$v['title'] = preg_replace('/��.+?��/i','',$v['title']);
				if($_SESSION['iscaijiuser']=='jumei' || $_SESSION['iscaijiuser']=='55128' || $_SESSION['iscaijiuser']=='tiangou' || $_SESSION['iscaijiuser']=='xinxin' || $_SESSION['iscaijiuser']=='cong' || $_SESSION['iscaijiuser']=='lijie' || $_SESSION['iscaijiuser']=='x0123' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='xx0123' || $_SESSION['iscaijiuser']=='ugou'){
					if($v['nprice']<10)
						$v['type'] = 2;
					elseif($v['nprice']>10 && $v['nprice']<20)
						$v['type'] = 3;
					elseif($v['nprice']>20 && $v['nprice']<30)
						$v['type'] = 5;
					elseif($v['nprice']>30 && $v['nprice']<40)
						$v['type'] = 7;
					elseif($v['nprice']>=40)
						$v['type'] = 4;
				}elseif($_SESSION['iscaijiuser']=='shiyonglianmeng'){
					if($v['nprice']<10)
						$v['type'] = 2;
					elseif($v['nprice']>10 && $v['nprice']<20)
						$v['type'] = 3;
					elseif(($v['nprice']/$v['oprice'])*10<=3)
						$v['type'] = 6;
					else 
						$v['type'] = 8;
					
				}
				if($_SESSION['iscaijiuser']=='ifengqiang' || $_SESSION['iscaijiuser']=='chuang' || $_SESSION['iscaijiuser']=='360tuan' || $_SESSION['iscaijiuser']=='tongqu' || $_SESSION['iscaijiuser']=='tbcsh' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='22888' || $_SESSION['iscaijiuser']=='282828' || $_SESSION['iscaijiuser']=='tblgj' || $_SESSION['iscaijiuser']=='tbypt'){
					if($v['nprice']<10)
						$v['act_from'] = 2;
					else 
						$v['act_from'] = 3;
				}else{
					$v['act_from'] = 1;
				}
				if($_SESSION['iscaijiuser']=='cong')
					$v['act_from'] = 3;
				if($_SESSION['iscaijiuser']=='9kuaigou'){ // �ſ鹺
					if($v['cat']==27)
						$v['cat']=22;
					$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',pic="'.$v['pic'].'",et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
				}else{
					if(in_array($_SESSION['iscaijiuser'],array('admin','jumei','cong','126789','tiangou')))
						$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
					else
						$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';

				}
				//echo $sqlout_sec;
			}
		}
		echo $sqlout_sec;
		$this->display("admin/uzcaiji.html");
	}
	
	// �Կͱ���
	public function tkreport(){ //�ϴ�������������ʾ
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		set_time_limit(0);
                ini_set("memory_limit","1024M");
		import('tbapi.php');
                import('PHPExcel.php');
                
                // �ϴ��ļ�
                if($this->spArgs("submit")){
                    import("func.php");
                    //����ļ���
                    $datalist=list_dir('./tmp/tkreport/');
                    foreach($datalist as $k=>$val){
                            unlink($val);
                    }
                    if ($_FILES["file"]["error"] > 0){
                        echo "Error: " . $_FILES["file"]["error"] . "<br />";
                    }else{
                        echo "Upload: " . $_FILES["file"]["name"] . "<br />";
                        echo "Type: " . $_FILES["file"]["type"] . "<br />";
                        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
                        echo "Stored in: data.xls" . "<br />";
                        if(file_exists("tmp/tkreport/data.xls")){
                            echo "data.xls already exists. ";
                        }else{
                            move_uploaded_file($_FILES["file"]["tmp_name"],"tmp/tkreport/data.xls");
                            echo "Stored in: " . "tmp/tkreport/data.xls<br/>";
                            fclose($fp);
                        }
                    }
                }
                
//                import("IOFactory.php");
		$input_file = "./tmp/tkreport/data.xls"; 
                $objPHPExcel = PHPExcel_IOFactory::load($input_file); 
                $sheetData = $objPHPExcel->getSheet(0)->toArray(null, true, true, false); 
                
                // ������
                for($i=0;$i<count($sheetData[0]);$i++){
                    $sheetData[0][$i] = iconv('utf-8','gbk',unicode2utf8($sheetData[0][$i]));
                }
//                $this->sheetDataHead = $sheetData[0];
                
                //�������
                for($i=1;$i<count($sheetData);$i++){
                    for($j=0;$j<count($sheetData[$i]);$j++){
                        if($j==1 || $j==3 || $j==10 || $sheetData[$i][$j] != 'PC')
                        $sheetData[$i][$j] = iconv('utf-8','gbk',unicode2utf8($sheetData[$i][$j]));
//                          echo $sheetData[$i][$j];
                    }
                }
                $en_letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t');
                $dataSheet = spClass("m_data_sheet"); 
                $dataSheet->runSql('truncate table data_sheet');
                
                //��������±�
                for($i=1;$i<count($sheetData);$i++){
                    for($j=0;$j<count($sheetData[$i]);$j++){
                       $sheetDataTemp[$en_letters[$j]] = $sheetData[$i][$j];
                    }
                    $dataSheet->create($sheetDataTemp);
                }
                $sheetData = $dataSheet->findSql('select b,f,g,h,k,q,sum(c) from data_sheet group by q order by h desc');
                $this->sheetData = $sheetData;
//                var_dump($sheetData);
                $this->tkreportCur = 1;
		$this->display("admin/tkreport.html");
	}
        
        public function paixu(){
            if(!$_SESSION['admin'])
                header("Location:/login.html");
            if($this->spArgs("paixu")){
                $rule_1['bishu'] = $this->spArgs("rule_1_bishu");
                $rule_1['rank'] = $this->spArgs("rule_1_rank");
                $rule_1['paiqian'] = $this->spArgs("rule_1_paiqian");
                $rules['rule_1'] = $rule_1;
                
                $rule_2['bishu'] = $this->spArgs("rule_2_bishu");
                $rule_2['rank'] = $this->spArgs("rule_2_rank");
                $rule_2['paiqian'] = $this->spArgs("rule_2_paiqian");
                $rules['rule_2'] = $rule_2;
                
                $dataSheet = spClass("m_data_sheet"); 
                $sheetData = $dataSheet->findSql('select b,f,g,h,k,q,sum(c),rank from data_sheet inner join fstk_pro on data_sheet.q=fstk_pro.iid group by q order by h desc');
                var_dump($rules);
                foreach($sheetData as $k=>$v){
                    echo $v['q'].'-----'.$v['sum(c)'].'-----'.$v['rank'].'<br />';
                }
            }
            $this->display("admin/paixu.html");
        }
		
}
?>
