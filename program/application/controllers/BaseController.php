<?php
    //��һ�����࣬��������controller�������̳С�
    class BaseController extends Zend_Controller_Action{
    	public function init(){
    	    //��ʼ�����ݿ�������
    	    $url =  "../application/configs/application.ini";
            $dbconfig = new Zend_Config_Ini($url,"mysql");
            $register = Zend_Registry::getInstance();
            Zend_Registry::set('config',$dbconfig);
            $db = Zend_Db::factory($dbconfig->resources->db);
            Zend_Db_Table::setDefaultAdapter($db);
    	} 
    }