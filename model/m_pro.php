<?php
class m_pro extends spModel{
	var $pk = "id";
	var $table = "fstk_pro";
        function getmypage($conditions,$order, $page, $pageSize){
           return $this->spPager($page,$pageSize)->findAll($conditions,$order);
        }
        function getC1data($conditions,$order){
            return $this->findAll($conditions,$order);
        }
        function getC2data($conditions,$order){
            return $this->findAll($conditions,$order);
        }
}
?>