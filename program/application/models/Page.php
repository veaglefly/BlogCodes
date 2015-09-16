<?php

class Page extends Zend_Db_Table_Abstract
{
	// 定义模型数据表
    protected $_name = 'core_pages';

    /**
     * 获取一个页面
     * 
     * 根据条件参数获取一个页面
     * 
     * @param unknown_type $where
     */
public function getPage($where = null)
    {

    	$select = $this->select()->setIntegrityCheck(false);
    	$select->from('core_pages', '*');
    	if (is_numeric($where)){
    		$select->where("core_pages.id = ?", $where);
    	}
    	if (is_array($where) & count($where) > 0){
        	foreach ($where as $key=>$value){
        		$select->where($key. ' = ?', $value);	        		
        	}
        	
    	}
    	$select->join('core_users', 'core_pages.uid = core_users.id','username');
    	$select->join('core_categories', 'core_pages.cid = core_categories.id','name');
    	$row = $this->fetchRow($select);
    	
    	if($row){
           	return $row;
        }
        else {
        	return null;
        }
    }
	/**
	 * 获取页面列表
	 * 
	 * 根据条件获取多条页面数据
	 * 
	 * @param unknown_type $where
	 * @param string $order
	 * @param int $limit
	 */
    public function getPages($where=null, $order=null, $paginator=true, $limit=null)
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
        if ($paginator == false){
        	$result = $this->fetchAll($select);
        }
        else{
        	$result = new Zend_Paginator_Adapter_DbTableSelect($select);
        }
        if($result->count() > 0){
            return $result;
        }
        else{
            return null;
        }
    }
    
    /**
     * 创建页面
     * 
     * 提交数据是数组格式，循环后键名为数据表字段，键值为提交值
     * 返回刚创建页面的id
     * 
     * @param array $data
     * @throws Zend_Exception
     */
    public function createPage($data = array())
    {
    	$row = $this->createRow();
    	if (count($data) > 0){
    		foreach ($data as $key=>$value){
    			$row->$key = $value;
    		}
    		$row->save();
    		return $row->id;
    	}
    	else{
    		throw new Zend_Exception('提交数据出错！');
    	}
    }
    
    /**
     * 更新页面
     * 
     * 提交数据是数组格式，循环后键名为数据表字段，键值为提交值
     * 返回更新页面的id
     * 
     * @param int $id
     * @param array $data
     * @throws Zend_Exception
     */
    public function updatePage($id, $data = array())
    {
    	$row = $this->find($id)->current();
    	if ($row){
    		if (count($data) > 0){
    			foreach ($data as $key=>$value){
    				$row->$key = $value;
    			}
    			$row->save();
    			return $id;
    		}
    		else{
    			throw new Zend_Exception('提交数据出错！');
    		}
    	}
    	else{
    		throw new Zend_Exception('更新数据出错！没有找到该页面！');
    	}  
    }

    /**
     * 删除页面
     * 
     * @param int $id
     */
    public function deletePage($id){
    	$row = $this->find($id)->current();
    	if ($row) {
    		$row->delete();
    	}
    	else{
    		throw new Zend_Exception('删除数据出错！没有找到该页面。');
    	}
    }

}


