<?php
import("tousers.php");
class user extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		$this->ucenter = spClass('spUcenter');
		$this->users = spClass("m_u");
		$this->member = spClass("m_member");
		$this->ggw = spClass("m_ggw");
                $this->mode = $this->spArgs("mode");
                $this->procats = spClass("m_procat")->findAll('isshow=1','type asc');
	}
	public function register(){
		$this->registersuccess = 0;
		if($this->spArgs("submit")){
			$username = $this->spArgs("username");
			$password = $this->spArgs("password");
			$email = $this->spArgs("email");
			$questionid = $this->spArgs("questionid");
			$answer = $this->spArgs("answer");
			$uid = $this->ucenter->uc_user_register($username,$password,$email);
                        
                        $vcode = spClass('spVerifyCode');
			if($vcode->verify($this->spArgs('verifycode'))) {
				//通过验证
				if($uid <= 0){
					if($uid == -1){
						$this->regnote = '注册失败：用户名不合法';
					}elseif($uid == -2){
						$this->regnote = '注册失败：包含要允许注册的词语';
					}elseif($uid == -3){
						$this->regnote = '注册失败：用户名已经存在';
					}elseif($uid == -4){
						$this->regnote = '注册失败：Email 格式有误';
					}elseif($uid == -5){
						$this->regnote = '注册失败：Email 不允许注册';
					}elseif($uid == -6){
						$this->regnote = '注册失败：该 Email 已经被注册';
					}else{
						$this->regnote = '注册失败：未定义';
					}
					$this->regnote .= '<a href="/?c=user&a=login&cmd=reg">重新注册</a>';
				}else{
					$this->registersuccess = 1;
					$this->regnote = '注册成功!!';
					$this->regnote .= '<a href="/?c=user&a=login">立即登录</a>';
				}
			}else{
				//没有通过验证
				$this->regnote = '注册失败：验证码错误';
				$this->regnote .= '<a href="/?c=user&a=login&cmd=reg">重新注册</a>';
			}
			
				
		}
	}
	/*
	 * 
	 */
	public function login(){
		/* 设置签名 */
//		$app_key = '21726073';
//		$secret='c23972d5f868ce97b17e66298a228136';
//		$timestamp=time()."000";
//		$message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
//		$mysign=strtoupper(hash_hmac("md5",$message,$secret));
//		setcookie("timestamp",$timestamp);
//		setcookie("sign",$mysign);
		/*  END - 设置签名*/
		//var_dump($_COOKIE);
	
		$loginstatus = $this->spArgs('cmd');	
		$refer = $this->spArgs("refer");
		if($refer)
			ssetcookie('_refer',$refer);
		else
			ssetcookie('_refer','');
		if($loginstatus == 'out'){
			clearcookie();
			header("Location:/?c=user&a=login");
		}elseif($loginstatus == 'reg'){
			$this->register();
		}else{
			if($this->spArgs("submit")){
				$username = $this->spArgs("username");
				$password = $this->spArgs("password");
				$email = $this->spArgs("email");
				$questionid = $this->spArgs("questionid");
				$answer = $this->spArgs("answer");
				$userinfo = $this->ucenter->uc_user_login($username,$password);
				$mtime = explode(' ', microtime());
				$uinfo = array(
					'uid'=>$userinfo[0],
					'username'=>$userinfo[1],
					'password'=>md5($userinfo[0].'|'.$mtime[1]),
					'email'=>$userinfo[3],
				);
				//var_dump($uinfo);
				//echo $uinfo['password'];
                            
                            
                            $vcode = spClass('spVerifyCode');
                            if($vcode->verify($this->spArgs('verifycode'))) {
                                    //通过验证
                                    if($uinfo['uid'] > 0) {
                                            $this->loginsuccess = 1;
                                            $GLOBALS['G_SP']['supe_uid'] = $uinfo['uid'];
                                            //设置cookie
                                            ssetcookie('auth', authcode($uinfo['password'].'\t'.$uinfo['uid'], 'ENCODE'), 31536000);
                                            ssetcookie('loginuser', $uinfo['username'], 31536000);                                         
                                            // end - 设置cookie
                                            $this->loginnote = '登录成功';
                                    } elseif($uinfo['uid'] == -1) {
                                            $this->loginnote = '用户不存在,或者被删除';
                                    } elseif($uinfo['uid'] == -2) {
                                            $this->loginnote = '密码错误';
                                    } else {
                                            $this->loginnote = '未定义';
                                    }
                            }else{
                                    //没有通过验证
                                    $this->loginnote = '登录失败：验证码错误';
                                    $this->loginnote .= '<a href="/?c=user&a=login&cmd=reg">重新登录</a>';
                            }
				
			}
		}
		if($GLOBALS['G_SP']['supe_uid']){ // 登录成功后
			//var_dump($uinfo);
                    	
			if(!$this->member->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']))){//没有找到用户，插入新数据到member表
				$this->member->create($uinfo);
			}
			if(!$this->users->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']))){
				//echo '新用户入库';
				$newuser = array(
					'uid'=>$GLOBALS['G_SP']['supe_uid'],
					'username'=>$uinfo['username'],
					//'lastlogin'=>date("Y-m-d H:i:s")
				);
				//var_dump($newuser);
				$this->users->create($newuser);
				//echo $this->users->dumpSql();
				//echo '用户入库完成';
			}else{
				$this->users->update(array('uid'=>$GLOBALS['G_SP']['supe_uid']),array('lastlogin'=>date("Y-m-d H:i:s")));
			}
			// 用户积分
                        $jf = $this->users->find(array('username'=>$uinfo['username']),'','jf');
                        ssetcookie('jf',$jf['jf'], 31536000);
                        
			// 用户类别
			$groups = $this->users->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']));
			$group = $groups['group'];
			
                        $uinfos = $this->users->find(array('username'=>$uinfo['username']));
                        ssetcookie('dpww',$uinfos['ww'], 31536000); 
                        
			if($_COOKIE[$GLOBALS['G_SP']['SC']['cookiepre'].'_refer'])
				header("Location:".$_COOKIE[$GLOBALS['G_SP']['SC']['cookiepre'].'_refer']);
			else
				switchtogrouppage($group);		
		}
		$this->cmd = $loginstatus;
		$this->display("front/login.html");
	}
	
	public function iinfo(){
		if(!$GLOBALS['G_SP']['supe_uid'])
			header("Location:/?c=user&a=login");
		$act = $this->spArgs("act");
		$this->act = $act;
		$uinfos = $this->member->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']));
		$this->uname = $uinfos['username'];
		
		$uinfo = $this->users->find(array('username'=>$uinfos['username']));
		//var_dump($uinfo);
		
		$this->lastlogin = $uinfo['lastlogin'];
		$this->ww = $uinfo['ww'];
		$this->hyjf = $uinfo['hyjf'];
                $this->deposit = $uinfo['deposit'];
                $this->ggws = $this->ggw->findAll(array('username'=>$this->uname));
                $this->bmbb = spClass("m_pro")->findAll('ww="'.$this->ww.'" and channel=2');
		
		//var_dump($this->ggws);
		if($this->spArgs("submit")){
			$ww = $this->spArgs("ww");
			$iid = $this->spArgs("iid");
                        if($ww){
                            if($this->users->update(array('username'=>$this->uname),array('ww'=>$ww))){
                                ssetcookie('dpww',$ww, 31536000); 
                            }
                        }
                        $proinfo = spClass("m_pro")->find(array('iid'=>$iid));
                        if($proinfo){//兑换推广
                            if($this->spArgs("leixing")==1)
                                $xuyaojifen = 900;
                            if($this->spArgs("leixing")==2)
                                $xuyaojifen = 100;
                            if($this->spArgs("leixing")==3){
                                $danshu = $this->spArgs("danshu");
                                if($danshu){
                                    if($danshu>1 and $danshu<500)
                                        $xuyaojifen = $danshu*2;
                                    elseif($danshu>=500 and $danshu<800)
                                        $xuyaojifen = $danshu*1.5;
                                    elseif($danshu>=800 and $danshu<1000)
                                        $xuyaojifen = $danshu*1;
                                    elseif($danshu>=1000 and $danshu<2000)
                                        $xuyaojifen = $danshu*0.8;
                                    elseif($danshu>=2000)
                                        $xuyaojifen = $danshu*0.6;
                                }else{
                                    $this->tips = "<p style='color:red;'>请填写单数！</p>";
                                }
                            }
                        }
                        if($xuyaojifen){
                            if($this->hyjf>=$xuyaojifen){
                                $this->ggw->create(array('username'=>$this->uname,'iid'=>$iid,'type'=>$this->spArgs("leixing"),'xhjf'=>$xuyaojifen,'dh'=>1));//录入广告位
                                $this->users->update(array('username'=>$this->uname),array('hyjf'=>$this->hyjf-$xuyaojifen));//扣除商家积分
                                $this->tips = "<p style='color:red;'>兑换成功！<a href='/?c=user&a=iinfo&act=ggw'>查看我的推广</a></p>";
                            }else{
                                $userinfo = $this->users->find(array('username'=>$this->uname));
                                $haichajifen = (int)$xuyaojifen - (int)$userinfo['hyjf'];
                                $this->tips = "<p style='color:red;'>积分不足请充值，还差(<span style='color:#000;'>".$haichajifen."</span> )积分！</p>";
                            }
                        }
			
		}
		
		if($act=='cz'){//充值
			if($this->spArgs("submit")){
				$total = intval($_POST['money']);
				if(!$total) {
				  $total = 900;
				} 
//				$pay_bank = trim($_POST['pay_bank']);
//				$account = $_POST['_account'];
				$base_path = 'http://'.$_SERVER['HTTP_HOST'].'/?c=user&a=iinfo&act=cz';echo $base_path;
				$order_id = date('Y-m-d H:i:s', time()); //时间值作为唯一的订单ID号
				$subject = $body = '充值'.$total.'元';
				$out_trade_no = date('Y-m-d H:i:s',time());
				//合作身份者ID，以2088开头的16位纯数字
				$partner = '2088311838983110';
				//安全检验码，以数字和字母组成的32位字符
				$security_code = '5fac3wolaqxry1kqg8s7z5jcij8fsd5h';
				//签约支付宝账号或卖家支付宝帐户
				$seller_email = 'jianquds@163.com';
				$_input_charset = "UTF-8";
				$sign_type = "MD5"; //签名方式
				$transport = 'https';//字符编码格式
				$parameter = array(
				  "service"        => "create_direct_pay_by_user",  //交易类型
				  "partner"        => $partner,         //合作商户号
				  "return_url"     => $base_path.'alipay/return',      //同步返回
				  "notify_url"     => $base_path.'alipay/notify',      //异步返回
				  "_input_charset" => 'UTF-8',  //字符集，默认为GBK
				  "subject"        => $subject,       //商品名称，必填
				  "body"           => $subject,       //商品描述，必填
				  "out_trade_no"   => $out_trade_no,     //商品外部交易号，必填（保证唯一性）
				  "price"          => $total,           //商品单价，必填（价格不能为0）
				  "payment_type"   => "1",              //默认为1,不需要修改
				  "quantity"       => "1",              //商品数量，必填
				  "paymethod"        => 'directPay',
				  "defaultbank"        => '',
				  "logistics_fee"      => '0.00',        //物流配送费用
				  "logistics_payment"  =>'BUYER_PAY',   //物流费用付款方式：SELLER_PAY(卖家支付)、BUYER_PAY(买家支付)、BUYER_PAY_AFTER_RECEIVE(货到付款)
				  "logistics_type"     =>'EXPRESS',     //物流配送方式：POST(平邮)、EMS(EMS)、EXPRESS(其他快递)
				  //"receive _mobile" => ”,         //收货人手机
				  "show_url"       => $base_path,        //商品相关网站
				  "seller_email"   => $seller_email,     //卖家邮箱，必填
				);
				import("alipay.class.inc.php");
				$alipay = new alipay_service($parameter, $security_code, $sign_type);
				$link = $alipay->create_url();
				header("Location: ".$link); 
			}
		}
                if($act=='bmbb'){
                    if($this->mode=='try'){
                            $pros = spClass("m_try_items");
                    }else{
                            $pros = spClass("m_pro");
                    }
                    $this->bmbb = $pros->findAll('ww="'.$this->ww.'" and channel=2');
                }
		$uinfo = $this->ucenter->uc_get_user($this->uname);
		$this->uemail = $uinfo[2];//var_dump($uinfo);
		$this->display("front/iinfo.html");
	}
	
	public function _vcode(){
		$vcode = spClass('spVerifyCode');
		$vcode->display();
	}
        public function ajaxqd(){ //签到前判断
                header("Content-type: text/html; charset=gbk"); 
		if ($GLOBALS['G_SP']['supe_uid']){
                    $uinfos = $this->member->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']),'','username');
                    $qddate = $this->users->find(array('username'=>$uinfos['username']),'','qddate,lxqd,jf,qd');
                    //判断今天是否签到
                    if($qddate['qddate']>=date("Y-m-d")){//今天已签到
                        if(!$qddate['qd'])
                            $this->users->update(array('username'=>$uinfos['username']),array('qd'=>1));
                    }else{
                        if($qddate['qd'])
                            $this->users->update(array('username'=>$uinfos['username']),array('qd'=>0));
                        // 判断昨天是否签到
                        if(!($qddate['qddate']<date("Y-m-d") && $qddate['qddate']>=date("Y-m-d",strtotime("-1 day")))){//未签到
                            $this->users->update(array('username'=>$uinfos['username']),array('lxqd'=>0));
                        }
                    }
                    $qddate = $this->users->find(array('username'=>$uinfos['username']),'','qddate,lxqd,jf,qd');
                    if($qddate['qd'])
                            $todayQdJf = $qddate['lxqd']*5;
                    else
                            $todayQdJf = $qddate['lxqd']*5+5;

                    $tomorrowQdJf = $todayQdJf+5;
                    if($todayQdJf>30){
                            $todayQdJf = 30;
                    }
                    if($tomorrowQdJf>30){
                            $tomorrowQdJf = 30;
                    }

                    echo '{"isqd":"'.$qddate['qd'].'","lxqd":"'.((int)$qddate['lxqd']).'","jtkl":"'.$todayQdJf.'","mtkl":"'.$tomorrowQdJf.'"}';
		}
	}
        public function qiandao(){//签到
            header("Content-type: text/html; charset=gbk"); 
            if($GLOBALS['G_SP']['supe_uid']){
                $uinfos = $this->member->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']),'','username');
                $qddate = $this->users->find(array('username'=>$uinfos['username']),'','qddate,lxqd,jf,qd');
                if ($qddate['qd']){//今天已签到
                    ssetcookie('jf',$qddate['jf'], 31536000);
                    echo '{"stat":"您已签到","jf":"'.$qddate['jf'].'","avatar":""}';
                }else{//今天未签到
                    $todayQdJf = $qddate['lxqd']*5+5;
                    if($todayQdJf>30){
                            $todayQdJf = 30;
                    }
                    $alljf = $todayQdJf +  $qddate['jf'];
                    $qiandao = array('qddate'=>date('Y-m-d'),'jf'=>$alljf,'lxqd'=>$jf[0][lxqd]+1);
                    $this->users->update(array('username'=>$uinfos['username']),$qiandao);
                    ssetcookie('jf',$qddate['jf'], 31536000);
                    echo '{"stat":"恭喜您获得'.$todayQdJf.'积分","jf":"'.$qddate['jf'].'","lxqd":"'.$qddate['lxqd'].'","avatar":""}';
                }
            }else{
                    echo '{"stat":"请先登陆","jf":"0","avatar":""}';
            }
        }
	
}