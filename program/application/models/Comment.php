<?php

class Comment extends Zend_Db_Table_Abstract
{
	protected $_name = "blog_comments";
	
	public function createComment($pid, $name, $email = null, $comment)
	{
		$row = $this->createRow();
		$row->pid = $pid;
		$row->name = $name; 
		$row->email = $email;
		$row->comment = $comment;
		$row->save();
		return $row->id;
	}
	
	public function getComments($id)
	{
		$select = $this->select();
		$select->where('pid = ?', $id);
		$result = $this->fetchAll($select);
		if ($result){
			return $result;
		}
		else{
			return null;
		}
	}

}

