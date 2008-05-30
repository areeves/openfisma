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

        $qry = $asset->select()->setIntegrityCheck(false)
                               ->from(array('a'=>'ASSETS'),array('id'=>'id',
                                                                 'name'=>'name'))
                               ->order('name ASC');
        if(!empty($system_id) && $system_id > 0){
            //$qry->join(array('sa'=>'SYSTEM_ASSETS'),'sa.asset_id = a.asset_id',array());
            $qry->where("a.system_id = $system_id");
        }

        if(!empty($asset_name)){
            $qry->where("a.name='$asset_name'");
        }

        if(!empty($ip)){
            $qry->where("a.address_ip = ".$ip);
        }

        if(!empty($port)){
            $qry->where("a.address_port = ".$port);
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
        $systems=$user->getMySystems($this->me->user_id);
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
            $asset_rows_affected=$db->insert('assets',$asset_row);

            $asset_last_insert_id=$db->lastInsertId();
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
                                   ->from(array('a'=>'assets'),array());
            $qry->join(array('p'=>'products'),'p.prod_id = a.prod_id',array('pname' =>'p.name',
                                                                        'pvendor' =>'p.vendor',
                                                                        'pversion' =>'p.version'));
            $qry->where("a.id = $id");
            $this->view->assets = $asset->fetchAll($qry)->toArray();
            $qry->reset();
            $qry = $asset->select()->setIntegrityCheck(false)
                                   ->from(array('a'=>'assets'));
            $qry->join(array('s'=>'systems'),'s.id = a.system_id',array('sname'=>'s.name'));
            $qry->where("a.id = $id");
            $this->view->system = $asset->fetchAll($qry)->toArray();
            $qry->reset();
            $qry = $asset->select()->setIntegrityCheck(false)
                                   ->from(array('a'=>'assets'),array('ip'=>'a.address_ip',
                                                                     'port'=>'a.address_port'));
            $qry->where("a.id = $id");
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
