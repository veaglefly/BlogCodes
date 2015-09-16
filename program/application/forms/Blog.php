<?PHP 
	require_once APPLICATION_PATH . '/models/Category.php';
	require_once APPLICATION_PATH . '/models/Page.php';
	
	//创建博客表单，用来发布博客	
class Form_Blog extends Zend_Form
{

	public function init()
    {
    	$this->setMethod('post');
    	
    	$title = $this->createElement('text', 'title');
    	$title->setLabel('标题：');
    	$title->setRequired(TRUE);
    	$title->addValidator('stringLength', false, array(4, 100));
    	$title->addErrorMessage('标题应有2-50个汉字。');
    	$this->addElement($title);
    	
    	// 分类
        $category = $this->createElement('select', 'cid');
        $category->setLabel('分类：');
        $category->setRequired(TRUE);
        $modelCategory = new Category();
        $where = array('fid'=>2);
        $categories = $modelCategory->getCategories($where);
        if ($categories != null ){
        	foreach($categories as $value){
        		$category->addMultiOption($value->id, $value->name);
        	}
        }
        $this->addElement($category);
    	
        // 博客内容
    	$body = $this->createElement('textarea', 'body');
    	$body->setLabel('内容：');
    	$body->setAttribs(array('rows'=>30, 'cols'=>250 ));
    	$body->setRequired(TRUE);
    	$this->addElement($body);
    	
    	// tags标签
    	$tags = $this->createElement('text', 'tags');
    	$tags->setLabel('tags');
    	$this->addElement($tags);
    	
    	// 发布状态
		$status = $this->createElement('checkbox', 'status');
		$status->setLabel('发布：');
		$status->setValue(1);
		$this->addElement($status);
    	
    	// 评论
		$allow = $this->createElement('checkbox', 'comment');
		$allow->setLabel('允许评论：');
		$allow->setValue(1);
		$this->addElement($allow);
		
    	// 提交按钮
        $submit = $this->createElement('submit', '提交');
		$this->addElement($submit);
		
		// 表单装饰器

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data'=>'HtmlTag'), array('tag'=>'td')),
            array('Label',array('tag'=>'td')),
            array(array('row'=>'HtmlTag'),array('tag'=>'tr')),
        	));
        
        $this->setDecorators(array(
        		'FormElements',
        		array('HtmlTag',array('tag'=>'table', 'class'=>'sheet')), 'Form'
        ));
		
	}

}
	