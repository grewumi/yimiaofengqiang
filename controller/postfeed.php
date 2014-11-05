<?php
class postfeed extends spController{
    public function __construct(){
        parent::__construct();
    }
    function graphicfeedpost(){
        $pros = spClass("m_pro");
        $items = $pros->findAll('st<=curdate() and et>=curdate() and ischeck=1 and type!=87 and classification=1','rank asc,postdt desc','iid','8');

        foreach($items as $k=>$v){
            $iids[] = $v['iid'];
        }
        graphicfeedpost($iids,'http://yinxiang.uz.taobao.com/d/getgraphicfeed');
    }    
}

