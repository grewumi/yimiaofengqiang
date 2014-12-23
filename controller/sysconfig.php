<?php
class sysconfig extends spController{
    public function __construct(){
        parent::__construct();
        import('public-data.php');
        $this->sysCur = 1;
    }
    // 系统管理
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
                        echo '修改成功';
                    else
                        echo '修改失败';
                }
                break;
//            case 'del':
//                if($websites->delete(array('id'=>$id)))
//                    echo '删除成功';
//                else
//                    echo '删除失败';
//                break;
            default:
                if($this->spArgs("modAd")){
                    $res = array(
                       "name"=>$this->spArgs("name"),
                       "actType"=>$this->spArgs("actType"),
                       "rank"=>$this->spArgs("rank")
                    );
                    if($websites->create($res))
                        echo '添加成功';
                    else
                        echo '添加失败';
                }
                break;
        }

        $this->display("admin/sysconfig.html");
    }
}
?>

