<?php
class postfeed extends spController{
    public function __construct(){
        parent::__construct();
    }
    function graphicfeedpost(){
        $control = spClass('m_control');
        $feed_control = $control->find(array('type'=>3));
        if($feed_control['isuse']){
            exit();
        }else{
            $pros = spClass("m_pro");
            $items = $pros->findAll('st<=curdate() and et>=curdate() and ischeck=1 and type!=87 and classification=2','rank asc,postdt desc','iid','8');

            foreach($items as $k=>$v){
                $iids[] = $v['iid'];
            }
            graphicfeedpost($iids,'http://yinxiang.uz.taobao.com/d/getgraphicfeed');
            shuffle($iids);//Êý×éÂÒÐò
            graphicfeedpost($iids,'http://youpinba.uz.taobao.com/d/getgraphicfeed');
            shuffle($iids);
            graphicfeedpost($iids,'http://okbuy.uz.taobao.com/d/getgraphicfeed');
            shuffle($iids);
            graphicfeedpost($iids,'http://mplife.uz.taobao.com/d/getgraphicfeed');
            $control->update(array('type'=>2),array('isuse'=>1));
        }
    }    
}

