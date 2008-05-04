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
require_once MODELS . DS . 'remediation.php';
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
        $this->_paging['currentPage'] = $req->getParam('P',1);
    }

    public function summaryAction(){
        $poam = new remediation();
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl().'/panel/remediation/sub/searchbox/s/search';
        $this->_paging['currentPage'] = $req->getParam('p',1);
        $s = $req->getParam('s');
        if('search' == $s){
            $criteria = $req->getParam('criteria');
            assert(is_array($criteria));
            extract($criteria);
        }
        $user = new user();
        $db = $poam->getAdapter();
        $uid = $this->me->user_id;
        $today = date('Ymd',time());
               
        $system_list = implode(",",$user->getMySystems($uid));
        $query = $poam->select()->setIntegrityCheck(false);
        $query->from(array('f'=>'FINDINGS'),array('finding_data'=>'f.finding_data'));
        $query->join(array('fs'=>'FINDING_SOURCES'),'f.source_id = fs.source_id',
                                                  array('source_nickname'=>'fs.source_nickname',
                                                        'source_name'=>'fs.source_name'));
        $query->join(array('p'=>'POAMS'),'p.finding_id = f.finding_id',
                                                  array('poam_id'=>'p.poam_id',
                                                        'legacy_poam_id'=>'p.legacy_poam_id',
                                                        'poam_type'=>'p.poam_type',
                                                        'poam_status'=>'p.poam_status',
                                                        'poam_date_created'=>'p.poam_date_created',
                                                        'poam_action_date_est'=>'p.poam_action_date_est'));
        $query->join(array('sa'=>'SYSTEM_ASSETS'),'sa.asset_id = f.asset_id',array());
        $query->join(array('s1'=>'SYSTEMS'),'s1.system_id = sa.system_id',
                                                  array('asset_owner_id'=>'s1.system_id',
                                                         'asset_owner_nickname'=>'s1.system_nickname',
                                                         'asset_owner_name'=>'s1.system_name'));
        $query->join(array('s2'=>'SYSTEMS'),'s2.system_id = p.poam_action_owner',
                                                  array('action_owner_id'=>'s2.system_id',
                                                        'action_owner_nickname'=>'s2.system_nickname',
                                                        'action_owner_name'=>'s2.system_name'));
        
        if(isset($source) && $source != 'any'){
            $query->where("fs.source_id = ".$source."");
        }
        if(isset($system) && $system != 'any'){
            $query->where("p.poam_action_owner = ".$system."");
        }
        if(!empty($startdate) && !empty($enddate)){
            $startdate = date("Y-m-d",strtotime($startdate));
            $enddate = date("Y-m-d",strtotime($enddate));
            $query->where("p.poam_action_date_est >='".$startdate."' AND p.poam_action_date_est <='".$enddate."'");
        }
        if(!empty($startcreatedate) && !empty($endcreatedate)){
            $startcreatedate = date("Y-m-d",strtotime($startcreatedate));
            $endcreatedate = date("Y-m-d",strtotime($endcreatedate));
            $query->where("p.poam_date_created >='".$startcreatedate."' AND p.poam_date_created <='".$endcreatedate."'");
        }
        if(isset($asset_owner) && $asset_owner != 'any'){
            $query->where("s1.system_id = ".$asset_owner."");
        }
        if(isset($action_owner) && $action_owner != 'any'){
            $query->where("s2.system_id = ".$action_owner."");
        }
        if(isset($ids) && !empty($ids)){
            $query->where("p.poam_id IN (".$ids.")");
        }
        if(isset($type) && $type != 'any'){
            $query->where("p.poam_type = '".$type."'");
        }
        if(isset($status) && $status != 'any'){
            $current_date = date("Y-m-d",time());  
            switch($status){
                case 'NEW':
                    $query->where("p.poam_status = 'OPEN' AND p.poam_type = 'NONE'");
                    break;
                case 'OPEN':
                    $query->where("p.poam_status = 'OPEN' AND p.poam_type != 'NONE'");
                    break;
                case 'EN':
                    $query->where("p.poam_status = 'EN' AND p.poam_action_date_est >= CURDATE()");
                    break;
                case 'EO':
                    $query->where("p.poam_status = 'EN' AND
                                 (p.poam_action_date_est < '$current_date' or p.poam_action_date_est is NULL)");
                    break;
                case 'EP-SSO':
                    $query->where("p.poam_status = 'EP' AND p.poam_id IN 
                    (SELECT DISTINCT pe.poam_id FROM `POAM_EVIDENCE` AS pe WHERE(pe.ev_ivv_evaluation = 'NONE'
                    AND pe.ev_fsa_evaluation = 'NONE' AND pe.ev_ivv_evaluation = 'NONE'))");
                    break;
                case 'EP-SNP':
                    $query->where("p.poam_status = 'EP' AND p.poam_id IN 
                    (SELECT DISTINCT pe.poam_id FROM `POAM_EVIDENCE` AS pe WHERE 
                    (pe.ev_sso_evaluation = 'APPROVED' AND 
                    pe.ev_fsa_evaluation = 'NONE' AND 
                    pe.ev_ivv_evaluation = 'NONE') ORDER BY ev_id DESC)");
                    break;
                case 'ES':
                    $query->where("p.poam_status = 'ES'");
                    break;
                case 'CLOSED':
                    $query->where("p.poam_status = 'CLOSED'");
                    break;
                case 'NOT-CLOSED':
                    $query->where("p.poam_status NOT LIKE 'CLOSED'");
                    break;
                case 'NOUP-30':
                    $query->where("p.poam_status NOT LIKE 'CLOSED' AND 
                                 p.poam_date_modified < SUBDATE('".$current_date."',30)");
                    break;
                case 'NOUP-60':
                    $query->where("p.poam_status NOT LIKE 'CLOSED' AND 
                                 p.poam_date_modified < SUBDATE('".$current_date."',60)");
                    break;
                case 'NOUP-90':
                    $query->where("p.poam_status NOT LIKE 'CLOSED' AND 
                                p.poam_date_modified < SUBDATE('".$current_date."',90)");
                    break;
                default:
                    $query->where("p.poam_status = ".$status."");
                    break;
            }
        }
        $query->where("sa.system_is_owner = 1 ");                                                        
        $query->where("poam_action_owner IN ($system_list)");
        $query->order('action_owner_name ASC');
        //echo $query->__toString();
        $list = $poam->fetchAll($query)->toArray();
        $query->limitPage($this->_paging['currentPage'],$this->_paging['perPage']);
        $data = $poam->fetchAll($query);
        $summary_list = $data->toArray();
        
        /** SUMMARY INFORMATION CREATION **/
        $summary = Array();
        $array_template = array('NEW'=>'','OPEN'=>'','EN'=>'','ED'=>'','EO'=>'','EP'=>'','ES'=>'','EP_SNP'=>'',
                                'EP_SSO'=>'','CLOSED'=>'','TOTAL'=>'');
        $totals = $array_template;
        $total_pages = 0;
        for($row=0;$row < count($list); $row++){
            $this_system = $list[$row]['action_owner_id'];
            if(!isset($summary[$this_system])){
                $summary[$this_system] = $array_template;
            }
            $summary[$this_system]['action_owner_nickname'] = $list[$row]['action_owner_nickname'];
            $summary[$this_system]['action_owner_name']     = $list[$row]['action_owner_name'];
            
            //count the NEW items
            if(($list[$row]['poam_status'] == 'OPEN') && ($list[$row]['poam_type'] == 'NONE')) {
                $summary[$this_system]['NEW'] += 1;
                $totals['NEW'] += 1;
            }

            //count the OPEN items
            if(($list[$row]['poam_status'] == 'OPEN') && ($list[$row]['poam_type'] != 'NONE')){                
                $summary[$this_system]['OPEN'] += 1;
                $totals['OPEN'] += 1;
            }

            //count the EN and EO items
            if($list[$row]['poam_status'] == 'EN'){
                $est = implode(split('-',$list[$row]['poam_action_date_est']));
                if($est < $today){
                    //update that the date has passed
                    $list[$row]['poam_status'] = 'EO';
                    //count the remediation as overdue
                    $summary[$this_system]['EO'] += 1;
                    $totals['EO'] += 1;
                    //update the display to show it as EN in the list
                    $list[$row]['poam_status'] = 'EO';
                }
                //still on time,just count it
                else {
                    $summary[$this_system]['EN'] += 1;
                    $totals['EN'] += 1;
                }
            }
            //count the EP items
            if($list[$row]['poam_status'] == 'EP'){
                $summary[$this_system]['EP'] += 1;
                $totals['EP'] += 1;
                //grab the SSO approvals to differentiate the EPs
                $query->reset();
                $query->from(array('pe'=>'POAM_EVIDENCE'),array('evaluation'=>'ev_sso_evaluation'));
                $query->where("poam_id = ".$list[$row]['poam_id']."");
                $query->order("ev_id DESC");
                $query->limit(1);
                $approval = $poam->fetchRow($query)->toArray();
                //if the SSO has approved it,then tag it as S&P
                if($approval['evaluation'] == 'APPROVED'){
                    $summary[$this_system]['EP_SNP'] += 1;
                    $totals['EP_SNP'] += 1;
                }
                //else tag it SSO
                else {
                    $summary[$this_system]['EP_SSO'] += 1;
                    $totals['EP_SSO'] += 1;
                }
            }
            //count the ES items
            if($list[$row]['poam_status'] == 'ES'){
                $summary[$this_system]['ES'] += 1;
                $totals['ES'] += 1;
            }
            //count the CLOSED items
            if($list[$row]['poam_status'] == 'CLOSED'){
                $summary[$this_system]['CLOSED'] += 1;
                $totals['CLOSED'] += 1;
            }
            //count the total number for the system
            $summary[$this_system]['TOTAL'] += 1;
            $totals['TOTAL'] += 1;
            //total pages
            $total_pages = ceil($totals['TOTAL'] /$this->_paging['perPage']);
        }
        $this->_paging['totalItems'] = $totals['TOTAL'];
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $pager = &Pager::factory($this->_paging);
        $this->view->assign('list',$list);
        $this->view->assign('summary',$summary);
        $this->view->assign('summary_list',$summary_list);
        $this->view->assign('total_pages',$total_pages);
        $this->view->assign('totals',$totals);
        $this->view->assign('links',$pager->getLinks());
        if('search' == $s){
            $this->render('search');
        }
        else {
            $this->render();
        }
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
            $this->_helper->actionStack('summary','Remediation',null,
                                        array('s' =>'search',
                                              'criteria'=>$criteria));
        }
        $this->view->assign('criteria',$criteria);
        $this->view->assign('system_list',$system_list);
        $this->view->assign('source_list',$source_list);
        $this->view->assign('filter_type',$filter_type);
        $this->view->assign('filter_status',$filter_status);
        $this->render();
    }

    
}
