<?php
class mkhtml extends spController{
	public function __construct() {
		parent::__construct();
		$pros = spClass("m_pro");
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
		$order = 'rank asc,postdt desc';
                $page = $this->spArgs('page',1);
		$items = $pros->spPager($page,45)->findAll($baseSql,$order);
		$this->items = $items;
//		var_dump($items);
	}
	public function index(){
            
		$this->display("front/mizhe.html");
	}
}