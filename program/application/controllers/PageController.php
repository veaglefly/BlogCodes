 <?php 
 require_once 'BaseController.php';
    require_once APPLICATION_PATH.'/models/Page.php';
    class PageController extends BaseController{
    	
    	 
    	public function detailAction(){
    		$id = $this->getRequest()->getParam('id');
//     		 echo $id;
//     		 exit();
    		$modelPage=new Page();
    		$page = $modelPage->getPage($id);
    		$this->view->page = $page;
    		//获取其他新闻列表
    		$where ="id != ".$id;
    		$pages = $modelPage->getPages($where);
    		$this->view ->pages = $pages -> toArray();
       	}
        
    }