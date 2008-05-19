<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'user.php';
require_once MODELS . DS . 'poam.php';
require_once 'Pager.php';

class RemediationController extends SecurityController
{
    private $_paging = array('mode'        =>'Sliding',
                             'append'      =>false,
                             'urlVar'      =>'p',
                             'path'        =>'',
                             'currentPage' =>1,
                             'perPage'     =>20);
    public function preDispatch()
    {
        parent::preDispatch();
        $req = $this->getRequest();
        $uid = $this->me->user_id;
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl() .'/panel/remediation/sub/searchbox/s/search';
        $this->_paging['currentPage'] = $req->getParam('p',1);
    }

    public function summaryAction(){
        require_once MODELS . DS . 'system.php';
        $req = $this->getRequest();
        $user = new user();
        $uid = $this->me->user_id;
        $system_list = $user->getMySystems($uid);
        $system_ids = implode(',',$system_list);
        $poam = new poam();
        $system = new System();
        $today = date('Ymd',time());
        $total = array('NEW'=>0,'OPEN'=>0,'EN'=>0,'EO'=>0,'EP_SNP'=>0,'EP_SSO'=>0,'ES'=>0,'CLOSED'=>0,'TOTAL'=>0);
        foreach($system_list as $id) {
            $remediations = $poam->search(array($id));
            if(!empty($remediations)){
                $sum = array('NEW'=>0,'OPEN'=>0,'EN'=>0,'EO'=>0,'EP_SNP'=>0,'EP_SSO'=>0,'ES'=>0,'CLOSED'=>0,'TOTAL'=>0);
                foreach($remediations as $row){
                    switch($row['status']) {
                    case 'OPEN':
                        if( $row['type'] == 'NONE' ) {
                            $sum['NEW'] ++;//count the NEW items
                            $total['NEW'] ++;
                        }else{ 
                            $sum['OPEN'] ++;//count the OPEN items
                            $total['OPEN'] ++;
                        }
                        break;
                    case 'EN':
                        $est = implode(split('-',$row['action_date_est']));
                        if($est < $today){
                            $sum['EO'] ++;                    //update the display to show it as EN in the list
                            $total['EO']++;
                        } else {//still on time,just count it
                            $sum['EN'] ++;
                            $total['EN'] ++;
                        }
                        break;
                    case 'EP':
                        //if the SSO has approved it,then tag it as S&P
                        $db = Zend_Registry::get('db');
                        $query = $db->select()
                              ->from(array('pe'=>'POAM_EVIDENCE'),array('evaluation'=>'pe.ev_sso_evaluation'))
                              ->where("poam_id = ".$row['id']."")
                              ->order("ev_id DESC")
                              ->limit(1);
                        $approval = $db->fetchRow($query);
                        if(isset($approval['evaluation']) && $approval['evaluation'] == 'APPROVED'){
                            $sum['EP_SNP']++;
                            $total['EP_SNP'] ++;
                        } else {//else tag it SSO
                            $sum['EP_SSO']++;
                            $total['EP_SSO']++;
                        }
                        break;
                    case 'ES':
                        $sum['ES']++;
                        $total['ES']++;
                        break;
                    case 'CLOSED':
                        $sum['CLOSED']++;
                        $total['CLOSED']++;
                        break;
                    }
                    $sum['TOTAL']++;
                    $total['TOTAL']++;
                }
                $summary[$id] = $sum;
            }
        }
        $systems = $system->find($system_list);
        foreach( $systems as $s ) {
            $sys_list[$s->system_id] = $s->toArray();
        }
        $this->view->assign('total',$total);
        $this->view->assign('systems',$sys_list);
        $this->view->assign('summary',$summary );
        $this->render('summary');
    }

