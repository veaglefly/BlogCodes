<?php 
	require_once 'BaseController.php';
    require_once APPLICATION_PATH.'/forms/User.php';
    require_once APPLICATION_PATH.'/models/User.php';
 
    class UserController extends BaseController{
		public function indexAction(){
			
		}
		//用户详细信息页面
		public function accountAction(){
			$id = $this -> _request->getParam('id');
			$modelUser = new User();
			$user = $modelUser->getUser($id); 
			 
			$this -> view -> user = $user;
			
		}
		public function registerAction(){
		//	$formRegister= new Form_User();
			$formUser = new Form_User();
			$formUser -> removeElement('avatar');
			$formUser -> removeElement('status');
			$formUser -> removeElement('role');
		 	$formUser -> removeElement('profile');
			 
			if($this -> getRequest()->isPost()){
			
				if(!$formUser->isValid($_POST)){
 
					$userData = $formUser->getValues();	 
					$modelUser = new User();
					$newUser = $modelUser->createUser($userData);
					 
					if($newUser){
						$this ->_redirect('/user/account/id/'.$newUser);
					}
				}
			}
			$this -> view -> formUser = $formUser;
			//$this -> view -> formUser = $formRegister;
		}
		//使用Zend_Auth组件实现登录
		public function loginAction(){
			$formLogin = new Form_User();
			$formLogin -> removeElement('sex');
			$formLogin -> removeElement('email');
			$formLogin -> removeElement('password2');
			$formLogin -> removeElement('avatar');
			$formLogin -> removeElement('status');
			$formLogin -> removeElement('role');
			$formLogin -> removeElement('profile');
			
			
			if($this -> getRequest()->isPost()){
				if($formLogin -> isValid($_POST)){
 
					$data = $formLogin -> getValues();

					//取得默认的数据库适配器
					$db = Zend_Db_Table::getDefaultAdapter();
					//实例化一个Auth适配器
					$authAdapter = new Zend_Auth_Adapter_DbTable($db,'core_users','username','password');
					//设置认证用户名和密码
					$authAdapter -> setIdentity($data['username']);
					$authAdapter -> setCredential(md5($data['password']));
					$result = $authAdapter -> authenticate();
					
					if($result->isValid()){
						 
						$auth = Zend_Auth::getInstance();
						//存储用户信息
						$storage = $auth -> getStorage();
						$storage -> write($authAdapter->getResultRowObject(
							array('id','username','role')
						));
						$id = $auth -> getIdentity() ->id;
						$modelUser = new User();
						$loginTime = $modelUser -> loginTime($id);
 
						return $this-> _redirect('/user/account/id/'.$id);
						
						
					}else{
						$this -> view -> loginMessage = '你的帐户名或密码不符';
					}
					
				
				}
			}
			
			$this -> view -> formLogin = $formLogin;
		}
		//用户面板
		public function panelAction(){
			$auth = Zend_Auth::getInstance();
			if($auth -> hasIdentity()){
				$this -> view -> identity = $auth ->getIdentity();
			}
		}
		public function updateAction(){
	 
			$id = $this->_request->getParam('id');
			$formUser = new Form_User();
			$formUser->removeElement('username');
			$formUser->removeElement('password');
			$formUser->removeElement('password2');
			$formUser->removeElement('role');
			$formUser->removeElement('status');
 
			$formUser -> removeElement('avatar');
			$modelUser = new User();
			if ($this->getRequest()->isPost()){
				if ($formUser->isValid($_POST)){
					// 上传和获取图片信息
					 $adapter = new Zend_File_Transfer_Adapter_Http();
					//$path = APPLICATION_PATH.'/../public/uploads/'.date("Y-m").'/avatar/';
					//$folder = new Zend_Search_Lucene_Storage_Directory_Filesystem($path); //如果文件夹不存在，则创建
					//$fileInfo = $adapter->getFileInfo();//获取基本配置
					//$extName = $this->_getExtension($fileInfo);//获取扩展名
					//$filename = time().'.'.$extName;//重命名
					//$adapter->addFilter('Rename', array('target' => $filename, 'overwrite' => true));//执行重命名
					//$adapter->setDestination($path); //设定保存路径
					//$adapter->addValidator('Size',FALSE, 128000 ); // 上传文件大小
					//$adapter->addValidator('Extension', FALSE, array('jpg', 'gif', 'png', 'jpeg')); //扩展名验证
		
					// 获取其它表单数据
					$data = array();
					$data['sex'] = $formUser->getValue('sex');
					$data['email'] = $formUser->getValue('email');
					$data['profile'] = $formUser->getValue('profile');
		
		
					 if ($adapter->receive()) { //如执行上传
						 $updateUser = $modelUser->updateUser( $id, $data);
           			   }
				  
							
				 	}
		
					if (updateUser){
						$this->_redirect('user/account/id/'.$id.'/'.$updateUser);
					}
					else{
						throw new Zend_Exception('更新用户信息出错！');
					}
				}
					
			 
			$user = $modelUser->find($id)->current();
			$formUser->populate($user->toArray());
			$this->view->user = $user;
			$this->view->formUser = $formUser;
		}
		
		/**
		 * 更改密码
		 *
		 */
		public function changePasswordAction()
		{
			$id = $this->_request->getParam('id');
			$formUser = new Form_User();
			$formUser->removeElement('username');
			$formUser->removeElement('sex');
			$formUser->removeElement('email');
			$formUser->removeElement('avatar');
			$formUser->removeElement('profile');
			$formUser->removeElement('role');
			$formUser->removeElement('star');
			$formUser->removeElement('status');
			if ($this->getRequest()->isPost()){
				if ($formUser->isValid($_POST)){
					$modelUser = new User();
					$newpsw = $modelUser->changPassword($id, $formUser->getValue('password'));
		
					return $this->_forward('account');
				}
			}
			$this->view->formUser = $formUser;
		}
		
		/**
		 * 用户注销
		 *
		 */
		public function logoutAction()
		{
			// 注销用户
			$authAdapter = Zend_Auth::getInstance();
			$authAdapter->clearIdentity();
		}
		
}