<?PHP 
	require_once APPLICATION_PATH . '/models/Category.php';
	require_once APPLICATION_PATH . '/models/Page.php';
	
	//�������ͱ���������������	
class Form_Source extends Zend_Form
{

	public function init()
    {
    	$this->setMethod('post');
    	
    	$title = $this->createElement('text', 'title');
    	$title->setLabel('���⣺');
    	$title->setRequired(TRUE);
    	$title->addValidator('stringLength', false, array(4, 100));
    	$title->addErrorMessage('����Ӧ��2-50�����֡�');
    	$this->addElement($title);
    	
    	// ����
        $category = $this->createElement('select', 'cid');
        $category->setLabel('���ࣺ');
        $category->setRequired(TRUE);
        $modelCategory = new Category();
        $where = array('fid'=>10);
        $categories = $modelCategory->getCategories($where);
        if ($categories != null ){
        	foreach($categories as $value){
        		$category->addMultiOption($value->id, $value->name);
        	}
        }
        $this->addElement($category);
    	
        // ��������
    	$body = $this->createElement('textarea', 'body');
    	$body->setLabel('���ݣ�');
    	$body->setAttribs(array('rows'=>30, 'cols'=>250 ));
    	$body->setRequired(TRUE);
    	$this->addElement($body);
    	 
    	// ����״̬
		$status = $this->createElement('checkbox', 'status');
		$status->setLabel('������');
		$status->setValue(1);
		$this->addElement($status);
    	
     
		
    	// �ύ��ť
        $submit = $this->createElement('submit', '�ύ');
		$this->addElement($submit);
		
		// ��װ����

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
	