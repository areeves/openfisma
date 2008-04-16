<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'finding.php';

class FindingController extends SecurityController
{
    private $perPage = 20;
    private $currentPage = 1;
    public function indexAction()
    {
        $this->render();
    }

    public function searchboxAction(){
        require_once MODELS . DS . 'source.php';
        require_once MODELS . DS . 'network.php';
        require_once MODELS . DS . 'system.php';

        $db = Zend_Registry::get('db');
        $user = new User();
        $src = new Source();
        $net = new Network();
        $sys = new System();

        $req = $this->getRequest();
        // parse the params of search
        $criteria['system'] = $req->getParam('system','any');
        $criteria['source'] = $req->getParam('source','any');
        $criteria['network'] = $req->getParam('network','any');
        $criteria['ip'] = $req->getParam('ip','');
        $criteria['port'] = $req->getParam('port','');
        $criteria['vuln'] = $req->getParam('vuln','');
        $criteria['product'] = $req->getParam('product','');
        $criteria['from'] = $req->getParam('from','');
        $criteria['to'] = $req->getParam('to','');
        $criteria['status'] = $req->getParam('status','any');

        // fetch data for lists
        $uid = $this->me->user_id;
        $qry = $db->select();
        $source_list  = $db->fetchPairs($qry->from($src->info(Zend_Db_Table::NAME), 
                                    array('id'=>'source_id','name'=>'source_name'))
                                    ->order(array('id ASC')) );
        $qry->reset();
        $network_list = $db->fetchPairs($qry->from($net->info(Zend_Db_Table::NAME), 
                                    array('id'=>'network_id','name'=>'network_name'))
                                    ->order(array('id ASC')) );
        $qry->reset();
        $ids = implode(',', $user->getMySystems($uid));
        $system_list = $db->fetchPairs($qry->from($sys->info(Zend_Db_Table::NAME),
                                    array('id'=>'system_id','name'=>'system_name'))
                                    ->where("system_id IN ( $ids )")
                                    ->order('id ASC'));
        if( 'search' == $req->getParam('s') ) {
            $this->_helper->actionStack('search','Finding',null,
                    array('criteria'=>$criteria,
                          'system'  =>$ids,
                          'source'  =>$source_list,
                          'network' =>$network_list));
        }
        $system_list['any'] = '--Any--';
        $source_list['any'] = '--Any--';
        $network_list['any'] = '--Any--';
        $status_list = array( 'any'=>'--Any--' ,
                              "OPEN"=>'Open',
                              "REMEDIATION"=>'Remediation',
                              "CLOSED"=>'Closed');
        $this->view->source = $source_list;
        $this->view->network = $network_list;
        $this->view->system = $system_list;
        $this->view->status = $status_list;
        $this->view->criteria = $criteria;
        $this->render();
    }

    /** 
        Provide searching capability of findings
        Data is limited in legal systems.
     */
    public function searchAction() {
        $finding = new Finding();
        $qry = $finding->select()->setIntegrityCheck(false)
                       ->from(array('finding'=>'FINDINGS'), array('id' => 'finding_id',
                                              'status'=>'finding_status',
                                              'source_id' => 'source_id',
                                              'discovered' => 'finding_date_discovered'));

        $req = $this->getRequest();
        $criteria = $req->getParam('criteria');
        $systems = $req->getParam('system');

        //$sources  = $req->getParam('source');
        //$networks = $req->getParam('network');
        assert(is_array($criteria)); //be more assert
        extract($criteria);
        if(!isset($from)){
            $startdate = strftime("%Y-%m-%d",(mktime(0,0,0,date("m"),date("d") - 7,date("Y"))));
        }
        else {
            $startdate = date("Y-m-d",strtotime($from));
        }
        if(!isset($to)){
            $enddate = strftime("%Y-%m-%d",(mktime(0,0,0,date("m"),date("d"),date("Y"))));
        }
        else {
            $enddate =date("%Y-%m-%d",strtotime($to));
        }

        $qry->join(array('as' => 'ASSETS'), 'as.asset_id = finding.asset_id',array())
            ->join(array('addr' => 'ASSET_ADDRESSES'),'as.asset_id = addr.asset_id',
                    array('ip'=>'addr.address_ip', 'port'=>'addr.address_port'));

        if( !empty($source) && $source != 'any' ) {
            $qry->where("source_id = {$source}");
        } 
        if( !empty($status) && $status != 'any' ) {
            $qry->where("finding_status = '$status'");
        }
        if( !empty($startdate) && strlen($startdate) == 10){
            $qry->where("finding_date_discovered >= '$startdate'");
        }
        if( !empty($enddate) && strlen($enddate) == 10){
            $qry->where("finding_date_discovered <= '$enddate'");
        }
        $phrase = " IN ( $systems )";
        if( !empty($system) && $system != 'any' ) {
            $phrase = " = $system";
        }
        $qry->join(array('sys_as' => 'SYSTEM_ASSETS'),
                    "as.asset_id = sys_as.asset_id AND sys_as.system_id $phrase",
                    array('sys_id'=>'sys_as.system_id') );
        if( !empty($network) && $network != 'any') {
            $qry->where("addr.network_id = $network");
        }
        if( !empty($ip) ) {
            $qry->where("addr.address_id = $ip");
        }
        if( !empty($port) ) {
            $qry->where("addr.address_port = $port");
        }
        if( !empty($from) ) { 
        }
        if( !empty($to) ) {
        }
        $qry->limitPage($this->currentPage,$this->perPage);
        $data = $finding->fetchAll($qry);
        $findings = $data->toArray();
        $this->view->assign('findings',$findings);

        $this->render();
    }

    /** 
        Create finding summary
        Data is limited in legal systems.
     */
    public function summaryAction() {
        //return;
        $db = Zend_Registry::get('db');
        $finding = new finding($db);
        $auth = Zend_Auth::getInstance();
        $uid = $auth->getIdentity()->user_id;
        $summary_data = $finding->getSummaryList($uid);
        $this->view->assign('summary_data',$summary_data);
        $this->render();     
    }



}
?>
