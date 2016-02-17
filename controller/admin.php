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
		// postdt>=curdate()为当日添加，不包括更新
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
            //转换编码与页面编码相同
//            $seller_nick = iconv('utf-8','gb2312',$seller_nick);
            $shopinfo = spClass("m_ymfqzs")->find(array("shopww"=>iconv('utf-8','gb2312',$seller_nick)));
            
            if($shopinfo){
                header("Content-type: application/json");
                // 更新status
                if(spClass("m_ymfqzs")->update(array('shopww'=>iconv('utf-8','gb2312',$seller_nick)),array('status'=>(int)$status)))
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"更新成功！").'"}}';
                else
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"更新失败！").'"}}';
            }else{
                //插入新数据
                import("tbapi.php");
                $shop = getShopNew($seller_nick);
                // 字符转换
                //转换编码与页面编码相同
                $shop['shop_title'] = iconv('utf-8','gb2312',$shop['shop_title']);
                $shop['seller_nick'] = iconv('utf-8','gb2312',$shop['seller_nick']);
                header("Content-type: application/json");
                if(spClass("m_ymfqzs")->create(array('shopww'=>$shop['seller_nick'],'status'=>(int)$status,'shopid'=>$shop['user_id'],'shopname'=>$shop['shop_title'])))
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"新增成功！").'"}}';
                else
                    echo '{"ok":true,"data":{"message":"'.iconv('gb2312','utf-8',"新增失败！").'"}}';
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
			
		
		// 登录判断
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
				// 二次验证
				//$url_last = '.uz.taobao.com/view/front/getusernick.php';
				//$url = 'http://'.$_SESSION['iscaijiuser'].$url_last;
				// END - 二次验证
				if($_SESSION['iscaijiuser'])
					header("Location:/dbselect.html");
			} 
		}
		else{
			header("Location:/login.html");
		} 
		
		$this->display("admin/login.html");
	}
	
	
	// 获取单品信息
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
                    // 递归取得淘宝二级节点
                    if($GLOBALS['G_SP']['autocat']){
                        $pcid = getPcidNew($item['iid']);
//                        echo 'cid:'.$item['cid'];
//                        echo ',pcid:'.$pcid;

                        // 查询fstk_catmap对应类目
                        $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                        //var_dump($catMap);
                        if($catMap){ //如果商品类目有映射
                                $item['cat'] = (int)$catMap['type'];
                        }else{
                                $item['cat'] = 42;
                        }
//                        echo ',cat:'.$item['cat'];
                    }
                    // end - 递归取得淘宝二级节点


        //	    echo $pcid;
                    // end - 查询fstk_catmap对应类目

                    // 字符转换
                    $item['title'] = iconv('utf-8','gb2312',$item['title']);
                    $item['title'] = preg_replace('/【.+?】/i','',$item['title']);
                    $item['nick'] = iconv('utf-8','gb2312',$item['nick']);
                    $item['shopname'] = iconv('utf-8','gb2312',$item['shopname']);
