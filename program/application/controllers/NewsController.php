<?php
    require_once 'BaseController.php';
    require_once APPLICATION_PATH.'/models/Page.php';
class NewsController extends Zend_Controller_Action
{
 
    public function indexAction()
    {
        // action body
        $modelPage = new Page(); //实例化模型对象
        $where = array('star'=>4,'top'=>1); // 定义查询条件
        $newsStar = $modelPage->getPage($where); //使用模型的getPage方法获取文章
        $this->view->newsStar = $newsStar; // 输出到view视图
        
        // 新闻文章列表
        $where_list = array('star'=>4, 'top'=>0);
        $order = 'createtime DESC';
        $limit = 5;
        $newsList = $modelPage->getPages($where_list, $order, $limit);
        $paginator = new Zend_Paginator($newsList);
		$paginator->setItemCountPerPage('5');
		// 获得当前显示的页码
		$page = $this->_request->getParam('page');
		$paginator->setCurrentPageNumber($page);
		// 渲染到视图
		$this->view->newsList = $paginator;
    }

}

    