<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once(CONTROLLERS . DS . 'PoamBaseController.php');
require_once(MODELS . DS . 'finding.php');
require_once MODELS . DS . 'asset.php';
require_once MODELS . DS . 'source.php';
require_once MODELS . DS . 'poam.php';
require_once('Pager.php');
define('TEMPLATE_NAME', "OpenFISMA_Injection_Template.xls"); 

class FindingController extends PoamBaseController
{
    public function searchboxAction(){

        $req = $this->getRequest();
        // parse the params of search
        $this->_paging_base_path .= '/panel/finding/sub/searchbox/s/search';
        $criteria['system_id'] = $req->getParam('system_id');
        $criteria['source_id'] = $req->getParam('source_id');
        $criteria['network_id'] = $req->getParam('network_id');
        $criteria['ip'] = $req->getParam('ip');
        $criteria['port'] = $req->getParam('port');
        $criteria['vuln'] = $req->getParam('vuln');
        $criteria['product'] = $req->getParam('product');
        $criteria['created_date_begin'] = $req->getParam('created_date_begin');
        $criteria['created_date_end']   = $req->getParam('created_date_end');
        $tmp = $req->getParam('created_date_begin');
        if(!empty($tmp)){
            $criteria['created_date_begin'] = new Zend_Date($tmp,Zend_Date::DATES);
        }
        $tmp = $req->getParam('created_date_end');
        if(!empty($tmp)){
            $criteria['created_date_end'] = new Zend_Date($tmp,Zend_Date::DATES);
        }
        $criteria['status'] = $req->getParam('status');

        $this->view->source = $this->_source_list;
        $this->view->network = $this->_network_list;
        $this->view->system = $this->_system_list;
        $this->view->criteria = $criteria;
        $this->render();
        if( 'search' == $req->getParam('s') ) {
            foreach($criteria as $key=>$value){
                if(!empty($value) ){
                    if($value instanceof Zend_Date){
                        $this->_paging_base_path .='/'.$key.'/'.$value->toString('Ymd').'';
                    }else{
                        $this->_paging_base_path .='/'.$key.'/'.$value.'';
                    }
                }
            }
            $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
            $this->_search($criteria) ;
        }
    }

    /**
        Provide searching capability of findings
        Data is limited in legal systems.
     */
    protected function _search($criteria)
    {
        //$req = $this->getRequest();
        //$criteria = $req->getParam('criteria');
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
        $result = $this->_poam->search($this->me->systems, $fields, $criteria, 
                    $this->_paging['currentPage'],$this->_paging['perPage']);
        $total = array_pop($result);

        $this->_paging['totalItems'] = $total ;
        $pager = &Pager::factory($this->_paging);

        $this->view->assign('findings',$result);
        $this->view->assign('links',$pager->getLinks());
        $this->render('search');
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

        $statistic = $this->_system_list;
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
    public function viewAction()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id',0);
        assert($id);

        $this->view->assign('id',$id);

        if(isAllow('finding','read')){
            $sys = new System();
            $poam = new Poam();
            $detail = $poam->find($id)->current();
            $this->view->finding = $poam->getDetail($id);
            $this->view->finding['system_name'] = $this->me->systems[$this->view->finding['system_id']];
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
                $handle = fopen($tempFile,'r');
                $data = fgetcsv($handle,1000,",",'"'); //skip the first line
                $data = fgetcsv($handle,1000,",",'"'); //skip the second line
                $row = 0;
                while($data = fgetcsv($handle,1000,",",'"')) {
                    if(implode('',$data)!=''){
                        $row++;
                        $ret = $this->insertCsvRow($data);
                        if(empty($ret) ){
                            $failedArray[] = $data;
                        }else{
                            $succeedArray[] = $data;
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
            try{
                $data = array();
                $data['source_id'] = $req->getParam('source');
                $data['asset_id'] = $req->getParam('asset_list');
                $data['system_id'] = $req->getParam('system');
                $data['status'] = 'NEW';
                $data['discover_ts'] = $req->getParam('discovereddate');
                $data['finding_data'] = $req->getParam('finding_data');

                $data['create_ts'] = self::$now->toString("Y-m-d H:i:s");
                $data['created_by'] = $this->me->id;

                $this->_poam->insert($data);
                $message="Finding created successfully";
                $model=self::M_NOTICE;
            }catch(Zend_Exception $e){
                $message= "Error in creating";//htmlspecialchars($e->getMessage());
                $model=self::M_WARNING;
            }
            $this->message($message,$model);
        }

        $this->view->assign('system',$this->_system_list);
        $this->view->assign('source',$this->_source_list);
        $this->render();
    }
    
    /**
    delete findings
    **/
    public function deleteAction(){
        $req = $this->getRequest();
        $post = $req->getPost();
        $poam = new poam();
        foreach($post as $key=>$id){
            if(substr($key,0,4) == 'id_'){
                $poam->update(array('status'=>'DELETED'),'id = '.$id);
            }
        }
        $this->_forward('searchbox','finding',null,array('s'=>'search'));

    }

    public function insertCsvRow($row){
        $asset = new asset();
        $poam = new poam();
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
        $query = $db->select()->from('systems','id')->where('nickname = ?',$row[0]);
        $result = $db->fetchRow($query);
        $row[0] = !empty($result)?$result['id']:false;
        $query->reset();
        $query = $db->select()->from('networks','id')->where('nickname = ?',$row[1]);
        $result = $db->fetchRow($query);
        $row[1] = !empty($result)?$result['id']:false;
        $query->reset();
        $query = $db->select()->from('sources','id')->where('nickname = ?',$row[5]);
        $result = $db->fetchRow($query);        
        $row[5] = !empty($result)?$result['id']:false;
        if (!$row[0] || !$row[1] || !$row[5]) {
            return false;
        }
        $asset_name = ':'.$row[3].':'.$row[4];
        $query = $asset->select()->from($asset,'id')
                                 ->where('system_id = ?',$row[0])
                                 ->where('network_id = ?',$row[1])
                                 ->where('address_ip = ?',$row[3])
                                 ->where('address_port = ?',$row[4]);
        $result = $asset->fetchRow($query);
        if(!empty($result)){
            $data = $result->toArray();
            $asset_id = $data['id'];
        }else{
            $asset_data = array('name'=>$asset_name,'create_ts'=>$row[2],'source'=>'SCAN',
                                'system_id'=>$row[0],'network_id'=>$row[1],'address_ip'=>$row[3],
                                'address_port'=>$row[4]);
            $asset_id = $asset->insert($asset_data);
        }
        $poam_data = array('asset_id'=>$asset_id,'source_id'=>$row[5],'system_id'=>$row[0],
                              'status'=>'NEW','create_ts'=>self::$now->toString('Y-m-d h:i:s') ,
                              'discover_ts'=>$row[2],'finding_data'=>$row[6]);
        $ret =  $poam->insert($poam_data);
        return $ret;
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
        $this->view->systems = $src->getList('nickname') ;
        $src = new Network();
        $this->view->networks = $src->getList('nickname');
        $src = new Source();
        $this->view->sources = $src->getList('nickname');
        $this->render();
    }
}
