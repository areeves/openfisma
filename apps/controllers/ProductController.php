<?php
/**
 * @file ProductController.php
 *
 * @description Product Controller
 *
 * @author     Jim <jimc@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';

require_once MODELS . DS . 'product.php';

class ProductController extends SecurityController
{
    /**
      Get Product List
    */
    public function searchAction(){
        $product = new Product();
        $req = $this->getRequest();
        $prod_id = $req->getParam('prod_list','');
        $prod_name = $req->getParam('prod_name','');
        $prod_vendor = $req->getParam('prod_vendor','');
        $prod_version = $req->getParam('prod_version','' );
        $tpl_name = $req->getParam('view','search');
        $this->_helper->layout->setLayout( 'ajax' );
        $qry = $product->select()->setIntegrityCheck(false)
                                 ->from(array(),array());

        if(!empty($prod_name)){
            $qry->where("name = '$prod_name'");
            $this->view->prod_name=$prod_name;
        }

        if(!empty($prod_vendor)){
            $qry->where("vendor='$prod_vendor'");
            $this->view->prod_vendor=$prod_vendor;
        }

        if(!empty($prod_version)){
            $qry->where("version='$prod_version'");
            $this->view->prod_version=$prod_version;
        }
        $qry->limit(100,0);

        $this->view->prod_list = $product->fetchAll($qry)->toArray();

        $this->render($tpl_name);
    }
}
?>
