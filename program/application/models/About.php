<?php
class About extends Zend_Db_Table_Abstract
{
	protected $_name = 'core_pages';
	public function getPages($where=null, $order=null, $limit=null)
	{
		$select = $this->select();
		if (is_string($where)){
			$select->where($where);
		}
		if(is_array($where) & count($where) > 0){
			foreach($where as $key=>$value){
				$select->where($key.'=?', $value);
			}
		}
		if($order){
			$select->order($order);
		}
		if($limit){
			$select->limit($limit);
		}
		 
		$result = $this->fetchAll($select);
		 
		if($result->count() > 0){
			return $result;
		}
		else{
			return null;
		}
	}
	
}