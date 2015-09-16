<?php

class Tags extends Zend_Db_Table_Abstract
{
    protected $_name = "blog_tags";
    
    protected function _setupPrimaryKey()
    {
        $this->_primary = 'blog_id';
        parent::_setupPrimaryKey();
    } 
    
    public function createTags($id, $tags = null)
    {
    	$arrTags = explode(',', $tags);
    	foreach ($arrTags as $tag){
    		$row = $this->createRow();
    		$row->blog_id = $id;
    		$row->tag = $tag;
    		$row->save();
    	}
    }
    
    public function getTags($where = array())
    {
    	$select = $this->select();
    	if (count($where) > 0) {
    		foreach ($where as $key=>$value){
    			$select->where($key . " = ?", $value);
    		}
    	}
    	$result = $this->fetchAll($select);
    	if ($select) {
    		return $result;
    	}
    	else{
    		return null;
    	}
    }
    
    public function deleteTags($id)
    {
    	$where = "blog_id = ".$id;
    	$this->delete($where);
    	
    }

}
