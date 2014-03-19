<?php
class mkhtml extends spController{
	public function index(){
		$pros = spClass("m_pro");
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
		$order = 'rank asc,postdt desc';
		$items = $pros->findAll($baseSql,$order,'',45);
		$this->items = $items;
		$this->display("front/mizhe.html");
	}
}