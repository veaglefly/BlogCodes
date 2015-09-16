<?php

class Form_Comment extends Zend_Form
{

    public function init()
    {   	
        $comment = $this->createElement('textarea','comment');
		$comment->setLabel('内容：');
		$this->addElement($comment);
		
    	// 提交按钮
        $submit = $this->createElement('submit', '提交');
        $this->addElement($submit);
		
		 
		 $this->setElementDecorators(array(
	            'ViewHelper',
	            'Errors',
		 		array('decorator' => array('tr' => 'HtmlTag'), 'options' => array('tag' => 'tr')),
	            array('decorator' => array('td' => 'HtmlTag'), 'options' => array('tag' => 'td'))
	   	)); 
    }

}