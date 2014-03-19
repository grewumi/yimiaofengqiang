<?php
class user extends spController{
	public function __construct(){
		parent::__construct();
		$this->ucenter = spClass('spUcenter');
		$this->users = spClass("m_u");
		$this->ggw = spClass("m_ggw");
		if($_SESSION['user'])
			$this->uname = $_SESSION['user'];
		if($this->uname){
			if(!$this->users->find(array('username'=>$this->uname)))
				$this->users->create(array('username'=>$this->uname,'lastlogin'=>date("Y-m-d H:i:s")));
		}
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
		$app_key = '21726073';/*��дappkey */
		$secret='c23972d5f868ce97b17e66298a228136';/*����Appsecret'*/
		$timestamp=time()."000";
		$message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
		$mysign=strtoupper(hash_hmac("md5",$message,$secret));
		setcookie("timestamp",$timestamp);
		setcookie("sign",$mysign);
		/*  END - ����ǩ��*/
		$loginstatus = $this->spArgs('cmd');
		if($loginstatus == 'out'){
			$_SESSION['user'] = 0;
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
				$userinfo = $this->ucenter->uc_user_login($username,$password,$email);
				$uid = $userinfo[0];
				$uname = $userinfo[1];
				//var_dump($userinfo);
				if($uid > 0) {
					$this->loginsuccess = 1;
					$userinfo = $this->ucenter->uc_get_user($username);
					$_SESSION['user'] = $uname;
					$this->loginnote = '��¼�ɹ�';
				} elseif($uid == -1) {
					$this->loginnote = '�û�������,���߱�ɾ��';
				} elseif($uid == -2) {
					$this->loginnote = '�������';
				} else {
					$this->loginnote = 'δ����';
				}
			}
		}
		if($_SESSION['user']){
			$this->users->update(array('username'=>$_SESSION['user']),array('lastlogin'=>date("Y-m-d H:i:s")));
			header("Location:/?c=user&a=iinfo");
		}
		$this->cmd = $loginstatus;
		$this->display("front/login.html");
	}
	public function iinfo(){
		if(!$_SESSION['user'])
			header("Location:/?c=user&a=login");
		//echo $_SESSION['user'];
		$act = $this->spArgs("act");
		$this->act = $act;
		$uinfo = $this->users->find(array('username'=>$this->uname));
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
		
		$uinfo = $this->ucenter->uc_get_user($_SESSION['user']);
		$this->uemail = $uinfo[2];//var_dump($uinfo);
		$this->display("front/iinfo.html");
	}
	
}