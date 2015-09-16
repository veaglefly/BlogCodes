<?php
    //做一个父类，供其他的controller的类来继承。
    class BaseController extends Zend_Controller_Action{
    	public function init(){
    	    //初始化数据库适配器
    	    $url =  "../application/configs/application.ini";
            $dbconfig = new Zend_Config_Ini($url,"mysql");
            $register = Zend_Registry::getInstance();
            Zend_Registry::set('config',$dbconfig);
            $db = Zend_Db::factory($dbconfig->resources->db);
            Zend_Db_Table::setDefaultAdapter($db);
    	} 
    }