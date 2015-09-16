<?php
	class Form_User extends Zend_Form{
		public function init(){
			$this -> setMethod('post');
			$username = $this -> createElement('text','username');
			$username -> setLabel('用户：');
			$username -> setRequired(TRUE);
			$username -> addValidator('stringLength',false,array(5,20));
			$username -> addErrorMessage('用户名要求英文5-20个字母或2-6个汉字');
			$this -> addElement($username);
			
			//密码
			$password = $this -> createElement('password','password');
			$password -> setLabel('密码：');
			$password -> setRequired(TRUE);
			$password -> addValidator('stringLength',false,array(6));
			$password -> addErrorMessage('密码至少要求6个字符');
			$this -> addElement($password);
			//确认密码
			$password2 = $this -> createElement('password','password2');
			$password2 -> setLabel('确认密码：');
			$password2 -> setRequired(TRUE);
			$password2 -> addValidator('identical',false,array('token'=>'password'));
			$password2 -> addErrorMessage('两次输入的密码不相同！');
			$this -> addElement($password2);
			//性别
			$sex = $this -> createElement('radio', 'sex');
			$sex -> setLabel('性别：');
			$sex -> addMultiOptions(array(1=>'男',0=>'女'));
			$sex -> setSeparator(" ");
			$this -> addElement($sex);
			//电子邮件
			$email = $this -> createElement('text','email');
			$email -> setLabel('电子邮箱：');
			$email -> setRequired(TRUE);
			$password2 -> addValidator('EmailAddress');
			$email -> addErrorMessage('请输入正确的电子邮箱 ！！');
			$this -> addElement($email);
			//个人简介
			$profile = $this -> createElement('textarea', 'profile');
			$profile -> setLabel('个人简介：');
			$profile -> setAttribs(array('rows'=>4,'cols'=>50));
			$this -> addElement($profile);
			//用户头像
			$avatar = $this -> createElement('file', 'avatar');
			$avatar ->setLabel('用户头像：');
			$avatar ->setRequired(FALSE);
			$this -> addElement($avatar);
			//用户状态
			$status = $this -> createElement('select', 'status');
			$status -> setLabel('用户状态：');
			$status -> addMultiOptions(array(
				'0' => '锁定',
				'1' => '激活',
			));
			$status-> setRequired(TRUE);
			$this -> addElement($status);
			//用户角色
			$role = $this -> createElement('select', 'role');
			$role -> setLabel('选择角色：');
			$role -> addMultiOptions(array(
				'user' => '用户',
				'student' =>'学生',
				'teacher' => '老师',
				'author'=>'作者',
				'editor'=> '编辑',
				'admin'=>'管理员'
			));
			$role -> setRequired(TRUE);
			$this -> addElement($role);
			
			//提交
			$submit = $this -> createElement('submit', '提交');
			$this -> addElement($submit);
		}
	}