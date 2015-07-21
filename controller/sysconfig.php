<?php
class sysconfig extends spController{
    public function __construct(){
        parent::__construct();
        import('public-data.php');
        $this->sysCur = 1;
    }
    // 一键采集顺序
    public function setcaiji(){
        //清空文件夹
        $datalist=list_dir('./tmp/setcaiji/');
        foreach($datalist as $k=>$val){   
                unlink($val);
        }
        $websites = spClass("m_website")->findAll(array('iscaiji'=>1),'rank desc');
        
        foreach($websites as $k=>$v){
            $contents = "http://".$_SERVER['HTTP_HOST']."/uzcaiji/type/".$v['ename'].".html;\n";
            $file = fopen('./tmp/setcaiji/setcaiji.txt',"a+");
            if(!$file)
                echo '文件打开失败';
            else
                fwrite($file,iconv('gbk','utf-8',$contents));
            $contents = null;
            fclose($file);
        }
        
    }
    // 系统管理
    public function index(){

        if(!$_SESSION['admin'])
            header("Location:/login.html");
        $websites = spClass("m_website");
        $m_tags = spClass("m_tags");
        $cmd = $this->spArgs("cmd");
        $id = $this->spArgs("id");
        $mode = $this->spArgs("mode")?$this->spArgs("mode"):1;
        $set = $this->spArgs("set");
        $this->set = $set;
        switch($set){
            case 'tags':
                import('pscws23/pscws3.class.php');
                $cws = spClass("PSCWS3",array('dictfile'=>'dict/dict.xdb'));
                $cws->set_ignore_mark(true);//忽略标点符号
                $cws->set_autodis(true);//人名识别
                $mydata = trim('陈凯歌并不是《无极》的唯一著作权人，一部电影的整体版权归电影制片厂所有。');
                // 执行切分, 分词结果数组执行 words_cb()
                $cws->segment($mydata,'words_cb');
                var_dump($cws->segment($mydata));
                
                $this->tags = $m_tags->findAll();
                switch($cmd){
                 case 'mod':
                     $site = $m_tags->find(array('id'=>$id));
                     $this->site = $site;
                     if($this->spArgs("modAd")){
                         $res = array(
                            "tag"=>$this->spArgs("tag")
                         );
                         if($m_tags->update(array('id'=>$id),$res))
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
                            "tag"=>$this->spArgs("tag")
                         );
                         if($m_tags->create($res))
                             echo '添加成功';
                         else
                             echo '添加失败';
                     }
                     break;
                }
                break;
            case 'caiji':
                if($mode==1)
                     $this->caijiwebsite = $websites->findAll(array('iscaiji'=>1),'rank desc');
                elseif($mode==2)
                    $this->caijiwebsite = $websites->findAll(array('iscaiji'=>0),'rank desc');
                 switch($cmd){
                     case 'mod':
                         $site = $websites->find(array('id'=>$id));
                         $this->site = $site;
                         if($this->spArgs("modAd")){
                             $res = array(
                                "name"=>$this->spArgs("name"),
                                "actType"=>$this->spArgs("actType"),
                                "iscaiji"=>$this->spArgs("iscaiji"),
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
                                "iscaiji"=>$this->spArgs("iscaiji"),
                                "rank"=>$this->spArgs("rank")
                             );
                             if($websites->create($res))
                                 echo '添加成功';
                             else
                                 echo '添加失败';
                         }
                         break;
            }
            // end caiji set
            default:
                ;
        }
        

        $this->display("admin/sysconfig.html");
    }
}
?>