    public function searchAction(){
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl().'/panel/remediation/sub/searchbox/s/search';
        $this->_paging['currentPage'] = $req->getParam('p',1);
       
        $user = new user();
        $uid = $this->me->user_id;
        $system_list = $user->getMySystems($uid);
        $criteria = $req->getParam('criteria');
        $this->_paging_base_path  = $req->getParam('path');
        
        $poam = new poam();
        $totals = $poam->search($system_list,array('count'=>array()),$criteria);
        $list = $poam->search($system_list,'*',$criteria,$this->_paging['currentPage'],$this->_paging['perPage']);
        $this->_paging['totalItems'] = $totals[0]['count'];
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $pager = &Pager::factory($this->_paging);
        $this->view->assign('summary_list',$list);
        $this->view->assign('total_pages',3);
        $this->view->assign('links',$pager->getLinks());
        $this->render('search');
    }

    public function searchboxAction()
    {
        require_once MODELS . DS . 'system.php';
        require_once MODELS . DS . 'source.php';
        require_once MODELS . DS . 'network.php';

        $db = Zend_Registry::get('db');
        $user = new User();
        $src = new Source();
        $net = new Network();
        $sys = new System();
        
        $req = $this->getRequest();
        $uid = $this->me->user_id;
        // parse the params of search
        $criteria['system'] = $req->getParam('system','any');
        $criteria['source'] = $req->getParam('source','any');
        $criteria['type'] = $req->getParam('type','any');
        $criteria['status'] = $req->getParam('status','any');
        $criteria['ids'] = $req->getParam('ids','');
        $criteria['asset_owner'] = $req->getParam('asset_owner','any');
        $criteria['action_owner'] = $req->getParam('action_owner','any');
        $criteria['startdate'] = $req->getParam('startdate','');
        $criteria['enddate'] = $req->getParam('enddate','');
        $criteria['startcreatedate'] = $req->getParam('startcreatedate','');
        $criteria['endcreatedate'] = $req->getParam('endcreatedate');

        $internal_crit = $criteria;

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
        $system_list['any'] = '--Any--';
        $source_list['any'] = '--Any--';
        $network_list['any'] = '--Any--';
        
        $filter_type = array('any'  =>'--- Any Type ---',
                        'NONE' =>'(NONE) Unclassified',
                        'CAP'  =>'(CAP) Corrective Action Plan',
                        'AR'   =>'(AR) Accepted Risk',
                        'FP'   =>'(FP) False Positive');

        $filter_status = array('any'   =>'--- Any Status ---',
                        'NEW'       =>'(NEW) Awaiting Mitigation Type and Approval',
                        'OPEN'      =>'(OPEN) Awaiting Mitigation Approval',
                        'EN'        =>'(EN) Evidence Needed',
                        'EO'        =>'(EO) Evidence Overdue',
                        'EP'        =>'(EP) Evidence Provided',
                        'EP-SSO'    =>'(EP-SSO) Evidence Provided to SSO',
                        'EP-SNP'    =>'(EP-S&P) Evidence Provided to S&P',
                        'ES'        =>'(ES) Evidence Submitted to IV&V',
                        'CLOSED'    =>'(CLOSED) Officially Closed',
                        'NOT-CLOSED'=>'(NOT-CLOSED) Not Closed',
                        'NOUP-30'   =>'(NOUP-30) 30+ Days Since Last Update',
                        'NOUP-60'   =>'(NOUP-60) 60+ Days Since Last Update',
                        'NOUP-90'   =>'(NOUP-90) 90+ Days Since Last Update');
        if('search' == $req->getParam('s')){
            foreach($criteria as $key=>$value){
                if(!empty($value) && $value!= 'any'){
                    $this->_paging_base_path .= '/'.$key.'/'.$value.'';
                }
            }    
            if(isset($criteria['status']) && $criteria['status'] != 'any'){
                $current_date = date('Y-m-d',time());
                switch($criteria['status']){
                    case 'NEW':
                        $internal_crit['status'] = 'OPEN';
                        $internal_crit['type']   = 'NONE';
                        break;
                    case 'OPEN':
                        $internal_crit['status'] = 'OPEN';
                        $internal_crit['type']   = array("'NONE'","'CAP'","'FP'","'AR'");
                        break;
                    case 'EN':
                        $internal_crit['status'] = 'EN';
                        $internal_crit['startdate'] = date('Y-m-d',time());
                        break;
                    case 'EO':
                        $internal_crit['status'] = 'EN';                        
                        $internal_crit['enddate'] = date('Y-m-d',time());
                        break;
                    case 'EP-SSO':
                        $internal_crit['status'] = 'EP';
                        $internal_crit['ep']     = array('sso'=>'APPROVED',
                                                    'fsa'=>'NONE',
                                                    'ivv'=>'NONE');
                        break;
                    case 'EP-SNP':
                        $internal_crit['status'] = 'EP';
                        $internal_crit['ep']     = array('sso'=>'APPROVED',
                                                    'fsa'=>'NONE',
                                                    'ivv'=>'NONE');
                        break;
                    case 'ES':
                        $internal_crit['status'] = 'ES';
                        break;
                    case 'CLOSED':
                        $internal_crit['status'] = 'CLOSED';
                        break;
                    case 'NOT-CLOSED':
                        $internal_crit['status'] = array("'OPEN'","'EN'","'EP'","'ES'");
                        break;
                    case 'NOUP-30':
                        $internal_crit['status'] = array("'OPEN'","'EN'","'EP'","'ES'");
                        $internal_crit['poam_date_modified'] = 'SUBDATE("'.$current_date.'",30)';
                        break;
                    case 'NOUP-60':
                        $internal_crit['status'] = array("'OPEN'","'EN'","'EP'","'ES'");
                        $internal_crit['poam_date_modified'] = 'SUBDATE("'.$current_date.'",60)';
                        break;
                    case 'NOUP-90':
                        $internal_crit['status'] = array("'OPEN'","'EN'","'EP'","'ES'");
                        $internal_crit['poam_date_modified'] = 'SUBDATE("'.$current_date.'",90)';
                        break;
                }
            }

            $this->_helper->actionStack('search','Remediation',null,
                                        array('s' =>'search','path'=>$this->_paging_base_path,
                                              'criteria'=>$internal_crit));
        }
        $this->view->assign('criteria',$criteria);
        $this->view->assign('system_list',$system_list);
        $this->view->assign('source_list',$source_list);
        $this->view->assign('filter_type',$filter_type);
        $this->view->assign('filter_status',$filter_status);
        $this->render();
    }

