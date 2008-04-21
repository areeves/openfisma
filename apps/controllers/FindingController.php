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
    private $perPage = 30;
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

    /**
       Get finding detail infomation
    */
    public function viewAction(){
        require_once MODELS . DS . 'asset.php';
        $this->_helper->actionStack('header','Panel');
        $req = $this->getRequest();
        $fid = $req->getParam('fid',0);
        assert($fid);
        
        $this->view->assign('fid',$fid);
        if(isAllow('finding','read')){
            $finding = new Finding();
            $finding_detail = $finding->getFindingById($fid);
            $this->view->assign('finding',$finding_detail);
            $this->render();
        }
        else {
            /// @todo Add a new Excption page to indicate Access denial
            $this->render();
        }
    }

    /**
      Edit finding infomation
    */
    public function editAction(){
        $req = $this->getRequest();
        $fid = $req->getParam('fid');
        assert($fid);
        $finding = new Finding();
        $do = $req->getParam('do');
        if($do == 'update'){
           $status = $req->getParam('status');
           $db = Zend_Registry::get('db');
           $result = $db->query("UPDATE FINDINGS SET finding_status = '$status' WHERE finding_id = $fid");     
           if($result){
               $this->view->assign('msg',"Finding updated successfully");
           }
           else {
               $this->view->assign('msg',"Finding update failed");
           }
        }
        $this->view->assign('act','edit');
        $this->_forward('view','Finding');
    }
   
    /**
     spreadsheet Upload
    */
    public function injectionAction(){
        $this->_helper->actionStack('header','Panel');
        if(isAllow('finding','create')){
            $csvFile = isset($_FILES['csv'])?$_FILES['csv']:array();
            if(!empty($csvFile)){
                if($csvFile['size'] < 1 ){
                    $err_msg = 'Error: Empty file.';
                }
                if($csvFile['size'] > 1048576 ){
                    $err_msg = 'Error: File is too big.';
                }
                if(preg_match('/\x00|\xFF/',file_get_contents($csvFile['tmp_name']))){
                    $err_msg = 'Error: Binary file.';
                }
            }
            if(empty($csvFile) || $csvFile['error']){
                $this->render();
                return;
            }
            if(!empty($err_msg)){
                $this->view->assign('error_msg',$err_msg);
                $this->render();
                return;
            }
            if(!empty($csvFile)){
                $fileName = $csvFile['name'];
                $tempFile = $csvFile['tmp_name'];
                $fileSize = $csvFile['size'];

                $faildArray = $succeedArray = array();
                $row = -2;
                $handle = fopen($tempFile,'r');
                while($data = fgetcsv($handle,1000,",",'"')) {
                    if(implode('',$data)!=''){
                        $row++;
                        if($row>0){
                            $sql = $this->csvQueryBuild($data);
                            if(!$sql){
                                $faildArray[] = $data;
                            }
                            else {
                                foreach($sql as $query){
                                    $db = Zend_Registry::get('db');
                                    $db->query($query);
                                }
                                $succeedArray[] = $data;
                            }
                        }
                    }
                }
                fclose($handle);
                $summary_msg = "You have uploaded a CSV file which contains $row line(s) of data.<br />";
                if(count($faildArray) > 0){
                    $temp_file = 'temp/csv_'.date('YmdHis').'_'.rand(10,99).'.csv';
                    $fp = fopen($temp_file,'w');
                    foreach($faildArray as $fail) {
                        fputcsv($fp,$fail);
                    }
                    fclose($fp);
                    $summary_msg .= count($faildArray)." line(s) cannot be parsed successfully. This is likely due to an unexpected datatype or the use of a datafield which is not currently in the database. Please ensure your csv file matches the data rows contained <a href='/$temp_file'>here</a> in the spreadsheet template. Please update your CSV file and try again.<br />";
                }
                if(count($succeedArray)>0){
                    $summary_msg .= count($succeedArray)." line(s) parsed and injected successfully. <br />";
                }
                if(count($succeedArray)==$row){
                    $summary_msg .= " Congratulations! All of the lines contained in the CSV were parsed and injected successfully.";
                }
                $this->view->assign('error_msg',$summary_msg);
            }
            $this->render();
        }
    }

   /** 
    Create finding
   */
    public function createAction(){
        require_once MODELS . DS . 'source.php';
        require_once MODELS . DS . 'network.php';
        require_once MODELS . DS . 'system.php';
        require_once MODELS . DS . 'asset.php';

        $req = $this->getRequest();
        //$do = $req->getParam('do');
        $system_id = $req->getParam('system_id');

        $db = Zend_Registry::get('db');
        $user = new User();
        $src = new Source();
        $net = new Network();
        $sys = new System();
        $asset = new Asset();
        $uid = $this->me->user_id;
        $qry = $db->select();
        $source_list = $db->fetchPairs($qry->from($src->info(Zend_Db_Table::NAME),
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
        $qry->reset();
        $asset_list = $db->fetchPairs($qry->from($asset->info(Zend_Db_Table::NAME),
                                  array('id'=>'asset_id','name'=>'asset_name'))
                                    ->order('name ASC'));
        /*if(!empty($system_id)){
            $db->fetchPairs($qry->join(array('sa' => 'SYSTEM_ASSETS'),'sa.asset_id = asset.asset_id',array(),
                                where("sa.system_id = $system_id")));
        }
        $db->fetchPairs($qry->order('name ASC'));*/
        $discovered_date = strftime("%m/%d/%Y",(mktime(0,0,0,date("m"),date("d"),date("Y"))));
        $this->view->assign('discovered_date',$discovered_date);
        $this->view->assign('asset_list',$asset_list);
        $this->view->assign('system_list',$system_list);
        $this->view->assign('network_list',$network_list);
        $this->view->assign('source_list',$source_list);
        list($asset_id,$sname) = each($asset_list);
        $this->_helper->actionStack('header','Panel');
        $this->render();
    }


    public function csvQueryBuild($row){
        if (!is_array($row) || (count($row)<7)){
            return false;
        }
        if (strlen($row[3])>63 || (!is_numeric($row[4]) && !empty($row[4]))){
            return false;
        }
        if (in_array('', array($row[0],$row[1],$row[2],$row[5],$row[6]))){
            return false;
        }
        $row[2] = date('Y-m-d',strtotime($row[2]));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$row[2])){
            return false;
        }
        $db = Zend_Registry::get('db');
        $result = $db->fetchRow("SELECT system_id FROM `SYSTEMS` WHERE system_nickname = '$row[0]'");
        $row[0] = is_array($result)?array_pop($result):false;
        $result = $db->fetchRow("SELECT network_id FROM `NETWORKS` WHERE network_nickname = '$row[1]'");
        $row[1] = is_array($result)?array_pop($result):false;
        $result = $db->fetchRow("SELECT source_id FROM `FINDING_SOURCES` WHERE source_nickname = '$row[5]'");
        $row[5] = is_array($result)?array_pop($result):false;
        if (!$row[0] || !$row[1] || !$row[5]) {
            return false;
        }
        $sql[] = "INSERT INTO `ASSETS`(asset_name, asset_date_created, asset_source)
                  VALUES(':$row[3]:$row[4]', '$row[2]', 'SCAN')";
        $sql[] = "INSERT INTO `SYSTEM_ASSETS` (system_id, asset_id, system_is_owner)
                  VALUES($row[0], LAST_INSERT_ID(), 1)";
        $sql[] = "INSERT INTO `ASSET_ADDRESSES` (asset_id,network_id,address_date_created,address_ip,address_port)
                  VALUES(LAST_INSERT_ID(), $row[1], '$row[2]', '$row[3]', '$row[4]')";
        $sql[] = "INSERT INTO `FINDINGS` (source_id,asset_id,finding_status,finding_date_created,
                  finding_date_discovered,finding_data)
                  VALUES($row[5], LAST_INSERT_ID(), 'OPEN', '$current_time_string', '$row[2]', '$row[6]')";
        return $sql;
    }

    
}
