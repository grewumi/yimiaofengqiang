<?php
class ajaxdata extends spController{
	public function index(){
		$pros = spClass("m_pro");
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
		$order = 'rank asc,postdt desc';
		$items = $pros->findAll($baseSql,$order,'',150);
		foreach($items as $k=>$v){
			echo 'update fstk_pro set rank="'.$v['rank'].'",postdt="'.$v['postdt'].'",pic="'.$v['pic'].'" where iid='.$v['iid'].';';
		}	
	}

}