    /**
    Get remediation detail info
    */
    public function viewAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        assert($id);
        $today = date("Ymd",time());
        $poam = new poam();
        $db = $poam->getAdapter();
        if(isset($_POST['action']) && 'save' == $_POST['action']){
            $id = $req->getParam('id');
            $now = date('Y-m-d,h:i:s',time());
            foreach($_POST as $k=>$v){
                if('poam_' == substr($k,0,5)){
                    $fields[$k] = $k;
                }
            }
            $fields['finding_id'] = 'finding_id';
            $query = $db->select()->from(array('p'=>'POAMS'),$fields)
                                  ->joinleft(array('pe'=>'POAM_EVIDENCE'),'p.poam_id = pe.poam_id',array())
                                  ->where("p.poam_id = $id");
            $poams = $db->fetchRow($query);
            foreach($_POST as $k=>$v){
              if(!empty($v)){
                switch($k){
                    case 'poam_blscr':
                        $data = array('poam_blscr'=>''.$v.'');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_type':
                        $data = array('poam_type'=>''.$v.'',
                                      'poam_status'=>'OPEN',
                                      'poam_date_modified'=>''.$now.'',
                                      'poam_action_planned'=>'null',
                                      'poam_action_date_est'=>'null',
                                      'poam_action_date_actual'=>'null',
                                      'poam_action_resources'=>'null',
                                      'poam_action_status'=>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        /*$data = array('ev_sso_evaluation'=>'EXCLUEDE');
                        $result = $db->update('POAM_EVIDENCE',$data,array('poam_id = '.$id.'','ev_sso_evalution="NONE"'));
                        $data = array('ev_fsa_evaluation'=>'EXCLUDED');
                        $result = $db->update('POAM_EVIDENCE',$data,array('poam_id = '.$id.'','ev_fsa_evalution="NONE"'));
                        $data = array('ev_ivv_evaluation'=>'EXCLUDED');
                        $result = $db->update('POAM_EVIDENCE',$data,array('poam_id = '.$id.'','ev_ivv_evalution="NONE"'));*/
                        break;
                    case 'poam_action_planned':
                        $data = array('poam_action_planned'=>''.$v.'',
                                      'poam_action_status'=>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_action_date_est':
                        $data = array('poam_action_date_est'=>''.$v.'',
                                      'poam_action_status'  =>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_action_status':
                        $data = array('poam_action_status' =>''.$v.'');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        if('APPROVED' == $v){
                            $db->update('POAMS',array('poam_status'=>'EN'),'poam_id = '.$id.'');
                        } else {
                            $db->update('POAMS',array('poam_status'=>'OPEN'),'poam_id = '.$id.'');
                        }
                        break;
                    case 'poam_action_suggested':
                        $data = array('poam_action_suggested'=>''.$v.'',
                                      'poam_action_status'   =>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_action_owner':
                        $data = array('poam_action_owner'=>''.$v.'');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;                                                               
                    case 'poam_action_resources':
                        $data = array('poam_action_resources'=>''.$v.'');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_cmeasure_effectiveness':
                        $data = array('poam_cmeasure_effectiveness'=>''.$v.'',
                                      'poam_action_status'         =>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_cmeasure':
                        $data = array('poam_cmeasure'      =>''.$v.'',
                                      'poam_action_status' =>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_cmeasure_justification':
                        $data = array('poam_cmeasure_justification'=>''.$v.'',
                                      'poam_action_status'         =>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_threat_level':
                        $data = array('poam_threat_level' =>''.$v.'',
                                      'poam_action_status'=>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_threat_source':
                        $data = array('poam_threat_source'=>''.$v.'',
                                      'poam_action_status'=>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'poam_threat_justification':
                        $data = array('poam_threat_justification'=>''.$v.'',
                                      'poam_action_status'       =>'NONE');
                        $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        break;
                    case 'sso_evaluate':
                        $data['ev_sso_evaluation'] = $v;
                        $data['ev_date_sso_evaluation'] = $now;
                        if('DENIED' == $v ){
                            $data['ev_fsa_evaluation'] = 'EXCLUDED';
                            $data['ev_ivv_evaluation'] = 'EXCLUDED';
                        }
                        $result = $db->update('POAM_EVIDENCE',$data,'poam_id = '.$id.'');
                        break;
                    case 'fsa_evaluate':
                        $data['ev_fsa_evaluation'] = $v;
                        $data['ev_date_fsa_evaluation'] = $now;
                        if('DENIED' == $v ){
                            $data['ev_ivv_evaluation'] = 'EXCLUDED';
                        }
                        $result = $db->update('POAM_EVIDENCE',$data,'poam_id = '.$id.'');
                        if('APPROVED' == $v){
                            $data = array('poam_status'=>'ES');
                            $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        }
                        break;
                    case 'ivv_evaluate':
                        $data = array('ev_ivv_evaluation'=>''.$v.'',
                                      'ev_date_ivv_evaluation'=>''.$now.'');
                        $result = $db->update('POAM_EVIDENCE',$data,'poam_id = '.$id.'');
                        if('APPROVED' == $v){
                            $data = array('poam_status'=>'CLOSED',
                                          'poam_date_closed'=>''.$now.'');
                            $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                            $data = array('FINDINGS.finding_status'=>'CLOSED',
                                          'FINDINGS.finding_date_closed'=>''.$now.'');
                            $result = $db->update('FINDINGS',$data,'POAMS.finding_id = FINDINGS.finding_id' AND 
                                                                         'poam_id = '.$id.'');
                        }
                        if('DENIED' == $v){
                            $data = array('poam_status'=>'EN','poam_action_date_actual'=>'NULL');
                            $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
                        }
                        break;
                }
              }
            }
            $data = array('poam_date_modified'=>''.$now.'',
                          'poam_modified_by'  =>''.$this->me->user_id.'');
            $result = $db->update('POAMS',$data,'poam_id = '.$id.'');
            //$query = $db->select()->from(array('p'=>'POAMS'),array('poam_d'=>'poam_d'));            
            $user_id = $this->me->user_id;
            $now = time();
            $eventArray = array('poam_action_owner'=>'UPDATE: responsible system',
                  'poam_type'=>'UPDATE: remediation type',
                  'poam_status'=>'UPDATE: remediation status',
                  'poam_blscr'=>'UPDATE: BLSCR number',
                  'poam_action_date_est'=>'UPDATE: course of action estimated completion date',
                  'poam_action_status'=>'UPDATE: course of action evaluation',
                  'poam_cmeasure_effectiveness'=>'UPDATE: countermeasure effectiveness',
                  'poam_action_suggested'=>'UPDATE: recommended course of action',
                  'poam_action_planned'=>'UPDATE: course of action',
                  'poam_action_resources'=>'UPDATE: course of action resources',
                  'poam_cmeasure'=>'UPDATE: countermeasure',
                  'poam_cmeasure_justification'=>'UPDATE: countermeasure justification',
                  'poam_threat_source'=>'UPDATE: threat source',
                  'poam_threat_justification'=>'UPDATE: threat justification',
                  'poam_previous_audits'=>'UPDATE: previous audits',
                  'poam_threat_level'=>'UPDATE: threat level',
                  'ev_sso_evaluation'=>'UPDATE: SSO evidence evaluation',
                  'ev_fsa_evaluation'=>'UPDATE: FSA evidence evaluation',
                  'ev_ivv_evaluation'=>'UPDATE: IV&V evidence evaluation'
                  );
            foreach($_POST as $k=>$v){
                if('poam_' == substr($k,0,5) && !empty($v)){
                    $data = array('finding_id'=>''.$poams['finding_id'].'',        
                                  'user_id'   =>$user_id,
                                  'date'      =>$now,
                                  'event'     =>''.$eventArray[$k].'',
                                  'description'=>'Original:'.$poams[$k].' New:'.$v.'');
                    $result = $db->insert('AUDIT_LOG',$data);
                }
            }
        }
        $query = $poam->select()->setIntegrityCheck(false);

        // Finding Information Query

        $query->from(array('p'=>'POAMS'),array());
        $query->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array('f_id'=>'f.finding_id',
                                                                                'f_status'=>'f.finding_status',
                                                                                'f_discovered'=>'f.finding_date_discovered',
                                                                                'f_created'=>'f.finding_date_created',
                                                                                'f_data'=>'f.finding_data'));
        $query->join(array('fs'=>'FINDING_SOURCES'),'fs.source_id = f.source_id',array('fs_nickname'=>'fs.source_nickname',
                                                                                      'fs_name'=>'fs.source_name'));
        $query->join(array('a'=>'ASSETS'),'a.asset_id = f.asset_id',array('asset_id'=>'a.asset_id',
                                                                         'asset_name'=>'a.asset_name'));
        $query->join(array('sa'=>'SYSTEM_ASSETS'),'sa.asset_id = a.asset_id',array());
        $query->join(array('s'=>'SYSTEMS'),'sa.system_id = s.system_id',array('system_nickname'=>'s.system_nickname',
                                                                             'system_name'=>'s.system_name'));
        $query->where("sa.system_is_owner = 1");
        $query->where("p.poam_id = ".$id."");
        $finding = $poam->fetchRow($query)->toArray();
        $this->view->assign('finding',$finding);
        
        //Asset Network And Addresses Query

        $query->reset();
        $query->from(array('n'=>'NETWORKS'),array('network_nickname'=>'n.network_nickname'));
        $query->join(array('aa'=>'ASSET_ADDRESSES'),'n.network_id = aa.network_id',array('ip'=>'aa.address_ip',
                                                                                         'port'=>'aa.address_port'));
        $query->where("aa.asset_id = ".$finding['asset_id']."");
        $asset_address = $poam->fetchAll($query)->toArray();
        $this->view->assign('asset_address',$asset_address); 
        // Finding Vulnerabilities Query

        $query->reset();
        $query->from(array('p'=>'POAMS'),array());
        $query->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array());
        $query->join(array('fv'=>'FINDING_VULNS'),'fv.finding_id = f.finding_id',array());
        $query->join(array('v'=>'VULNERABILITIES'),'v.vuln_type = fv.vuln_type AND v.vuln_seq = fv.vuln_seq',
                          array('type'=>'v.vuln_type',
                                'seq'=>'v.vuln_seq',
                                'primary'=>'v.vuln_desc_primary',
                                'secondary'=>'v.vuln_desc_secondary'));
        $query->where("p.poam_id = ".$id."");
        $vulnerabilities = $poam->fetchAll($query)->toArray();

        // Remediation Query

        $query->reset();
        $query->from(array('p'=>'POAMS'),'*');
        $query->join(array('s'=>'SYSTEMS'),'s.system_id = p.poam_action_owner',array('system_nickname'=>'s.system_nickname',
                                                                                     'system_name'=>'s.system_name'));
        $query->join(array('u1'=>'USERS'),'u1.user_id = p.poam_created_by',array('created_by'=>'u1.user_name'));
        $query->join(array('u2'=>'USERS'),'u2.user_id = p.poam_modified_by',array('modified_by'=>'u2.user_name'));
        $query->where("p.poam_id = ".$id."");
        $data = $poam->fetchRow($query);
        if(!empty($data)){
            $remediation = $data->toArray();
            $this->view->assign('remediation',$remediation);

            $est = implode(split('-',$remediation['poam_action_date_est']));
            if(($est < $today) && ($remediation['poam_status']=='EN')){
                $remediation['poam_status'] = 'EO';
            }
            $this->view->assign('remediation_status',$remediation['poam_status']);
            $this->view->assign('remediation_type',$remediation['poam_type']);
            $this->view->assign('threat_level',$remediation['poam_threat_level']);
            $this->view->assign('cmeasure_effectiveness',$remediation['poam_cmeasure_effectiveness']);
      
           // Product Query
            $query->reset();
            $query->from(array('p'=>'PRODUCTS'),array('prod_id'=>'p.prod_id',
                                               'prod_vendor'=>'p.prod_vendor',
                                               'prod_name'=>'p.prod_name',
                                               'prod_version'=>'p.prod_version'));
            $query->join(array('a'=>'ASSETS'),'a.prod_id = p.prod_id',array());
            $query->join(array('f'=>'FINDINGS'),'a.asset_id = f.asset_id',array());
            $query->where("f.finding_id = ".$remediation['finding_id']."");
            $products = $poam->fetchRow($query);
            $this->view->assign('products',$products);
        }
        
        //Blscr Query
        $query->reset();
        $query->from(array('b'=>'BLSCR'),'*');
        $query->join(array('p'=>'POAMS'),'p.poam_blscr = b.blscr_number',array());
        $query->where("p.poam_id = ".$id."");
        $data = $poam->fetchRow($query);
        if(!empty($data)){
            $blscr = $poam->fetchRow($query)->toArray();
        }
        else {
            $blscr = array();
        }
        $query->reset();
        $query->distinct()->from(array('b'=>'BLSCR'),array('value'=>'b.blscr_number'))
                          ->order("b.blscr_number ASC");
        $this->view->assign('all_values',$db->fetchCol($query));

        // Comments Query
        $query->reset();
        $query->from(array('pc'=>'POAM_COMMENTS','*'));
        $query->join(array('u'=>'USERS'),'u.user_id = pc.user_id',array('user_name'=>'u.user_name'));
        $query->where("pc.poam_id = ".$id."");
        $query->order("pc.comment_date DESC");
        $comments = $poam->fetchAll($query)->toArray();
        $comments_est = $comments_sso = $comments_ev = array();
        if(count($comments) >0 ){
            foreach($comments as &$comment){
                $comment['comment_topic'] = stripslashes($comment['comment_topic']);
                $comment['comment_body'] = nl2br($comment['comment_log']);
                $comment['comment_log'] = nl2br($comment['comment_log']);
                if($comment['comment_type'] == 'EST'){
                    $comments_est[] = $comment;
                }
                elseif($comment['comment_type'] == 'SSO'){
                    $comments_sso[] = $comment;
                }
                elseif(isset($comment['ev_id']) && ($comment['ev_id']>0)){
                    $comments_ev[$comment['ev_id']][$comment['comment_type']] = $comment;
                }
            }
        }
        $this->view->assign('comments_ev',$comments_ev);
        $this->view->assign('comments_est',$comments_est);
        $this->view->assign('comments_sso',$comments_sso);
        $this->view->assign('num_comments_est',count($comments_est));
        $this->view->assign('num_comments_sso',count($comments_sso));

        // Evidence Query
        $query->reset();
        $query->from(array('pe'=>'POAM_EVIDENCE'),'*');
        $query->join(array('u'=>'USERS'),'u.user_id = pe.ev_submitted_by',array('submitted_by'=>'u.user_name'));
        $query->where("pe.poam_id = ".$id."");
        $query->order("pe.ev_date_submitted ASC");
        $all_evidence = $poam->fetchAll($query)->toArray();
        $num_evidence = count($all_evidence);
        if($num_evidence){
            foreach($all_evidence as &$evidence){
                if($comments_ev != null){
                    $evidence['comments'] = $comments_ev[$evidence['ev_id']];
                }
                $evidence['fileName'] = basename($evidence['ev_submission']);
                if(file_exists($evidence['ev_submission'])){
                    $evidence['fileExists'] = 1;
                }
                else {
                    $evidence['fileExists'] = 0;
                }
            }
        }
        $this->view->assign('all_evidence',$all_evidence);
        $this->view->assign('num_evidence',$num_evidence);

        //Audit Log
        $query->reset();
        $query->from(array('al'=>'AUDIT_LOG'),array('*','time'=>'al.date'));
        $query->join(array('p'=>'POAMS'),'p.finding_id = al.finding_id',array());
        $query->join(array('u'=>'USERS'),'al.user_id = u.user_id',array('user_name'=>'u.user_name'));
        $query->where("p.poam_id = ".$id."");
        $query->order("al.date DESC");
        $logs = $poam->fetchAll($query)->toArray();
        foreach($logs as $k=>$v){
            //$date_default_timezone_set('America/New_York');
            $logs[$k]['time'] = date('Y-m-d H:i:s',$logs[$k]['time']);
        }
        $this->view->assign('logs',$logs);
        $this->view->assign('num_logs',count($logs));

        //Root Comment
        $query->reset();
        $query->from(array('pc'=>'POAM_COMMENTS'),array('comment_id'=>'pc.comment_id'));
        $query->where("pc.poam_id = ".$id."");
        $query->where("pc.comment_parent is null");
        $root_comment = $poam->fetchRow($query);
        $this->view->assign('root_comment',$root_comment);

        //All Fields Ok?
        if(!empty($remediation)){
            $r = $remediation;
            $r_fields_null = array($r['poam_threat_source'], $r['poam_threat_justification'],
            $r['poam_cmeasure'], $r['poam_cmeasure_justification'], $r['poam_action_suggested'],
            $r['poam_action_planned'], $r['poam_action_resources'], $r['poam_blscr']);
            $r_fields_zero = array($r['poam_action_date_est']);
            $r_fields_none = array($r['poam_cmeasure_effectiveness'], $r['poam_threat_level']);
            $is_completed = (in_array(null, $r_fields_null) || in_array('NONE', $r_fields_none) || in_array('0000-00-00', $r_fields_zero))?'no':'yes';
            $this->view->assign('is_completed', $is_completed);
        }
        
        
        $user = new user();
        $uid = $this->me->user_id;
        $ids = implode(',', $user->getMySystems($uid));
        $qry = $db->select()->from(array('s'=>'SYSTEMS'), array('id'=>'system_id',
                                                              'name'=>'system_name',
                                                              'nickname'=>'system_nickname'))
                                    ->where("system_id IN ( $ids )")
                                    ->order('id ASC');
        $system_list = $db->fetchAll($qry);
        $this->view->assign('system_list',$system_list);
        $this->view->assign('vulner',$vulnerabilities);
        $this->view->assign('blscr',$blscr);
        $this->view->assign('remediation_id',$id);
        $this->render();
    }
    
}
