<?php
/**
 * @file AssetController.php
 *
 * @description Asset Controller 
 *
 * @author     Jim <jimc@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'asset.php';
require_once MODELS . DS . 'system.php';
require_once MODELS . DS . 'source.php';
require_once MODELS . DS . 'product.php';

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

        $qry = $asset->select()->from($asset,array('id'=>'id', 'name'=>'name'))
                               ->order('name ASC');
        if(!empty($system_id) && $system_id > 0){
            $qry->where('system_id = ?',$system_id);
        }

        if(!empty($asset_name)){
            $qry->where('name=?',$asset_name);
        }

        if(!empty($ip)){
            $qry->where('address_ip = ?',$ip);
        }

        if(!empty($port)){
            $qry->where('address_port = ?',$port);
        }

        $this->view->assets = $asset->fetchAll($qry)->toArray();
        $this->_helper->layout->setLayout( 'ajax' );
        $this->render('list');
    }

    /**
     Create Asset
   */
    public function createAction(){
        $asset = new Asset();
        $systems= new System();
        $user=new User();
        $product=new Product();
        $systems=$user->getMySystems($this->me->id);
        $sys_id_set=implode(',',$systems);

        $db=Zend_Registry::get('db');
        $qry=$db->select();
        $system_list=$db->fetchPairs($qry->from(array('s'=>'systems'),
                                                array('id'=>'id','name'=>'name'))
                                         ->where("id IN ($sys_id_set)")
                                         ->order('name ASC'));
        $system_list['select']="--select--";
        $qry->reset();
        $network_list=$db->fetchPairs($qry->from(array('n'=>'networks'),
                                                 array('id'=>'n.id','name'=>'n.name'))
                                          ->order('name ASC'));
        $network_list['select']="--select--";
        $qry->reset();

        $req=$this->getRequest();

        $asset_name=$req->getParam('assetname','');
        $system_id=$req->getParam('system_list','');
        $network_id=$req->getParam('network_list','');
        $asset_ip=$req->getParam('ip','');
        $asset_port=$req->getParam('port','');
        $prod_id=$req->getParam('prod_id','');

        $asset_source="MANUAL";
        $create_time=date("Y_m_d H:m:s");

        if(!empty($asset_name)){
            $asset_row =array('prod_id'=>$prod_id,
                              'name'=>$asset_name,
                              'create_ts'=>$create_time,
                              'source'=>$asset_source,
                              'system_id'=>$system_id,
                              'network_id'=>$network_id,
                              'address_ip'=>$asset_ip,
                              'address_port'=>$asset_port);
            $asset_last_insert_id=$asset->insert($asset_row);
            $this->message( "Create Asset successfully", self::M_NOTICE);
        }
        $this->view->system_list=$system_list;
        $this->view->network_list=$network_list;
        $this->_helper->actionStack('header','Panel');
        $this->render();
        $this->_forward('search','product');
    }

   /**
     Get Asset Information
   */
    public function detailAction(){
        $asset = new Asset();
        $req = $this->getRequest();
        $id = $req->getParam('id');
        if(!empty($id)) {
            $qry = $asset->select()->setIntegrityCheck(false)
                                   ->from(array('a'=>'assets'),array('ip'=>'address_ip'))
                                   ->join(array('s'=>'systems'),'a.system_id=s.id',array('sname'=>'s.name'))
                                   ->join(array('p'=>'products'),
                                   'p.id = a.prod_id',array('pname' =>'p.name',
                                                                        'pvendor' =>'p.vendor',
                                                                        'pversion' =>'p.version'));
            $qry->where("a.id = $id");
            $result=$asset->fetchRow($qry);
            if(!$result){
                $result=NULL;
            }else{
                $result = $result->toArray();
            }
            $this->view->asset = $result;
        }
        $this->_helper->layout->setLayout( 'ajax' );
        $this->render('detail');
    }
}
?>
