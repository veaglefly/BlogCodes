 <?php 
 require_once 'BaseController.php';
    require_once APPLICATION_PATH.'/models/Category.php';
    class CategoryController extends BaseController{
    	public function indexAction(){
    	 
    		$modelCategory=new Category();
    		 $order = 'path ASC';
    		 
    		$categories = $modelCategory->getCategories(null,$order,null);
    		$this->view ->categories = $categories ;
    		
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
       	
       			$dataComment = $this->_request->getPost(); // ��ȡ�����ύֵ
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
       						echo "�����ύ������";
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
       	
    }
    