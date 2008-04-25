<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'asset.php';

class AssetController extends SecurityController
{
    /**
      Get Asset List
    */
    public function searchAction(){
        $asset = new Asset();
        $req = $this->getRequest();
        $system_id = $req->getParam('sid');
        $asset_name = $req->getParam('name');
        $ip = $req->getParam( 'ip' );
        $port = $req->getParam( 'port' );


        $qry = $asset->select()->setIntegrityCheck(false)
                               ->from(array('a'=>'ASSETS'),array('id'=>'asset_id',
                                                                 'name'=>'asset_name'))
                               ->order('name ASC');
        if(!empty($system_id) && $system_id > 0){
            $qry->join(array('sa'=>'SYSTEM_ASSETS'),'sa.asset_id = a.asset_id',array());
            $qry->where("sa.system_id = $system_id");
        }

        if(!empty($asset_name)){
            $qry->where("asset_name='$asset_name'");
        }

        if(!empty($ip) || !empty($port) ){
            $qry->join(array('aa'=>'ASSET_ADDRESSES'),
                         'aa.asset_id = a.asset_id',
                         array() );
            if($ip) {
                $qry->where("aa.address_ip='$ip'");
            }
            if($port) {
                $qry->where("aa.address_port='$port'");
            }
        }

        $this->view->assets = $asset->fetchAll($qry)->toArray();
        $this->_helper->layout->setLayout( 'ajax' );
        $this->render('list');
    }

   /**
     Get Asset Information
   */
    public function detailAction(){
        $asset = new Asset();
        $req = $this->getRequest();
        $id = $req->getParam('id');
        if(!empty($id) ) {
            $qry = $asset->select()->setIntegrityCheck(false)
                                   ->from(array('a'=>'ASSETS'),array());
            $qry->join(array('p'=>'PRODUCTS'),'p.prod_id = a.prod_id',array('pname' =>'p.prod_name',
                                                                        'pvendor' =>'p.prod_vendor',
                                                                        'pversion' =>'p.prod_version'));
            $qry->where("a.asset_id = $id");
            $this->view->assets = $asset->fetchAll($qry)->toArray();
            $qry->reset();
            $qry = $asset->select()->setIntegrityCheck(false)
                                   ->from(array('sa'=>'SYSTEM_ASSETS'));
            $qry->join(array('s'=>'SYSTEMS'),'s.system_id = sa.system_id',array('sname'=>'s.system_name'));
            $qry->where("sa.asset_id = $id");
            $this->view->system = $asset->fetchAll($qry)->toArray();
            $qry->reset();
            $qry = $asset->select()->setIntegrityCheck(false)
                                   ->from(array('aa'=>'ASSET_ADDRESSES'),array('ip'=>'aa.address_ip',
                                                                             'port'=>'aa.address_port'));
            $qry->where("aa.asset_id = $id");
            $ipport = $asset->fetchAll($qry)->toArray();
            if(!empty($ipport)){
                foreach($ipport as $result){
                    $ip = $result['ip'].":".$result['port'];
                }
                $this->view->ip = $ip;
            }
        }
        $this->_helper->layout->setLayout( 'ajax' );
        $this->render('detail');

    }
}
?>
