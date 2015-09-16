 <?php 
 	require_once 'BaseController.php';
 	require_once APPLICATION_PATH.'/models/Page.php';
    require_once APPLICATION_PATH.'/forms/Blog.php';
    require_once APPLICATION_PATH.'/forms/Comment.php';
    require_once APPLICATION_PATH.'/models/Comment.php';
    require_once APPLICATION_PATH.'/models/Tags.php';
    require_once APPLICATION_PATH.'/models/TagsTotal.php';
 
class ArticleController extends BaseController
{

//     public function init()
//     {
//         // ��ȡ���
//         $auth = Zend_Auth::getInstance();
//         if ($auth->hasIdentity()){
//         	$this->identity = $auth->getIdentity();
//         }
        
//         //$this->_helper->cache(array('index', 'view'), array('gook'));
//     }
    
    public function indexAction()
    {
    	// �����б�
        $modelBlog = new Page();
        $where = array('type'=>'blog');
        $blogs = $modelBlog->getPages($where);
        
        $paginator = new Zend_Paginator($blogs);
		$paginator->setItemCountPerPage(10);
		$paginator->setPageRange(5);
		// ��õ�ǰ��ʾ��ҳ��
		$page = $this->_request->getParam('page');
		$paginator->setCurrentPageNumber($page);
		// ��Ⱦ����ͼ
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
        $formBlog = new Form_Blog();
        if ($this->getRequest()->isPost()){
        	if ($formBlog->isValid($_POST)){
        		$blogData = $formBlog->getValues();
        		$blogData['uid'] = $this->identity->id;
        		$blogData['type'] = 'blog';
        		$blogData['createtime'] = time();
        		$tags = str_replace("��", ",", $blogData['tags']); 
        		unset($blogData['tags']);
        		unset($blogData['�ύ']);
        		
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
        		$blogData['type'] = 'blog';
        		$blogData['createtime'] = time();
        		$tags = str_replace("��", ",", $blogData['tags']); 
        		unset($blogData['tags']);
        		unset($blogData['�ύ']);
        		
        		$updateBlog = $modelBlog->updatePage($id, $blogData);
        		if ($updateBlog){
        			if ($tags != null){
	        			$modelTags = new Tags();
	        			$modelTags->deleteTags($id); // ���ԭtags
	        			$modelTags->createTags($updateBlog, $tags);
	        			$modelTagsTotal = new TagsTotal();
	        			$arrTags = explode(",", $tags);
	        			if (count($arrTags) > 0){
	        				foreach ($arrTags as $value){
	        					$modelTagsTotal->cutTag($value); // ��total�м�ȥ
	        					$modelTagsTotal->createTag($value); //���´���
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
        		echo "�㲻�Ǳ����͵����ߣ����ܶԱ����ͽ��б༭������";
        		exit();
        	}
        	
        	$arrBlog = $blog->toArray();
        	// ��blog��tags
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
        	
        	
        	// ����
        	$modelComment = new Comment();
        	$comments = $modelComment->getComments($id);
        	$this->view->comments = $comments;
        	 
        	$dataComment = $this->_request->getPost(); // ��ȡ���ύֵ
        	if ($dataComment){
        		if ($dataComment['captcha'] == $captchaCode){
        			// ������˹���
        			$filters = array(
        					'name' => array('StringTrim'),
        					'comment'=>'StripTags'
        			);
        			// ������֤����
        			$validators=array(
        					'name'=>array(
        							array('StringLength',3,16),
        							'NotEmpty',
        							Zend_Filter_Input::MESSAGES=>array(array(
        									Zend_Validate_StringLength::INVALID=>"������һ���Ϸ����ַ���",
        									Zend_Validate_StringLength::TOO_SHORT=>"�������ַ�����Ϊ3-16",
        									Zend_Validate_StringLength::TOO_LONG=>"�������ַ�����Ϊ3-16"
        							))
        					),
        					'email'=>array('EmailAddress',
        							Zend_Filter_Input::MESSAGES=>array(array(
        									Zend_Validate_EmailAddress::INVALID_FORMAT=>"�ʼ���ʽ����ȷ�����������롣"
        							))
        					),
        					'comment'=>array()
        			);
        				
        			// ʵ���������������й�����֤
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
        			// ��������֤������д�����ݿ�
        			$modelComment = new Comment();
        			$newComment = $modelComment->createComment($pid = $id, $filterPost->name, $filterPost->email, $filterPost->comment);
        			if ($newComment){
        				$this->_redirect('/blog/view/id/'.$id);
        			}
        			else{
        				echo "�����ύ����";
        			}
        		}
        		else{
        			echo "��֤�������ˢ�º��������롣";
        		}
        	}
        		
        	// ������֤��
        	$this->captcha_session = new Zend_Session_Namespace('captcha'); //��Ĭ�Ϲ��캯����ʵ����
        	$captcha = new Zend_Captcha_Image(array(
        			'font'=>'images/SIMYOU.TTF',     // �����ļ�·��
        			'session' => $this->captcha_session,    // ��֤��sessionֵ
        			'fontsize' => 15, // �ֺ�
        			'imgdir' => 'images/code/',    // ��֤��ͼƬ���λ��
        			'width' => 120,    // ͼƬ��
        			'height' => 30,    // ͼƬ��
        			'gcFreq' => 3,    // ɾ�����ɵľɵ���֤��ͼƬ���������
        			'dotNoiseLevel' => 5,    // �����
        			'lineNoiseLevel' => 1,    // ����
        			'wordlen'=>4 )    // ��ĸ��
        	);
        	
        	$captcha->generate(); // ����ͼƬ
        	
        	// ���淽ʽ
        	$this->view->img_dir = $captcha->getImgDir();
        	$this->view->captcha_id = $captcha->getId(); //ͼƬ�ļ�����md5����
        	$this->view->captcha_code = $captcha->getWord();
	        $this->view->id = $id;
        }
        else{
			echo "�ò������²����ڣ�";        
        }
    }

    public function deleteAction()
    {
    	$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()){
    		$this->identity = $auth->getIdentity();
    	}
        $id = $this->_request->getParam('id');
        $modelBlog = new Page();
        $modelBlog->deletePage($id);
        $this->_redirect('/blog');
    }

}
    