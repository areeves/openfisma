<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once(CONTROLLERS . DS . 'SecurityController.php');
require_once(MODELS . DS . 'finding.php');
require_once(MODELS. DS . 'system.php');
require_once(MODELS. DS . 'source.php');
require_once(MODELS. DS . 'network.php');
require_once MODELS . DS . 'poam.php';
require_once MODELS . DS . 'asset.php';
require_once('Pager.php');
define('TEMPLATE_NAME', "OpenFISMA_Injection_Template.xls"); 

class FindingController extends SecurityController
{
    private $_systems = array();
    private $_poam = null;
    private $_paging = array('mode'    =>'Sliding',
                             'append'  =>false,
                             'urlVar'  =>'p',
                             'path'    =>'',
                             'currentPage'=>1,
                             'perPage'=>20);                             

    public function init()
    {
        $this->_poam = new Poam();
    }
   
    public function preDispatch()
    {
        parent::preDispatch();
        require_once MODELS . DS . 'system.php';
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl().'/panel/finding/sub/searchbox/s/search';
        $this->_paging['currentPage'] = $req->getParam('p',1);
        $sys = new System();
        $user = new User();
        $uid = $this->me->id;
        $this->_systems = $sys->getList('name',$user->getMySystems($uid));
    }


    public function searchboxAction(){
        require_once MODELS . DS . 'source.php';
        require_once MODELS . DS . 'network.php';

        $db = Zend_Registry::get('db');
        $user = new User();
        $src = new Source();
        $net = new Network();
        $sys = new System();
        $poam = new Poam();

        $req = $this->getRequest();
        // parse the params of search
        $criteria['system'] = $req->getParam('system');
        $criteria['source'] = $req->getParam('source');
        $criteria['network'] = $req->getParam('network');
        $criteria['ip'] = $req->getParam('ip');
        $criteria['port'] = $req->getParam('port');
        $criteria['vuln'] = $req->getParam('vuln');
        $criteria['product'] = $req->getParam('product');
        $criteria['discovered_date_begin'] = $req->getParam('from');
        $criteria['discovered_date_end'] = $req->getParam('to');
        $criteria['status'] = $req->getParam('status');

        // fetch data for lists
        $source_list  = $src->getList('name');
        $network_list = $net->getList('name');
        $system_list = $this->_systems;

        if( 'search' == $req->getParam('s') ) {
            $this->_helper->actionStack('search','Finding',null,
                    array('criteria'=>$criteria) );
        }
        $system_list[0] = '--Any--';
        $source_list[0] = '--Any--';
        $network_list[0] = '--Any--';
        $status_list = array(   0 =>'--Any--' ,
                             "NEW"=>'NEW',
                     "REMEDIATION"=>"REMEDIATION",
                            'OPEN'=>'-- OPEN',
                              'EN'=>'-- EN',
                              'EP'=>'-- EP',
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
    public function searchAction()
    {
        $req = $this->getRequest();
        $criteria = $req->getParam('criteria');
        foreach($criteria as $key=>$value){
            if(!empty($value) && $value!='any'){
                $this->_paging_base_path .='/'.$key.'/'.$value.'';
            }
        }
        $fields = array('id',
                       'legacy_finding_id',
                       'ip',
                       'port',
                       'status',
                       'source_id',
                       'system_id',
                       'discover_ts',
                       'count'=>'count(*)');
        if( $criteria['status'] == 'REMEDIATION' ) {
            $criteria['status'] = array('OPEN','EN','EP','ES');
        }
        $result = $this->_poam->search(array_keys($this->_systems), $fields, $criteria, 
                    $this->_paging['currentPage'],$this->_paging['perPage']);
        $total = array_pop($result);

        $this->_paging['totalItems'] = $total ;
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $pager = &Pager::factory($this->_paging);
        $this->view->assign('findings',$result);
        $this->view->assign('links',$pager->getLinks());
        $this->render();
    }

    /**
        Create finding summary
        Data is limited in legal systems.
     */
    public function summaryAction() {
        require_once 'Zend/Date.php';
        $finding = new Finding();
        $from = new Zend_Date();
        $to = clone $from;
        //count time range
        $to->add(1,Zend_Date::DAY);
        $range['today']  = array('from'=>clone $from, 'to'=>clone $to);
        $from->sub(30,Zend_Date::DAY);
        $to->sub(1,Zend_Date::DAY);
        $range['last30'] = array('from'=>clone $from, 'to'=>clone $to);
        $from->sub(30,Zend_Date::DAY);
        $to->sub(30,Zend_Date::DAY);
        $range['last60'] = array('from'=>clone $from, 'to'=>clone $to);
        $to->sub(30,Zend_Date::DAY);
        $range['after60'] = array( 'to'=>$to);

        $statistic = $this->_systems;
        foreach($statistic as $k => $row){
            $data = $finding->getStatusCount(array($k) );
            $statistic[$k] = array(
                        'NAME'=>$row,
                        'NEW'=>array('total'=>$data['NEW'],
                                      'today'=>0,
                                      'last30day'=>0,
                                      'last2nd30day'=>0,
                                      'before60day'=>0),
                        'REMEDIATION'=>$data['OPEN']+$data['ES']+$data['EN']+$data['EP'],
                        'CLOSED'=>$data['CLOSED']);

            $data = $finding->getStatusCount(array($k),$range['today'],'NEW');
            $statistic[$k]['NEW']['today'] = $data['NEW'];
            $data = $finding->getStatusCount(array($k),$range['last30'],'NEW');
            $statistic[$k]['NEW']['last30day'] = $data['NEW'];
            $data = $finding->getStatusCount(array($k),$range['last60'],'NEW');
            $statistic[$k]['NEW']['last2nd30day'] = $data['NEW'];
            $data = $finding->getStatusCount(array($k),$range['after60'],'NEW');
            $statistic[$k]['NEW']['before60day'] = $data['NEW'];
        }

        $this->view->assign('range',$range);
        $this->view->assign('statistic',$statistic);
        $this->render();
    }

    /**
       Get finding detail infomation
    */
    public function viewAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id',0);
        assert($id);

        $this->view->assign('id',$id);

        if(isAllow('finding','read')){
            $sys = new System();
            $poam = new Poam();
            $detail = $poam->find($id)->current();
            $this->view->finding = $poam->getDetail($id);
            $this->view->finding['system_name'] = 
                $this->me->systems[$this->view->finding['system_id']];
            assert($this->view->finding['system_name']);
            $this->render();
        } else {
            /// @todo Add a new Excption page to indicate Access denial
            $this->render();
        }
    }

    /**
      Edit finding infomation
    */
    public function editAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        assert($id);
        $finding = new Finding();
        $do = $req->getParam('do');
        if($do == 'update'){
           $status = $req->getParam('status');
           $db = Zend_Registry::get('db');
           $result = $db->query("UPDATE FINDINGS SET finding_status = '$status' WHERE finding_id = $id");
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

                $failedArray = $succeedArray = array();
                $row = -2;
                $handle = fopen($tempFile,'r');
                while($data = fgetcsv($handle,1000,",",'"')) {
                    if(implode('',$data)!=''){
                        $row++;
                        if($row>0){
                            $sql = $this->csvQueryBuild($data);
                            if(!$sql){
                                $failedArray[] = $data;
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
                if(count($failedArray) > 0){
                    $temp_file = 'temp/csv_'.date('YmdHis').'_'.rand(10,99).'.csv';
                    $fp = fopen($temp_file,'w');
                    foreach($failedArray as $fail) {
                        fputcsv($fp,$fail);
                    }
                    fclose($fp);
                    $summary_msg .= count($failedArray)." line(s) cannot be parsed successfully. This is likely due to an unexpected datatype or the use of a datafield which is not currently in the database. Please ensure your csv file matches the data rows contained <a href='/$temp_file'>here</a> in the spreadsheet template. Please update your CSV file and try again.<br />";
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
    public function createAction()
    {
        $req = $this->getRequest();
        $do = $req->getParam('is','view');
        if("new" == $do){
            $source = $req->getParam('source');
            $asset_id = $req->getParam('asset_list');
            $status = 'OPEN';
            $discovereddate = $req->getParam('discovereddate');
            $finding_data = $req->getParam('finding_data');

            $now = date("Y-m-d H:m:s");
            $m = substr($discovereddate, 0, 2);
            $d = substr($discovereddate, 3, 2);
            $y = substr($discovereddate, 6, 4);
            $disdate = strftime("%Y-%m-%d", (mktime(0, 0, 0, $m, $d, $y)));

            $sql = "INSERT INTO `FINDINGS`
                  (source_id, asset_id, finding_status, finding_date_created,finding_date_discovered,finding_data)
                  VALUES ('$source', '$asset_id', '$status', '$now', '$disdate', '$finding_data')";
            $res = $db->query($sql);
            if($res){
                $message="Finding created successfully";
                $model=self::M_NOTICE;
            }
            else {
                $message="Finding creation failed";
                $model=self::M_WARNING;
            }
            $this->message($message,$model);
        }

        $user = new User();
        $src = new Source();
        $net = new Network();
        $sys = new System();
        $asset = new Asset();
        $source_list = $src->getList('name');
        $this->view->assign('system',$this->_systems);
        $this->view->assign('source',$source_list);
        $this->render();
    }
    
    /**
       convert finding to poam
    **/
    public function convertAction()
    {
        $req = $this->getRequest();
        $finding = new finding();
        $id  = $req->getParam('id');
        $data = array('finding_status'=>'REMEDIATION');
        $finding->update($data,'finding_id = '.$id);
        $rows = $finding->getFindingById($id);
        $system_id = $rows['system_id'];
        $data = array('finding_id'=>$id,
                      'poam_created_by'=>$this->me->user_id,
                      'poam_modified_by'=>$this->me->user_id,
                      'poam_date_created'=>date('Y-m-d H:i:s'),
                      'poam_date_modified'=>date('Y-m-d H:i:s'),
                      'poam_action_date_est'=>'0000-00-00',
                      'poam_action_owner'=>$system_id);
        $poam = new poam();
        $insert_id = $poam->insert($data);
        $data = array('finding_id'=>$id,
                      'user_id'   =>$this->me->user_id,
                      'date'      =>time(),
                      'event'     =>'CREATE:NEW REMEDIATION CREATE',
                      'description'=>'A new remediation was created from finding '.$id);
        $poam->getAdapter()->insert('AUDIT_LOG',$data);
        $this->_forward('remediation','panel',null,array('sub'=>'view','id'=>$insert_id));
    }

    /**
    delete findings
    **/
    public function deleteAction(){
        $req = $this->getRequest();
        $post = $req->getPost();
        $finding = new finding();
        $poam = new poam();
        foreach($post as $key=>$id){
            if(substr($key,0,4) == 'id_'){
                $finding->update(array('finding_status'=>'deleted'),'finding_id = '.$id);
                $poam->delete('finding_id = '.$id);
            }
        }
        $this->_forward('searchbox','finding',null,array('s'=>'search'));

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

    /** 
     * Downloading a excel file which is used as a template for uploading findings.
     * systems, networks and sources are extracted from the database dynamically.
    */
    public function templateAction() 
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addContext('xls', array(
                    'suffix'=>'xls',
                    'headers'=>array('Content-type'=>'application/vnd.ms-excel',
                                     'Content-Disposition'=>'filename='.TEMPLATE_NAME)
                ));
        $contextSwitch->addActionContext('template', 'xls')->initContext('xls');

        $resp = $this->getResponse();

        $src = new System();
        $this->view->systems = $src->getList('system_nickname') ;
        $src = new Network();
        $this->view->networks = $src->getList('network_nickname');
        $src = new Source();
        $this->view->sources = $src->getList('source_nickname');
        $this->render();
    }
}
