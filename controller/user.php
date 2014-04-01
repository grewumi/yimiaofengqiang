<?php
class user extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		$this->ucenter = spClass('spUcenter');
		$this->users = spClass("m_u");
		$this->member = spClass("m_member");
		$this->ggw = spClass("m_ggw");
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
			if($uid <= 0){
				if($uid == -1){
					$this->regnote = 'ע��ʧ�ܣ��û������Ϸ�';
				}elseif($uid == -2){
					$this->regnote = 'ע��ʧ�ܣ�����Ҫ����ע��Ĵ���';
				}elseif($uid == -3){
					$this->regnote = 'ע��ʧ�ܣ��û����Ѿ�����';
				}elseif($uid == -4){
					$this->regnote = 'ע��ʧ�ܣ�Email ��ʽ����';
				}elseif($uid == -5){
					$this->regnote = 'ע��ʧ�ܣ�Email ������ע��';
				}elseif($uid == -6){
					$this->regnote = 'ע��ʧ�ܣ��� Email �Ѿ���ע��';
				}else{
					$this->regnote = 'ע��ʧ�ܣ�δ����';
				}
			}else{
				$this->registersuccess = 1;
				$this->regnote = 'ע��ɹ�!!';
			}
				
		}
	}
	public function login(){
		/* ����ǩ�� */
		$app_key = '21726073';
		$secret='c23972d5f868ce97b17e66298a228136';
		$timestamp=time()."000";
		$message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
		$mysign=strtoupper(hash_hmac("md5",$message,$secret));
		setcookie("timestamp",$timestamp);
		setcookie("sign",$mysign);
		/*  END - ����ǩ��*/
		$loginstatus = $this->spArgs('cmd');
		if($loginstatus == 'out'){
			clearcookie();
			//header("Location:/?c=user&a=login");
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
				if($uinfo['uid'] > 0) {
					$this->loginsuccess = 1;
					$GLOBALS['G_SP']['supe_uid'] = $uinfo['uid'];
					//����cookie
					ssetcookie('auth', authcode($uinfo['password'].'\t'.$uinfo['uid'], 'ENCODE'), 31536000);
					ssetcookie('loginuser', $uinfo['username'], 31536000);
					ssetcookie('_refer', '');
					// end - ����cookie
					$this->loginnote = '��¼�ɹ�';
				} elseif($uinfo['uid'] == -1) {
					$this->loginnote = '�û�������,���߱�ɾ��';
				} elseif($uinfo['uid'] == -2) {
					$this->loginnote = '�������';
				} else {
					$this->loginnote = 'δ����';
				}
			}
		}
		if($GLOBALS['G_SP']['supe_uid']){
			//var_dump($uinfo);
			if(!$this->member->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']))){
				$this->member->create($uinfo);
			}
			if(!$this->users->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']))){
				//echo '���û����';
				$newuser = array(
					'uid'=>$GLOBALS['G_SP']['supe_uid'],
					'username'=>$uinfo['username'],
					'lastlogin'=>date("Y-m-d H:i:s")
				);
				//var_dump($newuser);
				$this->users->create($newuser);
				//echo $this->users->dumpSql();
				//echo '�û�������';
			}else{
				$this->users->update(array('uid'=>$GLOBALS['G_SP']['supe_uid']),array('lastlogin'=>date("Y-m-d H:i:s")));
			}
			header("Location:/?c=user&a=iinfo");
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
		$this->ggws = $this->ggw->findAll(array('username'=>$this->uname));
		//var_dump($this->ggws);
		if($this->spArgs("submit")){
			$ww = $this->spArgs("ww");
			$iid = $this->spArgs("iid");
			if($ww)
				$this->users->update(array('username'=>$this->uname),array('ww'=>$ww));
			if($iid){
				if($this->hyjf>=900){
					$this->ggw->create(array('username'=>$this->uname,'iid'=>$iid,'dh'=>1));
					$this->users->update(array('username'=>$this->uname),array('hyjf'=>$this->hyjf-900));
				}
			}
		}
		
		$uinfo = $this->ucenter->uc_get_user($this->uname);
		$this->uemail = $uinfo[2];//var_dump($uinfo);
		$this->display("front/iinfo.html");
	}
	
}