<?php
class Category extends Zend_Db_Table_Abstract{
	protected $_name = 'core_categories';
	public function getCategories($where = array(),$order = null,$limit = null){
		$select = $this-> select();
		if(count($where)>0){
			foreach ($where as $key => $value){
				$select->where($key .'= ?',$value);
			}
		}
		if($order){
			$select -> order($order);
		}
		if($limit){
			$select-> limit($limit);
		}
		$result = $this -> fetchAll($select);
		 
		if($result -> count()>0){
			return $result;
		}else{
			return null;
		}
	
	}
}