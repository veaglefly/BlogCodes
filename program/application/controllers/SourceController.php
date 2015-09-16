 <?php 
 	require_once 'BaseController.php';
 	require_once APPLICATION_PATH.'/models/Page.php';
    require_once APPLICATION_PATH.'/forms/Source.php';
    require_once APPLICATION_PATH.'/forms/Comment.php';
    require_once APPLICATION_PATH.'/models/Comment.php';
    require_once APPLICATION_PATH.'/models/Tags.php';
    require_once APPLICATION_PATH.'/models/TagsTotal.php';
 
class SourceController extends BaseController
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
        $where = array('type'=>'source');
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
        $formBlog = new Form_Source();
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
        			 
        			$this->_redirect('source/view/id/'.$newBlog);
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
        $formBlog = new Form_Source();
        if ($this->getRequest()->isPost()){
        	if ($formBlog->isValid($_POST)){
        		$blogData = $formBlog->getValues();
        		$blogData['uid'] = $this->identity->id;
        		$blogData['type'] = 'source';
        		$blogData['createtime'] = time();
        		$tags = str_replace("��", ",", $blogData['tags']); 
        		unset($blogData['tags']);
        		unset($blogData['�ύ']);
        		
        		$updateBlog = $modelBlog->updatePage($id, $blogData);
        		if ($updateBlog){
        			 
        			$this->_redirect('source/view/id/'.$updateBlog);
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
    	if (!$this->identity->id){
    		$this->_redirect('/user/login');
    	}
        $id = $this->_request->getParam('id');
        $modelBlog = new Page();
        $modelBlog->deletePage($id);
        $this->_redirect('/source');
    }

}
    