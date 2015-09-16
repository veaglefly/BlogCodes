<?php

class TagsTotal extends Zend_Db_Table_Abstract
{
	protected $_name = "blog_tags_total";
    
    public function createTag($tag)
    {
    	$select = $this->select();
    	$select->where('tag = ?', $tag);
    	$result = $this->fetchRow($select);
    	if ($result) {
    		$row = $this->find($tag)->current();
    		$row->total = $result->total+1;
    	}
    	else{
    		$row = $this->createRow();
			$row->tag = $tag;
			$row->total = 1;

    	}
    	$row->save();
    	return $row;
    }
    
    public function cutTag($tag)
    {
    	$select = $this->select();
    	$select->where('tag = ?', $tag);
    	$result = $this->fetchRow($select);
    	if ($result) {
    		if ($result->total > 1){
    			$result = $result->total -1;
    		}
    	}
    	else {
    		return null;
    	}
    }

}

