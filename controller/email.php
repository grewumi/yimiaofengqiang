<?php
class email extends spController{
	public function __construct() {
		parent::__construct();
		$this->mailbody = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/?c=mkhtml');
	}
	public function getemail(){
		$datalist=list_dir('./tmp/email/');
		$data = array();
		foreach($datalist as $v){
			$file[] = file($v);
		}
		foreach($file as $iv){
			foreach($iv as &$line){
				$data[] = trim($line);
			}
		}
		return $data;
	}
	public function sendemail($smtpemailto,$mailsubject,$mailbody){
		set_time_limit(0);
		import("smtp.php");
		$smtpserver = "smtp.163.com";//SMTP������
		$smtpserverport = 25;//SMTP�������˿�
		$smtpusermail = "yimiaofengqiang@163.com";//SMTP���������û�����
		
		$smtpuser = "yimiaofengqiang@163.com";//SMTP���������û��ʺ�
		$smtppass = "z123456";//SMTP���������û�����
		
                $mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
		$smtpemailto = $this->spArgs("smtpemailto")?$this->spArgs("smtpemailto"):"247176039@qq.com";//���͸�˭
		$mailsubject = $this->spArgs("mailsubject")?$this->spArgs("mailsubject"):"һ�������������ƫ�á���ѡ��Ʒ������ѡ��";//�ʼ�����
		$mailbody =  $this->spArgs("mailbody")?$this->spArgs("mailbody"):$this->mailbody;//�ʼ�����
		
//		##########################################
		$smtp = spClass("smtp");
		$smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
		$smtp->debug = FALSE;//�Ƿ���ʾ���͵ĵ�����Ϣ
		$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);		
	}
	
}