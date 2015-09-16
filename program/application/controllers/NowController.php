 <?php 
 	require_once 'BaseController.php';
 	require_once APPLICATION_PATH.'/models/Page.php';
    require_once APPLICATION_PATH.'/forms/Now.php';
    require_once APPLICATION_PATH.'/forms/Comment.php';
    require_once APPLICATION_PATH.'/models/Comment.php';
    require_once APPLICATION_PATH.'/models/Tags.php';
    require_once APPLICATION_PATH.'/models/TagsTotal.php';
 
class NowController extends BaseController
{

//     public function init()
//     {
//         // 获取身份
//         $auth = Zend_Auth::getInstance();
//         if ($auth->hasIdentity()){
//         	$this->identity = $auth->getIdentity();
//         }
        
//         //$this->_helper->cache(array('index', 'view'), array('gook'));
//     }
    
    public function indexAction()
    {
    	// 博客列表
        $modelBlog = new Page();
        $where = array('type'=>'now');
        $blogs = $modelBlog->getPages($where);
        
        $paginator = new Zend_Paginator($blogs);
		$paginator->setItemCountPerPage(5);
		$paginator->setPageRange(5);
		// 获得当前显示的页码
		$page = $this->_request->getParam('page');
		$paginator->setCurrentPageNumber($page);
		// 渲染到视图
		$this->view->blogs = $paginator;
		$this->_helper->cache(array('index', 'view'), array('gook'));
    }

    public function createAction()
    {
    	$auth = Zend_Auth::getInstance();
              if ($auth->hasIdentity()){
            	$this->identity = $auth->getIdentity();
             }
     	if (!$this->identity->id){
      		$this->_redirect('/user/login');
      	}
        $formBlog = new Form_Now();
        if ($this->getRequest()->isPost()){
        	if ($formBlog->isValid($_POST)){
        		$blogData = $formBlog->getValues();
        		$blogData['uid'] = $this->identity->id;
        		$blogData['type'] = 'now';
        		$blogData['createtime'] = time();
        		$tags = str_replace("，", ",", $blogData['tags']); 
        		unset($blogData['tags']);
        		unset($blogData['提交']);
        		
        		$modelBlog = new Page();
        		$newBlog = $modelBlog->createPage($blogData);
        		
        		if ($newBlog){
        			if ($tags != null){
	        			$modelTags = new Tags();
	        			$modelTags->createTags($newBlog, $tags);
	        			$modelTagsTotal = new TagsTotal();
	        			$arrTags = explode(",", $tags);
	        			if (count($arrTags) > 0){
	        				foreach ($arrTags as $value){
	        					$modelTagsTotal->createTag($value);
	        				}
	        			}
	        			else{
	        				$modelTagsTotal->createTag($tags);
	        			}
	        			
        			}
        			$this->_redirect('blog/view/id/'.$newBlog);
        		}
        	}
        }
        $this->view->formBlog = $formBlog;
       // $this->_helper->cache(array('index', 'view'), array('gook'));
    }

