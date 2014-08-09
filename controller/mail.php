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
		$smtpserver = "smtp.163.com";//SMTP服务器
		$smtpserverport =25;//SMTP服务器端口
		$smtpusermail = "keep_looking@163.com";//SMTP服务器的用户邮箱
		$smtpemailto = "247176039@qq.com";//发送给谁
		$smtpuser = "keep_looking@163.com";//SMTP服务器的用户帐号
		$smtppass = "z123456";//SMTP服务器的用户密码
		$mailsubject = "PHP100测试邮件系统";//邮件主题
		$mailbody = "<h1> 这是一个测试程序 PHP100.com </h1>";//邮件内容
		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
		##########################################
		$smtp = spClass("smtp");
		$smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		$smtp->debug = FALSE;//是否显示发送的调试信息
		$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
		echo "111";
	}
}