//                    $item['volume'] = getvolume($iid,$item['shopshow']);
                    if(!$item['volume'])
                            $item['volume'] = -1;
                    
                    // end - 字符转换
                    //$item['sid'] = getShop($item['nick']);
                    //var_dump($item);
                    echo '{"iid":"'.$item['iid'].'","title":"'.$item['title'].'","slink":"'.$item['slink'].'","nick":"'.$item['nick'].'","shopname":"'.$item['shopname'].'","pic":"'.$item['pic'].'","oprice":"'.$item['oprice'].'","nprice":"'.$item['nprice'].'","st":"'.$item['st'].'","et":"'.$item['et'].'","cid":"'.$item['cid'].'","link":"'.$item['link'].'","rank":'.$item['rank'].',"postdt":"'.$item['postdt'].'","ischeck":'.$item['ischeck'].',"volume":'.$item['volume'].',"carriage":'.$item['carriage'].',"shopshow":'.$item['shopshow'].',"shopv":'.$item['shopv'].',"cat":'.$item['cat'].',"item_imgs":"'.$item['item_imgs'].'","commission_rate":'.$item['commission_rate'].'}';

                }
	}
        function yqtout(){
            // 文件下载
            header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header('Content-disposition: attachment; filename='.basename('./tmp/yqtdata/yqtout.txt')); //文件名   
            header("Content-Type: application/text"); //text格式的 
            header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件    
            header('Content-Length: '. filesize('./tmp/yqtdata/yqtout.txt')); //告诉浏览器，文件大小   
            @readfile('./tmp/yqtdata/yqtout.txt');
        }
        public function adenter($outs,$guanggao,$step){
            $outs_zu = array_chunk($outs,$step);
            foreach($guanggao as $k=>$v){
                if($k==0){//第一个广告位在尾部添加
                    foreach($outs_zu as &$iv){//尾巴追加广告位
                        array_push($iv,$v);
                    }
                }elseif($k<=4){//值替换
                    foreach($outs_zu as &$iv){
                        $iv[$k-1] = $v;
                    }
                }
            }
            // 数组重组 
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
            //清空文件夹
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
                echo 'tmp/yqtdata/yqtout.txt创建成功.<br />';
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
                echo '创建失败！';
            }
            fclose($fp);
            header("Location:/yqtout.html");
        }
	public function getCommissionRate($iid){
		if(getCommissionRate('38510058624')=='-2'){//cookie模拟登录失败
			if(loginTaobao('liushiyan8','liujun987'))//重新登录(验证码登录),更新cookie
				$this->loginalimama = 1;
			else
				$this->loginalimama = 0;
			
			if($this->loginalimama)//登录成功
				return getCommissionRate($iid);
			else
				return -2;
		}else{//cookie模拟登陆
			return getCommissionRate($iid);
		}
	}
	// 后台首页
	public function index(){
		ini_set('memory_limit','128M');
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass("m_pro");
               
                
		// 所有没有下架的商品统计
		$this->allPros = $pros->spCache(480)->findCount('st<=curdate() and et>=curdate()');
		
		// 当天提交的没有下架的商品统计
		$this->todayPros = $pros->spCache(480)->findCount('st<=curdate() and et>=curdate() and postdt>=curdate()');
		
		// 过期商品
		$this->guoqis = $pros->spCache(480)->findCount('et<curdate()');
		
		$this->indexCur = 1;
		$this->display("admin/index.html");
	}
	
	// 商品续期
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
	
	// 商品管理
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
	// 商品审核
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
                                    $mailsubject = '已通过审核！';
                                    echo '操作成功,商品已通过审核！';
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
					if($pros->update(array('id'=>$id),array('ischeck'=>2,'reason'=>'亲 '.$reason))){
                                                $mailsubject = '不通过审核！('.$reason.')';
						echo '操作成功,商品不通过审核！';
                                        }
				}else
					echo '操作失败,请填写备注！';
			}
                        
                        $mailbody = "<h1>您报名的商品</h1><br />"
                        . "<a target='_blank' href='".$pro[link]."'>".$pro[title]."</a><h2><span style='color:red'>".$mailsubject."</span></h2><br />"
                        . "联系QQ:350544519";
                
                        if($uemail){
                            import("email.class.php");
                            $smtpserver = "smtp.163.com";//SMTP服务器
                            $smtpserverport =25;//SMTP服务器端口
                            $smtpusermail = "yimiaofengqiang@163.com";//SMTP服务器的用户邮箱
                            $smtpuser = "yimiaofengqiang@163.com";//SMTP服务器的用户帐号
                            $smtppass = "z123456";//SMTP服务器的用户密码

                            $smtpemailto = $uemail;//发送给谁
                            $mailsubject = $mailsubject;//邮件主题
                            $mailbody = $mailbody;//邮件内容
                            $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
                            ##########################################
                            $smtp = spClass("smtp");
                            $smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
                            $smtp->debug = FALSE;//是否显示发送的调试信息
                            $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
                        }
                