    public function updateAction()
    {
    	$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()){
    		$this->identity = $auth->getIdentity();
    	}
        if (!$this->identity->id){
    		$this->_redirect('/user/login');
    	}
    	$id = $this->_request->getParam('id');
        $modelBlog = new Page();
        $formBlog = new Form_Blog();
        if ($this->getRequest()->isPost()){
        	if ($formBlog->isValid($_POST)){
        		$blogData = $formBlog->getValues();
        		$blogData['uid'] = $this->identity->id;
        		$blogData['type'] = 'now';
        		$blogData['createtime'] = time();
        		$tags = str_replace("，", ",", $blogData['tags']); 
        		unset($blogData['tags']);
        		unset($blogData['提交']);
        		
        		$updateBlog = $modelBlog->updatePage($id, $blogData);
        		if ($updateBlog){
        			if ($tags != null){
	        			$modelTags = new Tags();
	        			$modelTags->deleteTags($id); // 清空原tags
	        			$modelTags->createTags($updateBlog, $tags);
	        			$modelTagsTotal = new TagsTotal();
	        			$arrTags = explode(",", $tags);
	        			if (count($arrTags) > 0){
	        				foreach ($arrTags as $value){
	        					$modelTagsTotal->cutTag($value); // 从total中减去
	        					$modelTagsTotal->createTag($value); //重新创建
	        				}
	        			}
	        			else{
	        				$modelTagsTotal->createTag($tags);
	        			}
        			}
        			$this->_redirect('blog/view/id/'.$updateBlog);
        		}
        	}
        }
        else {
        	$blog = $modelBlog->getPage($id);
        	if ($this->identity->id != $blog->uid){
        		echo "你不是本博客的作者，不能对本博客进行编辑操作。";
        		exit();
        	}
        	
        	$arrBlog = $blog->toArray();
        	// 该blog的tags
        	$modelTags = new Tags();
        	$where = array('blog_id'=>$id);
        	$tags = $modelTags->getTags($where);
        	if (count($tags) > 0){
        		$strTags = '';
        		foreach ($tags as $tag){
        			$strTags .= $tag->tag.",";
        		}
        		$strTags = rtrim($strTags, ",");
        	}
        	$arrBlog['tags'] = $strTags;
        	
        	$formBlog->populate($arrBlog);
        }
        $this->view->formBlog = $formBlog;
      //  $this->_helper->cache(array('index', 'view'), array('gook'));
    }

    public function viewAction()
      {

    	
        $id = $this->_request->getParam('id');
      $captchaCode = $this->_request->getParam('captcha_code');
        
        $modelBlog = new Page();
        $blog = $modelBlog->getPage($id);
        if ($blog){
        	$this->view->blog = $blog;			
        	// tags
        	$modelTags = new Tags();
        	$where = array('blog_id' => $id);
        	$tags = $modelTags->getTags($where);
        	if ($tags){
        		$this->view->tags = $tags;
        	} 
        	
        	
        	// 评论
        	$modelComment = new Comment();
        	$comments = $modelComment->getComments($id);
        	$this->view->comments = $comments;
        	 
        	$dataComment = $this->_request->getPost(); // 获取表单提交值
        	if ($dataComment){
        		if ($dataComment['captcha'] == $captchaCode){
        			// 定义过滤规则
        			$filters = array(
        					'name' => array('StringTrim'),
        					'comment'=>'StripTags'
        			);
        			// 定义验证规则
        			$validators=array(
        					'name'=>array(
        							array('StringLength',3,16),
        							'NotEmpty',
        							Zend_Filter_Input::MESSAGES=>array(array(
        									Zend_Validate_StringLength::INVALID=>"请输入一个合法的字符串",
        									Zend_Validate_StringLength::TOO_SHORT=>"请输入字符长度为3-16",
        									Zend_Validate_StringLength::TOO_LONG=>"请输入字符长度为3-16"
        							))
        					),
        					'email'=>array('EmailAddress',
        							Zend_Filter_Input::MESSAGES=>array(array(
        									Zend_Validate_EmailAddress::INVALID_FORMAT=>"邮件格式不正确，请重新输入。"
        							))
        					),
        					'comment'=>array()
        			);
        				
        			// 实例化过滤器并进行过滤验证
        			$data = $_POST;
        			$filterPost = new Zend_Filter_Input($filters, $validators, $data);
        			if ($filterPost->hasInvalid() || $filterPost->hasMissing()) {
        				$messages = $filterPost->getMessages();
        				foreach ($messages as $message){
        					foreach ($message as $value){
        						echo $value."<br />";
        					}
        				}
        			}
        			// 将经过验证的数据写入数据库
        			$modelComment = new Comment();
        			$newComment = $modelComment->createComment($pid = $id, $filterPost->name, $filterPost->email, $filterPost->comment);
        			if ($newComment){
        				$this->_redirect('/blog/view/id/'.$id);
        			}
        			else{
        				echo "评论提交出错！";
        			}
        		}
        		else{
        			echo "验证码错误，请刷新后重新输入。";
        		}
        	}
        		
        	// 生成验证码
        	$this->captcha_session = new Zend_Session_Namespace('captcha'); //在默认构造函数里实例化
        	$captcha = new Zend_Captcha_Image(array(
        			'font'=>'images/SIMYOU.TTF',     // 字体文件路径
        			'session' => $this->captcha_session,    // 验证码session值
        			'fontsize' => 15, // 字号
        			'imgdir' => 'images/code/',    // 验证码图片存放位置
        			'width' => 120,    // 图片宽
        			'height' => 30,    // 图片高
        			'gcFreq' => 3,    // 删除生成的旧的验证码图片的随机几率
        			'dotNoiseLevel' => 5,    // 躁点数
        			'lineNoiseLevel' => 1,    // 线条
        			'wordlen'=>4 )    // 字母数
        	);
        	
        	$captcha->generate(); // 生成图片
        	
        	// 界面方式
        	$this->view->img_dir = $captcha->getImgDir();
        	$this->view->captcha_id = $captcha->getId(); //图片文件名，md5编码
        	$this->view->captcha_code = $captcha->getWord();
	        $this->view->id = $id;
        }
        else{
			echo "该博客文章不存在！";        
        }
    }

    public function deleteAction()
    {
    	$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()){
    		$this->identity = $auth->getIdentity();
    	}
    	if (!$this->identity->id){
    		$this->_redirect('/user/login');
    	}
        $id = $this->_request->getParam('id');
        $modelBlog = new Page();
        $modelBlog->deletePage($id);
        $this->_redirect('/now');
    }

}
    