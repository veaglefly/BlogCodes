 <?php 
 require_once 'BaseController.php';
    require_once APPLICATION_PATH.'/models/About.php';
    class AboutController extends BaseController{
    	public function indexAction(){
    	 
    		$modelPage=new About();
    		$where = array('cid'=>1);
    		$aboutPages = $modelPage->getPages($where);
    		$this->view ->aboutPages = $aboutPages ;
    		
       	}
    }
    