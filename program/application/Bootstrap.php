<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    protected function _initNavigation(){
    	$this -> bootstrap('layout');
    	$layout = $this -> getResource('layout');
    	$view = $layout -> getView();
    	$config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/nav.xml');
    	$nav = new Zend_Navigation($config);
    	$view->navigation($nav);
    }
}

