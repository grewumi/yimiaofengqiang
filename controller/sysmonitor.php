<?php
class sysmonitor extends spController{
    public function __construct(){
        parent::__construct();
        import('StatisticClient.php');
    }
    public function index(){
        // ͳ�ƿ�ʼ
        StatisticClient::tick("User", 'getInfo');
        // ͳ�ƵĲ������ӿڵ����Ƿ�ɹ��������롢������־
        $success = true; $code = 0; $msg = '';
        // �����и�User::getInfo����Ҫ���
        $user_info = User::getInfo();
        if(!$user_info){
            // ���ʧ��
            $success = false;
            // ��ȡ�����룬����getErrCode()���
            $code = User::getErrCode();
            // ��ȡ������־������getErrMsg()���
            $msg = User::getErrMsg();
        }
        // �ϱ����
        StatisticClient::report('User', 'getInfo', $success, $code, $msg);
    }
}
?>