//				header("Location:/pro/sh/no.html");
		}

                               
		$this->pro = $pro; 
		$this->display('admin/checkpro.html');
	}
	
	// 查找是否存在
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
	
	// 商品添加
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
			if($this->mode!='try'){// 促销商品添加
                                if($this->isInThere($item['iid'])){
					$submitTips = '商品已存在,请勿重复添加';
				}else{
					$art = $pros->create($item);
					if($art){	//修改成功后跳转
						$submitTips = '添加成功';
                                                if($GLOBALS['G_SP']['ajaxToUz']['addpro']){
                                                    $this->postDateToEachUz($item);
                                                }
//						header("Location:".$referUrl);
					}else
						$submitTips = '添加失败';
				}
			}else{// 试用商品添加
                                unset($item['classification']);
				if($this->isInThere($item['iid'],'try_items')){
					$submitTips = '试用商品已存在,请勿重复添加';
				}else{
					$item['istry'] = 1;
					$item['gailv'] = $_POST['gailv'];
					$art = $pros->create($item);
					if($art){	//修改成功后跳转
						$submitTips = '添加成功';
						header("Location:".$referUrl);
					}
					else
						$submitTips = '添加失败';
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
		// 商品类别
		$this->actfroms = $actfroms;
		$this->proCats = $proCats;
                $this->classifications = $classifications;
		// 提交提示
		$this->submitTips = $submitTips;
		$this->display("admin/addpro.html");
	}
	
	// 删除过期商品
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
	// 商品删除
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
//            echo "编码后输出<br />";
//            echo $itemEncode;
//            echo "解码后输出<br />";
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
	// 商品修改
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
			if($art){ // 修改成功后跳转
				$submitTips = '修改成功';
//                                var_dump($item);
                                if($GLOBALS['G_SP']['ajaxToUz']['modpro']){
                                    $this->postDateToEachUz($item);
                                }
//				if($this->mode!='try')
//					header("Location:".$referUrl);
//				else
//					header("Location:".$referUrl);
			}else
				$submitTips = '修改失败';
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
	
	// 用户管理
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
                                    $this->tips = "充值成功！,请刷新页面查看";
                            else
                                    $this->tips = "充值失败！";
                        }else{
                            $this->tips = "查不到该用户！";
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
	
	// 友链管理
	public function link(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//$pros = spClass("m_pro");
                $this->linkCur =1;
		$this->display("admin/link.html");
	}
	
	// 广告管理
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
                                    echo '修改成功';
                                else
                                    echo '修改失败';
                            }
                            break;
                        case 'del':
                            if($ads->delete(array('id'=>$id)))
                                echo '删除成功';
                            else
                                echo '删除失败';
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
                                    echo '添加成功';
                                else
                                    echo '添加失败';
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
                                    echo '修改成功';
                                else
                                    echo '修改失败';
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
	
	// 后台商品采集页
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
		// 采集开春哥
		ini_set('memory_limit', '64M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		// end - 采集开春哥
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
	
	// 采集
	public function uzcaijiapi(){
		set_time_limit(0);
		// 采集开春哥
		ini_set('memory_limit', '64M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		// end - 采集开春哥
		
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
			
		$type = $this->spArgs('type');
		$actType = $this->website[$type]['actType'];
		
		
		// 采集接口输出
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
			}elseif($actType == 10 || $actType == 16){ // 秒杀通采集5页
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
			echo '没有选择采集站点!';
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
                                    echo $v['iid'].' 获取信息失败!<br/>';
                                }else{
//                                    echo $v['iid'].' 获取信息CG!<br/>';
                                    // 现价  && 图片
                                    if($v['nprice'])
                                        $item['nprice'] = $v['nprice'];
                                    if($v['pic'])
                                        $item['pic'] = $v['pic'];
                                    // end - 现价  && 图片

                                    // 递归取得淘宝二级节点
                                    if($GLOBALS['G_SP']['autocat']){
//                                        $pcid = getPcidNew($item['iid']);

                                        // 查询fstk_catmap对应类目
                                        $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                                        //var_dump($catMap);
                                        if($catMap){ //如果商品类目有映射
                                                $item['cat'] = (int)$catMap['type'];
                                        }else{
                                                $item['cat'] = 42;
                                        }
                                        // end - 查询fstk_catmap对应类目
                                    }
                                    // end - 递归取得淘宝二级节点

                                    if($v['cat'])
                                        $item['cat'] = $v['cat'];

                                    // 字符转换
                                    $item['title'] = iconv('utf-8','gb2312',$item['title']);
                                    $item['title'] = preg_replace('/【.+?】/i','',$item['title']);
                                    $item['nick'] = iconv('utf-8','gb2312',$item['nick']);
                                    $item['ww'] = iconv('utf-8','gb2312',$item['ww']);
                                    $item['shopname'] = iconv('utf-8','gb2312',$item['shopname']);
                                    // end - 字符转换

                                    if($actType)
                                            $item['act_from'] = $actType;
                                    else
                                            $item['act_from'] = 1;
                                    $item['last_modify'] = date("Y-m-d H:i:s");
//                                    $item['volume'] = getvolume($v['iid'],$item['shopshow']);
                                  
                                    //var_dump($item);
                                    if(!$pros->find(array('iid'=>$v['iid']))){ //没找到
                                            $item['postdt'] = date("Y-m-d H:i:s");

                                            if(!$pros->create($item)){
                                                    echo $v['iid'].' 添加失败,数据库操作失败!<br/>';
                                            }else{
                                                    //$this->upyjscript($v['iid'],$actType);
                                                    //$this->updateyjPhp($v['iid']);
                                                    echo $v['iid'].' 添加成功!<br/>';
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
                                                    echo $v['iid'].' 更新失败,数据库操作失败!<br/>';
                                            }else{
                                                    //$this->upyjscript($v['iid'],$actType);
                                                    //$this->updateyjPhp($v['iid']);
                                                    echo $v['iid'].' 更新成功!<br/>';
                                            }

                                    }
				}	
				
			}
		}
		$this->display("admin/uzcaiji.html");
	}
	
	
	// 一键采集
	public function yjuzcaiji(){
//            set_time_limit(0);
            // 采集开春哥
//            ini_set('memory_limit', '64M'); // 内存超载
//            ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
//            ini_set('pcre.recursion_limit', 99999); // 资源开大就行
            // end - 采集开春哥
            $file = "./tmp/output"; 
//            $file = "/var/log/messages"; 
            $lastpos = 0;  
//            exec("rm -f ".$file);
//            exec("uzcaiji.sh >> ".$file." &");
            while(true){  
                echo tail($file,$lastpos);  
            } 
	}
        
        //淘客商品采集
        public function tkitemscaiji(){
            set_time_limit(0);
              
            // 采集开春哥
            ini_set('memory_limit', '64M'); // 内存超载
            ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
            ini_set('pcre.recursion_limit', 99999); // 资源开大就行
            // end - 采集开春哥
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
        
	// uz采集
	public function uzcaiji($type=null){
		
//		if(!$_SESSION['admin'])
//			header("Location:/login.html");
		
		set_time_limit(0);
              
                
		// 采集开春哥
		ini_set('memory_limit', '64M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		// end - 采集开春哥
		
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
				
		//采集
		if(!$type)
			$type = $this->spArgs('type');
		
			
		$actType = $this->website[$type]['actType'];
		$pros = spClass("m_pro");
		/* $catmaps = spClass("m_catmap");
		import('tbapi.php'); */
		//echo $caiji.'<br/>';
//                elseif($actType == 11 || $actType == 4){ // 卷皮  && 九块邮  
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
			if($actType == 15){ // 赚宝
                                 
                            for($page=1;$page<=1;$page++){
                               //$xiaiCaiji->Caiji($type,$page);
                               $xiaiCaiji->Caiji($type);
                               $items = $xiaiCaiji->getitems();
                               $this->getitems($items, $actType);
                           }
                                
			}elseif($actType == 10 || $actType == 16){ // 秒杀通,特价疯抢采集5页
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
			echo '没有选择采集站点!';
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
	public function postDataToUz($mode='php'){//批量POST数据到优站
		set_time_limit(0);
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		header("Content-Type: text/html; charset=gbk");
		
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // 当天的预告过滤
		
		$control = spClass('m_control');
		$caiji_control = $control->find(array('type'=>1));
		if($caiji_control['isuse'])
			exit();
		else
			$control->update(array('type'=>1),array('isuse'=>1));
		
		
		//var_dump($control->find());
		$pros = spClass('m_pro');
		
		// 一键导入数据重组
		foreach($this->website as $k=>$v){
			if($k!='none'){
				if(COMISSIONRATESORT)
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate() and commission_rate>=5';
				else
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate()';
			}
			$items_zu['actfrom'.$v['actType']] = $pros->findAll($where,'commission_rate asc');//佣金低->高组合，插入的时候就反过来基于postdt时间为now(),
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
					$v['title'] = preg_replace('/【.+?】/i','',$v['title']);
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
			echo date("H:i:s").'暂停';
			sleep(210);
			echo date("H:i:s").'继续';
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
				alert('taobao.taobaoke.widget.items.convert接口获取商信息品失败!'+resp.error_response.msg);
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
	
	// 更新佣金插件PHP版
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
					echo '更新成功.<br />';
				else
					echo '更新失败.<br />';
			}else{
				echo '获取失败.<br />';
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
                    // 查询fstk_catmap对应类目
                    $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                    //var_dump($catMap);
                    if($catMap){ //如果商品类目有映射
                        $itemTemp = array('cat'=>(int)$catMap['type']);
                    }else{
                        $itemTemp = array('cat'=>43);
                    }
                    if($pros->update(array('iid'=>$v['iid']),$itemTemp))
                        echo $v['iid'].' 从分类'.$v['cat'].'更新分类到 '.$itemTemp['cat'].' 成功.<br />';
                    else
                        echo $v['iid'].' 从分类'.$v['cat'].'更新分类到 '.$itemTemp['cat'].' 失败.<br />';
		}
	}
        public function updatetags(){
            $pros = spClass('m_pro');
            $where = 'st<=curdate() and et>=curdate() and ischeck=1';
            $items = $pros->findAll($where,'','','100');
            foreach($items as $k=>$v){
                if(!$v['tags']){
                    echo $v['tags'].' 没有提取标签 <br/>';
                }
            }
        }
        public function updateshopname(){
            set_time_limit(0);
            
            // 采集开春哥
            ini_set('memory_limit', '64M'); // 内存超载
            ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
            ini_set('pcre.recursion_limit', 99999); // 资源开大就行
            // end - 采集开春哥
            
            //更新slink
            $pros = spClass('m_pro');
            import('tbapi.php');
            $where = 'slink=""';
            $items = $pros->findAll($where);
            if(!empty($items)){
                foreach($items as $k=>$v){
                    $iteminfo = getItemDetail($v['iid']);
                    if($iteminfo<0){
                        echo $v['iid'].' 获取信息失败!<br/>';
                    }else{
                        $itemTemp['slink'] = $iteminfo['slink'];
                        if($pros->update(array('iid'=>$v['iid']),$itemTemp))
                            echo $v['iid'].' 更新slink为'.$itemTemp['slink'].' 成功.<br />';
                        else
                            echo $v['iid'].' 更新slink为'.$itemTemp['slink'].' 失败.<br />';
                    }
                }
            }else{
                echo 'slink无需更新！';
            }
            //更新shopname,ww,nick
            $where = 'ww="" or nick="" or shopname=""';
            $items = $pros->findAll($where);
            if(!empty($items)){
                foreach($items as $k=>$v){
                    $iteminfo = getItemDetail($v['iid']);
                    if($iteminfo<0){
                        echo $v['iid'].' 获取信息失败!<br/>';
                    }else{
                        $itemTemp['ww'] = iconv('utf-8','gbk',$iteminfo['ww']);
                        $itemTemp['nick'] = iconv('utf-8','gbk',$iteminfo['nick']);
                        $itemTemp['shopname'] = iconv('utf-8','gbk',$iteminfo['shopname']);
                        if($pros->update(array('iid'=>$v['iid']),$itemTemp))
                            echo $v['iid'].' 更新shopname为'.$itemTemp['shopname'].' 成功.<br />';
                        else
                            echo $v['iid'].' 更新shopname为'.$itemTemp['shopname'].' 成功.<br />';
                    }
                }
            }else{
                echo 'shopname,ww,nick无需更新！';
            }
        }
	// 更新佣金插件
	public function updateyjonce(){
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass('m_pro');
		$yj = $this->spArgs('zk');
		$iid = $this->spArgs('iid');
		$item['commission_rate'] = $yj;
		//echo $iid.' 佣金'.$yj.'<br/>';
		if($iid && $yj){
			$pros->update(array('iid'=>$iid),$item);
		}
		$this->display("admin/uzcaiji.html");
	}
	// END 更新佣金插件
	// 数据导出
	public function dbselect(){
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
			
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // 当天的预告过滤
		
		
		$pros = spClass('m_pro');
		
		// 一件导入控制
		if(SETAJAXTOUZ){
			$control = spClass("m_control");
			$caiji_control = $control->find(array('type'=>1));
			$this->caijiisuse = $caiji_control['isuse'];
		}else{
			$this->caijiisuse = 1;
		}
		
		// SQL文件导入控制
		if(SETFILETOUZ){
			$control = spClass("m_control");
			$getsql_control = $control->find(array('type'=>2));
			$this->getsqlisuse = $getsql_control['isuse'];
		}
		
		// 当天采集的数据
//		if(COMISSIONRATESORT){
//			$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
//		}
//		else{    
//			$where = $baseSql.' and postdt>=curdate()';
//		}
		
		
		// 各个平台的数据单独导
		$type = $this->spArgs('type');
		$actfrom = $this->website[$this->spArgs('type')]['actType'];
		if($actfrom){ // 每个平台选择			
			$page = $this->spArgs('page',1);
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// 会员购,VIP特惠，VIP购优惠不过滤佣金
				$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			else{
				if(COMISSIONRATESORT)
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()  and commission_rate>=5';
				else
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			}
		}else{ // 全部
			$page = $this->spArgs('page',1);
			if(COMISSIONRATESORT)
				$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
			else
				$where = $baseSql.' and postdt>=curdate()';
		}
		
		$itemsTemp = $pros->spPager($page,50)->findAll($where);
		
		
		// 采集用户的信息
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
	
	//导出数据数据存储为sql文件
	public function savesqltouz(){
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		$filename = $_SESSION['iscaijiuser'].date("Y-m-d");
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // 当天的预告过滤
	
		$pros = spClass('m_pro');
		
		// 队列下载
		$control = spClass('m_control');
		$getsql_control = $control->find(array('type'=>2));
		if($getsql_control['isuse'])
			exit();
		else
			$control->update(array('type'=>2),array('isuse'=>1));
		
		// 一键导入数据重组
		foreach($this->website as $k=>$v){
			if($k!='none'){
				if(COMISSIONRATESORT)
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate() and commission_rate>=5';
				else
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate()';
			}
			$items_zu['actfrom'.$v['actType']] = $pros->findAll($where,'commission_rate asc');//佣金低->高组合，插入的时候就反过来基于postdt时间为now(),
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
		
		//清空文件夹
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
					$v['title'] = preg_replace('/【.+?】/i','',$v['title']);
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
                                            echo '文件打开失败';
					//echo $sqlout_sec.'<br />';
					fwrite($file,iconv('gbk','utf-8',$sqlout_sec."\n"));
//                                        fwrite($file,substr($sqlout_sec,0,-1));
					$sqlout_sec = null;
				}
			}
			fclose($file);
		}
		
		//获取列表 
		$datalist=list_dir('./tmp/sqlout/');
		//var_dump($datalist);
		$zipfilename = "./tmp/".$filename.".zip"; //最终生成的文件名（含路径）   
		unlink($zipfilename);
		if(!file_exists($zipfilename)){   
			//重新生成文件   
			$zip = new ZipArchive();//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释   
			if ($zip->open($zipfilename, ZIPARCHIVE::CREATE)!==TRUE) {   
				exit('无法打开文件，或者文件创建失败');
			}   
			foreach($datalist as $k=>$val){   
				if(file_exists($val)){   
					$zip->addFile($val,basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下   
				}   
			}   
			$zip->close();//关闭   
		}   
		if(!file_exists($zipfilename)){   
			exit("无法找到文件"); //即使创建，仍有可能失败。。。。   
		}   
		header("Cache-Control: public"); 
		header("Content-Description: File Transfer"); 
		header('Content-disposition: attachment; filename='.basename($zipfilename)); //文件名   
		header("Content-Type: application/zip"); //zip格式的   
//		header('Content-disposition: attachment; filename='.basename('./tmp/sqlout/'.$filename.'-part1.sql')); //文件名   
//		header("Content-Type: application/text"); //text格式的 
		header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件    
		header('Content-Length: '. filesize($zipfilename)); //告诉浏览器，文件大小   
//		header('Content-Length: '. filesize('./tmp/sqlout/'.$filename.'-part1.sql')); //告诉浏览器，文件大小   
		@readfile($zipfilename); 
//		@readfile('./tmp/sqlout/'.$filename.'-part1.sql');
		$control->update(array('type'=>2),array('isuse'=>0));
	}
	
	// 数据导出
	public function sqlout(){
		
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		
		$actfrom =	$this->website[$this->spArgs('type')]['actType'];
		$pros = spClass('m_pro');
		//echo $actfrom.'best<br/>';
		if($actfrom){		
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// 会员购,VIP特惠，VIP购优惠不过滤佣金
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
		if($_SESSION['iscaijiuser']=='yuansu')// 爆划算接口
			$sqlout_fir = "INSERT INTO `items` (`iid`,`title`,`picurl`,`itemurl`,`price`,`prom`,`nick`,`categoryid`,`partid`,`status`,`top`,`gg`,`report`,`freeshipping`,`stock`,`sorts`,`starttime`,`endtime`) VALUES ";
		else // fstk_数据库的输出
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
				$v['title'] = preg_replace('/【.+?】/i','',$v['title']);
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
				if($_SESSION['iscaijiuser']=='9kuaigou'){ // 九块购
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
	
	// 淘客报表
	public function tkreport(){ //上传和数据整合显示
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		set_time_limit(0);
                ini_set("memory_limit","1024M");
		import('tbapi.php');
                import('PHPExcel.php');
                
                // 上传文件
                if($this->spArgs("submit")){
                    import("func.php");
                    //清空文件夹
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
                
                // 表格标题
                for($i=0;$i<count($sheetData[0]);$i++){
                    $sheetData[0][$i] = iconv('utf-8','gbk',unicode2utf8($sheetData[0][$i]));
                }
//                $this->sheetDataHead = $sheetData[0];
                
                //表格数据
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
                
                //表格数据下标
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
