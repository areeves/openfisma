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
require_once  'Pager.php';

class FindingController extends SecurityController
{
    private $systems = array();
    private $_paging = array('mode'    =>'Sliding',
                             'append'  =>false,
                             'urlVar'  =>'p',
                             'path'    =>'',
                             'currentPage'=>1,
                             'perPage'=>20);                             

    public function indexAction()
    {
        $this->render();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        require_once MODELS . DS . 'system.php';
        $sys = new System();
        $user = new User();
        $uid = $this->me->user_id;
        $qry = $sys->select();
        $ids = implode(',', $user->getMySystems($uid));
        $this->systems = $sys->getAdapter()
                             ->fetchPairs($qry->from($sys->info(Zend_Db_Table::NAME),
                                    array('id'=>'system_id','name'=>'system_name'))
                                    ->where("system_id IN ( $ids )")
                                    ->order('id ASC'));
        $req = $this->getRequest();
    }

    public function searchboxAction(){
        require_once MODELS . DS . 'source.php';
        require_once MODELS . DS . 'network.php';

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
                                    ->order(array('id ASC')) );
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
    public function searchAction()
    {
        $finding = new Finding();
        $qry = $finding->select()->setIntegrityCheck(false)
                       ->from(array('finding'=>'FINDINGS'), array('id' => 'finding_id',
                                              'status'=>'finding_status',
                                              'source_id' => 'source_id',
                                              'discovered' => 'finding_date_discovered'));

        $req = $this->getRequest();
        
        $criteria = $req->getParam('criteria');
        foreach($criteria as $key=>$value){
            if(!empty($value) && $value!='any'){
                $this->_paging_base_path .='/'.$key.'/'.$value.'';
            }
        }
        $this->_paging_base_path = $req->getBaseUrl().'/panel/finding/sub/searchbox/s/search';
        $this->_paging['currentPage'] = $req->getParam('p',1);
        $this->_paging_base_path = $req->getParam('path');
        $systems = $req->getParam('system');

        assert(is_array($criteria)); //be more assert
        extract($criteria);
        if(!empty($from)){
            $startdate = date("Y-m-d",strtotime($from));
            $qry->where("finding_date_discovered >= '$startdate'");
        }
        if(!empty($to)){
            $enddate =date("Y-m-d",strtotime($to));
            $qry->where("finding_date_discovered < '$enddate'");
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
            $qry->where("addr.address_ip = '$ip'");
        }
        if( !empty($port) ) {
            $qry->where("addr.address_port = '$port'");
        }
        $this->_paging['totalItems'] = $total = count($finding->fetchAll($qry));
        $qry->limitPage($this->_paging['currentPage'],$this->_paging['perPage']);
        $data = $finding->fetchAll($qry);
        $findings = $data->toArray();
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $pager = &Pager::factory($this->_paging);
        $this->view->assign('findings',$findings);
        $this->view->assign('total_pages',$total);
        $this->view->assign('links',$pager->getLinks());
        $this->render();
    }

    /**
        Create finding summary
        Data is limited in legal systems.
     */
    public function summaryAction() {
        require_once 'Zend/Date.php';

        $statistic = $this->systems;
        foreach($statistic as $k => $row){
            $statistic[$k] = array(
                        'NAME'=>$row,
                        'OPEN'=>array('total'=>0,
                                      'today'=>0,
                                      'last30day'=>0,
                                      'last2nd30day'=>0,
                                      'before60day'=>0),
                        'REMEDIATION'=>array('total'=>0),
                        'CLOSED'=>array('total'=>0));
        }
        $finding = new finding();
        $user = new User();
        $uid = $this->me->user_id;
        $systems = $user->getMySystems($uid);
        $from = new Zend_Date();
        $to = clone $from;

        $data = $finding->getCount($systems,array(), array('OPEN','REMEDIATION','CLOSED'));
        foreach($data as $row){
            $statistic[$row['sysid']][$row['status']]['total'] += $row['count'];
        }

        //count time range
        $to->add(1,Zend_Date::DAY);
        $range['today']  = array('from'=>$from->toString("yyyyMMdd"),
                                          'to'=>$to->toString("yyyyMMdd"));
        $from->sub(30,Zend_Date::DAY);
        $to->sub(1,Zend_Date::DAY);
        $range['last30'] = array('from'=>$from->toString("yyyyMMdd"),
                                          'to'=>$to->toString("yyyyMMdd"));
        $from->sub(30,Zend_Date::DAY);
        $to->sub(30,Zend_Date::DAY);
        $range['last60'] = array('from'=>$from->toString("yyyyMMdd"),
                                 'to'=>$to->toString("yyyyMMdd"));
        $to->sub(30,Zend_Date::DAY);
        $range['after60'] = array('from'=>null, 'to'=>$to->toString("yyyyMMdd"));

        $data = $finding->getCount($systems, $range['today'], 'OPEN');
        foreach($data as $row){
            $statistic[$row['sysid']][$row['status']]['today'] += $row['count'];
        }

        //count 30 days before
        $data = $finding->getCount($systems,$range['last30'], 'OPEN');
        foreach($data as $row){
            $statistic[$row['sysid']][$row['status']]['last30day'] += $row['count'];
        }
        //count 2nd 30 days before
        $data = $finding->getCount($systems,$range['last60'], 'OPEN');
        foreach($data as $row){
            $statistic[$row['sysid']][$row['status']]['last2nd30day'] += $row['count'];
        }
        //count later
        $data = $finding->getCount($systems, $range['after60'], 'OPEN');
        foreach($data as $row){
            $statistic[$row['sysid']][$row['status']]['before60day'] += $row['count'];
        }

        $this->view->assign('range',$range);
        $this->view->assign('statistic',$statistic);
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

        $db = Zend_Registry::get('db');
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
        $system_id = $req->getParam('system_id');

        $user = new User();
        $src = new Source();
        $net = new Network();
        $sys = new System();
        $asset = new Asset();
        $uid = $this->me->user_id;
        $qry = $db->select();
        $source_list = $db->fetchPairs($qry->from($src->info(Zend_Db_Table::NAME),
                                       array('id'=>'source_id','name'=>'source_name'))
                                      ->order('id ASC'));
        $qry->reset();
        $network_list = $db->fetchPairs($qry->from($net->info(Zend_Db_Table::NAME),
                                    array('id'=>'network_id','name'=>'network_name'))
                                    ->order('id ASC'));
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
