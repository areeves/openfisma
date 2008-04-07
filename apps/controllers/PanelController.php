<?php

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

    public function sampleAction()
    {
        $collection = array(
            array(
                "name" =>"CVE",
                "url" => "/zfentry.php/Search/cve/item/cve/status/assigned/view/1"
            ),
            array(
                "name" => "Exploit",
                "url" => "/zfentry.php/Search/exploit/item/exploit/status/assigned/view/1"
            )
        );
        $req = $this->getRequest();
        //$panel = $req->getParam('panel') ? $req->getParam('panel') : 'assignment';
        //$item = $req->getParam('item') ? $req->getParam('item') : 'cve';
        $this->_helper->actionStack('leftnav', null, null, 
                                array('menu' => $collection));
        $this->_helper->actionStack('header');
        $this->_helper->actionStack( 'cve','Search' ,null, array( 'view' => 1));
    }
}


?>
