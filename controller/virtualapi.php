<?php
class virtualapi extends spController{
  public function loginAlimama(){
    import("function_login_taobao.php");

    if(!empty($_POST['username']) && !empty($_POST['password']))
    {
        header("Content-type: text/html; charset=gbk");
        $user = trim($_POST['username']);
        $pass = trim($_POST['password']);

        loginTaobao($user, $pass);
       
        
        echo getCommissionRate('37856517809');
     }
    $this->display("admin/loginAlimama.html");
  } 
  
  public function getCheckcode(){
	$imgurl = urldecode($this->spArgs("imgurl"));
	//echo $imgurl;
	$img = getImage($imgurl,'/tmp/','Checkcode.jpg');
	var_dump($img);
	$softID = '96821';
	$softKey = 'af21e117eb4a4befb55ab75d16ffa997';
	$userName = 'lemontea';
	$passWord = 'uu.86#set.';
	$codeType = '1004';
	/* import('uuapi.php');
	$obj = spClass('uuApi');
	$obj->setSoftInfo($softID,$softKey);
	$loginStatus=$obj->userLogin($userName,$passWord);
	if($loginStatus>0){
		echo '�����û�IDΪ��'.$loginStatus.'<br/>';
		$getPoint=$obj->getPoint($userName,$passWord);
		echo '���ʻ��ڵ�ʣ����ֻ��У�'.$getPoint.'<br/><br/>';	//�������Ǵ������
		
		//���濪ʼʶ��				
		$result=$obj->autoRecognition('/tmp/Checkcode.jpg',$codeType);
		
		echo $result;
		//echo '����ͼƬʶ����Ϊ��'.$result;
	}else{
		//echo '��¼ʧ�ܣ��������Ϊ��'.$loginStatus.'<br/>';
	} */
  }
}
?>

