<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class mail extends spController{
	public function __construct(){
		parent::__construct;
		import("email.class.php");
	}
	public function send(){
		$smtpserver = "smtp.163.com";//SMTP������
		$smtpserverport =25;//SMTP�������˿�
		$smtpusermail = "keep_looking@163.com";//SMTP���������û�����
		$smtpemailto = "247176039@qq.com";//���͸�˭
		$smtpuser = "keep_looking@163.com";//SMTP���������û��ʺ�
		$smtppass = "z123456";//SMTP���������û�����
		$mailsubject = "PHP100�����ʼ�ϵͳ";//�ʼ�����
		$mailbody = "<h1> ����һ�����Գ��� PHP100.com </h1>";//�ʼ�����
		$mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
		##########################################
		$smtp = spClass("smtp");
		$smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
		$smtp->debug = FALSE;//�Ƿ���ʾ���͵ĵ�����Ϣ
		$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
		echo "111";
	}
}

