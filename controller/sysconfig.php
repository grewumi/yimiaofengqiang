<?php
class sysconfig extends spController{
    public function __construct(){
        parent::__construct();
        import('public-data.php');
        $this->sysCur = 1;
    }
    // һ���ɼ�˳��
    public function setcaiji(){
        //����ļ���
        $datalist=list_dir('./tmp/setcaiji/');
        foreach($datalist as $k=>$val){   
                unlink($val);
        }
        $websites = spClass("m_website")->findAll('','rank desc');
        foreach($websites as $k=>$v){
            $contents .= 'http://'.$_SERVER['HTTP_HOST'].'/uzcaiji/type/'.$v['ename'].'.html;';
        }
        $file = fopen('./tmp/setcaiji/setcaiji.txt',"w");
        if(!$file)
            echo '�ļ���ʧ��';
        //echo $sqlout_sec.'<br />';
        fwrite($file,iconv('gbk','utf-8',$contents));
        fclose($file);
    }
    // ϵͳ����
    public function index(){

        if(!$_SESSION['admin'])
            header("Location:/login.html");
        $websites = spClass("m_website");
        $cmd = $this->spArgs("cmd");
        $id = $this->spArgs("id");
        $this->caijiwebsite = $websites->findAll('','rank desc');
        switch($cmd){
            case 'mod':
                $site = $websites->find(array('id'=>$id));
                $this->site = $site;
                if($this->spArgs("modAd")){
                    $res = array(
                       "name"=>$this->spArgs("name"),
                       "actType"=>$this->spArgs("actType"),
                       "rank"=>$this->spArgs("rank")
                    );
                    if($websites->update(array('id'=>$id),$res))
                        echo '�޸ĳɹ�';
                    else
                        echo '�޸�ʧ��';
                }
                break;
//            case 'del':
//                if($websites->delete(array('id'=>$id)))
//                    echo 'ɾ���ɹ�';
//                else
//                    echo 'ɾ��ʧ��';
//                break;
            default:
                if($this->spArgs("modAd")){
                    $res = array(
                       "name"=>$this->spArgs("name"),
                       "actType"=>$this->spArgs("actType"),
                       "rank"=>$this->spArgs("rank")
                    );
                    if($websites->create($res))
                        echo '��ӳɹ�';
                    else
                        echo '���ʧ��';
                }
                break;
        }

        $this->display("admin/sysconfig.html");
    }
}
?>

