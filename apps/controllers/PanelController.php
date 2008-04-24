<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';

class PanelController extends SecurityController
{
    /** Alias of dashboardAction
     */
    public function indexAction()
    {
        $this->_forward('dashboard');
    }

    public function headerAction()
    {   
        $this->_helper->layout->assign('header',
            $this->view->render($this->_helper->viewRenderer->getViewScript()));
        $this->_helper->layout->setLayout('default');

    }

    public function dashboardAction()
    {
        $this->_helper->actionStack('index','Dashboard' );
        $this->_helper->actionStack('header');
    }

    /** finding menu
    */
    public function findingAction()
    {
        $this->_helper->actionStack('searchbox','Finding' );
        $this->_helper->actionStack('summary','Finding' );
        $this->_helper->actionStack('header');
    }
    
    public function userAction()
    {
        $req = $this->getRequest();
        $sub = $req->getParam('sub');
        if('view' == $sub){
            $this->_helper->actionStack('view','User');
        }else if('edit' == $sub){
            $this->_helper->actionStack('view','User');
        }else if('update' == $sub){
            $this->_helper->actionStack('update','User');
        }else {
            $this->_helper->actionStack('list','User');
        }
        $this->_helper->actionStack('searchbox','User');
        $this->_helper->actionStack('header');
    }

    public function searchAction()
    {
        $req = $this->getRequest();
        $action = $req->getParam('obj');
        if('finding' == $action){
            $this->_helper->actionStack($action ,'Search' );
            $this->_helper->actionStack('finding','Summary' );
            $this->_helper->actionStack('header');
        }
    }

}


?>
