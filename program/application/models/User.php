<?php
	class User extends Zend_Db_Table_Abstract{
		protected $_name = 'core_users';
		protected $_primary = 'id';
		public function createUser($userData){
			$row = $this -> createRow();
			if(count($userData) > 0){
				foreach($userData as $key => $value){
					switch($key){
						case 'password':
							$row -> $key = md5($value);
							break;
						case 'password2':
							break;
						default:
						$row ->$key = $value;
					}
				}
				$row -> role = 'user';
				$row -> status = 1;
				$row -> time_reg = time();
				$row -> save();
				return $row ->id;
			}else{
				return null;
			}
			
		}
		public function getUser($where){
		 if(is_numeric($where)){
    	    	$row = $this->find($where)->toArray();
    	    	
    	    	 
    	    }
    
    	    if(is_array($where) & count($where)>0){
    	        $select = $this->select();
    	        foreach ($where as $key => $value){
    	        	$select->where($key .'= ?',$value);
    	        }
    	        $row = $this->fetchRow($select)->toArray();
    	        
    	    }

    		if($row){
    			return $row;
    		}else{
    			return null;
    		}
		}
		
		public function loginTime($id){
			$row = $this -> find($id) -> current();
			if($row){
				$row->time_last = time();
				$row -> save();
			}else{
				return false;
			}
		}
		public function validUser($username)
		{
			$select = $this->select();
			$select->where("username = ?", $username);
			$result = $this->fetchRow($select);
			if ($result->username == $username){
				return $result->id;
			}
			else{
				return FALSE;
			}
		}
		
		
		/**
		 * 编辑用户
		 *
		 * 编辑用户信息
		 *
		 * @param int $id
		 * @param array $data
		 * @param string $password
		 */
		public function updateUser($id, $data, $password = null)
		{
			$row = $this->find($id)->current();
			if ($row){
				if (count($data) > 0){
					foreach ($data as $key=>$value){
						$row->$key = $value;
// 						 
					}
				}
				if ($password){
					$row->password = md5($password);
				}
				return $row->save();
				return $row->id;
			}
			else{
				return false;
			}
		}
		
		
		/**
		 * 更改密码
		 *
		 * @param int $id
		 * @param string $password
		 * @throws Zend_Exception
		 */
		public function changpassword($id, $password)
		{
			$row = $this->find($id)->current();
			if ($row){
				$row->password = md5($password);
				$row->save();
			}
			else {
				throw new Zend_Exception("用户不存在，更改密码未成功。");
			}
		}
		
		
	}