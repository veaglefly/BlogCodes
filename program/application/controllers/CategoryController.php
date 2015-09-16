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
       	
    }
    