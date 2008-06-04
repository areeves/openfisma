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

    public function init()
    {
        $this->_poam = new Poam();
        parent::init();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl() .'/panel/remediation/sub/searchbox/s/search';
        $this->_paging['currentPage'] = $req->getParam('p',1);
    }

    public function summaryAction(){
        require_once MODELS . DS . 'system.php';
        $req = $this->getRequest();
        $user = new user();
        $system_list = $this->me->systems;
        $sys = new System();
        $systems = $sys->getList(array('name','nickname'),$system_list);

        $today = parent::$now->toString('Ymd');

        $summary_tmp = array('NEW'=>0,'OPEN'=>0,'EN'=>0,'EO'=>0,'EP'=>0,'EP_SNP'=>0,'EP_SSO'=>0,'ES'=>0,'CLOSED'=>0,'TOTAL'=>0);
        ///@todo EP_SNP & EP_SSO not counted
        $total = $summary_tmp;
        foreach($system_list as $id) {
            $summary[$id] = $summary_tmp;
            $sum = $this->_poam->search(array($id),array('count'=>'status','status'));
            foreach( $sum as $s ) {
                $summary[$id][$s['status']] = $s['count'];
                $summary[$id]['TOTAL'] += $s['count']; //ATTENTION!!!!
                $total[$s['status']] += $s['count'];
            }
        }
        $this->view->assign('total',$total);
        $this->view->assign('systems',$systems);
        $this->view->assign('summary',$summary );
        $this->render('summary');
    }

    public function searchAction(){
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl().'/panel/remediation/sub/searchbox/s/search';
        $this->_paging['currentPage'] = $req->getParam('p',1);
       
        $system_list = array_keys($this->me->systems);
        $criteria = $req->getParam('criteria');
        foreach($criteria as $key=>$value){
            if(!empty($value) && $value!='0'){
                $this->_paging_base_path .='/'.$key.'/'.$value.'';
            }
        }

        //$this->_paging_base_path .= $req->getParam('path');
        
        $list = $this->_poam->search($system_list, array('id',
                                                         'source_id',
                                                         'system_id',
                                                         'type',
                                                         'status',
                                                         'finding_data',
                                                         'action_est_date',
                                                         'count'=>'count(*)'),
                                     $criteria,$this->_paging['currentPage'],
                                     $this->_paging['perPage']);
        $total = array_pop($list);

        $this->_paging['totalItems'] = $total;
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $pager = &Pager::factory($this->_paging);
        $this->view->assign('list',$list);
        $this->view->assign('total_pages',$total);
        $this->view->assign('links',$pager->getLinks());
        $this->render('search');
    }

    public function searchboxAction()
    {
        require_once MODELS . DS . 'system.php';
        require_once MODELS . DS . 'source.php';
        require_once MODELS . DS . 'network.php';

        $db = Zend_Registry::get('db');
        $src = new Source();
        $net = new Network();
        $sys = new System();
        
        $req = $this->getRequest();
        // parse the params of search
        $criteria['system'] = $req->getParam('system',0);
        $criteria['source'] = $req->getParam('source',0);
        $criteria['type'] = $req->getParam('type');
        $criteria['status'] = $req->getParam('status');
        $criteria['ids'] = $req->getParam('ids','');
        $criteria['asset_owner'] = $req->getParam('asset_owner',0);
        $criteria['system_id'] = $req->getParam('action_owner',0);
        $tmp = $req->getParam('est_date_begin');
        if(!empty($tmp)) {
            $criteria['est_date_begin'] = new Zend_Date($tmp);
        }
        $tmp = $req->getParam('est_date_end');
        if(!empty($tmp)) {
            $criteria['est_date_end'] = new Zend_Date($tmp);
        }
        $tmp = $req->getParam('created_data_begin');
        if(!empty($tmp)) {
            $criteria['created_data_begin'] = new Zend_Date($tmp);
        }
        $tmp = $req->getParam('created_data_end');
        if(!empty($tmp)) {
            $criteria['created_data_end'] = new Zend_Date($tmp);
        }

        $internal_crit = $criteria;

        $source_list  = $src->getList('name');
        $system_list = $sys->getList('name',array_keys($this->me->systems) );
        $system_list = array_merge(array('0'=>'--Any--'),$system_list);
        $source_list = array_merge(array('0'=>'--Any--'),$source_list);
        
        $filter_type = array(0  =>'--- Any Type ---',
                        'NONE' =>'(NONE) Unclassified',
                        'CAP'  =>'(CAP) Corrective Action Plan',
                        'AR'   =>'(AR) Accepted Risk',
                        'FP'   =>'(FP) False Positive');

        $filter_status = array(0    =>'--- Any Status ---',
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
            if( !empty($criteria['status']) ){
                $now = clone parent::$now;
                switch($criteria['status']){
                    case 'NEW':
                        $internal_crit['status'] = 'NEW';
                        $internal_crit['type']   = 'NONE';
                        break;
                    case 'OPEN':
                        $internal_crit['status'] = 'OPEN';
                        break;
                    case 'EN':
                        $internal_crit['status'] = 'EN';
                        $internal_crit['est_date_begin'] = $now->toString('Ymd');
                        break;
                    case 'EO':
                        $internal_crit['status'] = 'EN';                        
                        $internal_crit['est_date_end'] = $now->toString('Ymd');
                        break;
                    case 'EP-SSO':
                    ///@todo EP searching needed
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
                        $internal_crit['modify_ts'] = $now->sub(30, Zend_Date::DAY);
                        break;
                    case 'NOUP-60':
                        $internal_crit['status'] = array("'OPEN'","'EN'","'EP'","'ES'");
                        $internal_crit['modify_ts'] = $now->sub(60, Zend_Date::DAY);
                        break;
                    case 'NOUP-90':
                        $internal_crit['status'] = array("'OPEN'","'EN'","'EP'","'ES'");
                        $internal_crit['modify_ts'] = $now->sub(90, Zend_Date::DAY);
                        break;
                }
            }
            $this->_helper->actionStack('search','Remediation',null,array('criteria'=>$internal_crit));
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
    **/
    public function viewAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $today = date("Ymd",time());
        $poam = new poam();
        $db = $poam->getAdapter();
        $query = $poam->select()->setIntegrityCheck(false);
        // Finding Information Query
        $query->from(array('p'=>'poams'),array('f_id'=>'p.legacy_finding_id',
                                               'f_status'=>'p.status',
                                               'f_data'  =>'p.finding_data',
                                               'f_discovered'=>'p.discover_ts',
                                               'f_created'=>'p.create_ts'));
        $query->join(array('s'=>'sources'),'s.id = p.source_id',array('source_nickname'=>'s.nickname',
                                                                      'source_name'=>'s.name'));
        $query->join(array('a'=>'assets'),'a.id = p.asset_id',array('asset_id'=>'a.id',
                                                                    'asset_name'=>'a.name'));
        $query->join(array('sys'=>'systems'),'sys.id = p.system_id',array('system_nickname'=>'sys.nickname',
                                                                      'system_name'=>'sys.name'));
        $query->where("p.id = ".$id."");
        $finding = $poam->fetchRow($query)->toArray();
        $this->view->assign('finding',$finding);
        
        //Asset Network And Addresses Query

        $query->reset();
        $query->from(array('n'=>'networks'),array('network_nickname'=>'n.nickname'));
        $query->join(array('a'=>'assets'),'n.id = a.network_id',array('ip'=>'a.address_ip',
                                                                      'port'=>'a.address_port'));
        $query->where("a.id = ".$finding['asset_id']."");
        $asset_address = $poam->fetchAll($query)->toArray();
        $this->view->assign('asset_address',$asset_address); 
        // Finding Vulnerabilities Query

        $query->reset();
        $query->from(array('p'=>'poams'),array());
        $query->join(array('pv'=>'poam_vulns'),'pv.poam_id = p.id',array());
        $query->join(array('v'=>'vulnerabilities'),'v.type = pv.vuln_type AND v.seq = pv.vuln_seq',
                          array('type'=>'v.type',
                                'seq'=>'v.seq'));
                                //'primary'=>'v.desc_primary',
                                //'secondary'=>'v.desc_secondary'));
        $query->where("p.id = ".$id."");
        $vulnerabilities = $poam->fetchAll($query)->toArray();

        // Remediation Query

        $query->reset();
        $query->from(array('p'=>'poams'),'*');
        $query->join(array('s'=>'systems'),'s.id = p.system_id',array('system_nickname'=>'s.nickname',
                                                                      'system_name'=>'s.name'));
        $query->join(array('u'=>'users'),'u.id = p.modified_by',array('modified_by'=>'u.account'));
        $query->where("p.id = ".$id."");
        $data = $poam->fetchRow($query);
        if(!empty($data)){
            $remediation = $data->toArray();
            $this->view->assign('remediation',$remediation);

            $est = implode(split('-',$remediation['action_est_date']));
            if(($est < $today) && ($remediation['status']=='EN')){
                $remediation['status'] = 'EO';
            }
            $this->view->assign('remediation_status',$remediation['status']);
            $this->view->assign('remediation_type',$remediation['type']);
            $this->view->assign('threat_level',$remediation['threat_level']);
            $this->view->assign('cmeasure_effectiveness',$remediation['cmeasure_effectiveness']);
      
           // Product Query
            $query->reset();
            $query->from(array('pr'=>'products'),array('prod_id'=>'pr.id',
                                               'prod_vendor'=>'pr.vendor',
                                               'prod_name'=>'pr.name',
                                               'prod_version'=>'pr.version'));
            $query->join(array('a'=>'assets'),'a.prod_id = pr.id',array());
            $query->join(array('p'=>'poams'),'p.asset_id = a.id',array());
            $query->where("p.id = ".$remediation['id']);
            $products = $poam->fetchRow($query);
            $this->view->assign('products',$products);
        }
        
        //Blscr Query
        $query->reset();
        $query->from(array('b'=>'blscrs'),'*');
        $query->join(array('p'=>'poams'),'p.blscr = b.code',array());
        $query->where("p.id = ".$id."");
        $data = $poam->fetchRow($query);
        if(!empty($data)){
            $blscr = $poam->fetchRow($query)->toArray();
        }
        else {
            $blscr = array();
        }
        $query->reset();
        $query->distinct()->from(array('b'=>'blscrs'),array('value'=>'b.code'))
                          ->order("b.code ASC");
        $this->view->assign('all_values',$db->fetchCol($query));

        // Comments Query
        $query->reset();
        $query->from(array('c'=>'comments','*'));
        $query->join(array('u'=>'users'),'u.id = c.user_id',array('user_name'=>'u.account'));
        $query->where("c.id = ".$id."");
        $query->order("c.date DESC");
        $comments = $poam->fetchAll($query)->toArray();
        $comments_est = $comments_sso = $comments_ev = array();
        if(count($comments) >0 ){
            foreach($comments as &$comment){
                $comment['topic'] = stripslashes($comment['topic']);
                $comment['content'] = nl2br($comment['content']);
            }
        }
        $this->view->assign('comments_ev',$comments_ev);
        $this->view->assign('comments_est',$comments_est);
        $this->view->assign('comments_sso',$comments_sso);
        $this->view->assign('num_comments_est',count($comments_est));
        $this->view->assign('num_comments_sso',count($comments_sso));

        // Evidence Query
        $query->reset();
        $query->from(array('e'=>'evidences'),'*');
        $query->join(array('u'=>'users'),'u.id = e.submitted_by',array('submitted_by'=>'u.account'));
        $query->where("e.id = ".$id."");
        $query->order("e.date_submitted ASC");
        $all_evidence = $poam->fetchAll($query)->toArray();
        $num_evidence = count($all_evidence);
        if($num_evidence){
            foreach($all_evidence as &$evidence){
                if($comments_ev != null && !empty($comments_ev[$evidence['ev_id']])){
                    $evidence['comments'] = $comments_ev[$evidence['ev_id']];
                }
                $evidence['fileName'] = basename($evidence['submission']);
                if(file_exists($evidence['submission'])){
                    $evidence['fileExists'] = 1;
                }else {
                    $evidence['fileExists'] = 0;
                }
            }
        }
        $this->view->assign('all_evidence',$all_evidence);
        $this->view->assign('num_evidence',$num_evidence);

        //Audit Log
        $query->reset();
        $query->from(array('al'=>'audit_logs'),array('*','time'=>'al.timestamp'));
        $query->join(array('p'=>'poams'),'p.id = al.poam_id',array());
        $query->join(array('u'=>'users'),'al.user_id = u.id',array('user_name'=>'u.account'));
        $query->where("p.id = ".$id."");
        $query->order("al.timestamp DESC");
        $logs = $poam->fetchAll($query)->toArray();var_dump($logs);
        foreach($logs as $k=>$v){var_dump($logs[$k]['timestamp']);
            $logs[$k]['time'] = date('Y-m-d H:i:s',$logs[$k]['timestamp']);
        }var_dump($logs);
        $this->view->assign('logs',$logs);
        $this->view->assign('num_logs',count($logs));

        //Root Comment
        $query->reset();
        $query->from(array('c'=>'comments'),array('comment_id'=>'c.id'));
        $query->where("c.id = ".$id."");
        $root_comment = $poam->fetchRow($query);
        $this->view->assign('root_comment',$root_comment);

        //All Fields Ok?
        if(!empty($remediation)){
            $r = $remediation;
            $r_fields_null = array($r['threat_source'], $r['threat_justification'],
            $r['cmeasure'], $r['cmeasure_justification'], $r['action_suggested'],
            $r['action_planned'], $r['action_resources'], $r['blscr']);
            $r_fields_zero = array($r['action_est_date']);
            $r_fields_none = array($r['cmeasure_effectiveness'], $r['threat_level']);
            $is_completed = (in_array(null, $r_fields_null) || in_array('NONE', $r_fields_none) || in_array('0000-00-00', $r_fields_zero))?'no':'yes';
            $this->view->assign('is_completed', $is_completed);
        }
        
        
        $user = new user();
        $uid = $this->me->id;
        $ids = implode(',', $user->getMySystems($uid));
        $qry = $db->select()->from(array('s'=>'systems'), array('id'=>'id',
                                                                'name'=>'name',
                                                                'nickname'=>'nickname'))
                                    ->where("id IN ( $ids )")
                                    ->order('id ASC');
        $system_list = $db->fetchAll($qry);
        $this->view->assign('system_list',$system_list);
        $this->view->assign('vulner',$vulnerabilities);
        $this->view->assign('blscr',$blscr);
        $this->view->assign('remediation_id',$id);
        $this->render();
    }

    public function modifyAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $comment = $req->getParam('comment_body');
        $ev_id   = $req->getParam('ev_id');
        assert($id);
        $today = date("Ymd",time());
        $user_id = $this->me->id;
        $now = date('Y-m-d,h:i:s',time());
        $poam = new poam();
        $db = $poam->getAdapter();
        foreach($_POST as $k=>$v){
            if(!in_array($k,array('id','comment_body','ev_id')) && !empty($v)){
                $fields[$k] = $k;
            }
        }
        $fields['finding_id'] = 'finding_id';
        /*$query = $db->select()->from(array('p'=>'poams'),array())
                              ->joinleft(array('e'=>'evidences'),'p.id = e.id',array())
                              ->where("p.id = $id");
        $poams = $db->fetchRow($query);*/
        $poams = $poam->find($id)->toArray();
        foreach($_POST as $k=>$v){
          if(!empty($v)){
            switch($k){
                case 'blscr':
                    $data = array('blscr'=>''.$v.'');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'type':
                    $data = array('type'=>''.$v.'',
                                  'status'=>'OPEN',
                                  'modify_ts'=>''.$now.'',
                                  'action_planned'=>'null',
                                  'action_est_date'=>'null',
                                  'action_actual_date'=>'null',
                                  'action_resources'=>'null',
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    $data = array('ev_sso_evaluation'=>'EXCLUEDE');
                    $result = $db->update('POAM_EVIDENCE',$data,array('id = '.$id.'','ev_sso_evaluation="NONE"'));
                    $data = array('ev_fsa_evaluation'=>'EXCLUDED');
                    $result = $db->update('POAM_EVIDENCE',$data,array('id = '.$id.'','ev_fsa_evaluation="NONE"'));
                    $data = array('ev_ivv_evaluation'=>'EXCLUDED');
                    $result = $db->update('POAM_EVIDENCE',$data,array('id = '.$id.'','ev_ivv_evaluation="NONE"'));
                    break;
                case 'action_planned':
                    $data = array('action_planned'=>''.$v.'',
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'action_est_date':
                    $data = array('action_est_date'=>''.$v.'',
                                  'action_status'  =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'action_status':
                    $data = array('action_status' =>''.$v.'');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    if('APPROVED' == $v){
                        $db->update('poams',array('status'=>'EN'),'id = '.$id.'');
                    } else {
                        $db->update('poams',array('status'=>'OPEN'),'id = '.$id.'');
                    }
                    break;
                case 'action_suggested':
                    $data = array('action_suggested'=>''.$v.'',
                                  'action_status'   =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'action_owner':
                    $data = array('system_id'=>''.$v.'');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;                                                               
                case 'action_resources':
                    $data = array('action_resources'=>''.$v.'');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'cmeasure_effectiveness':
                    $data = array('cmeasure_effectiveness'=>''.$v.'',
                                  'action_status'         =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'cmeasure':
                    $data = array('cmeasure'      =>''.$v.'',
                                  'action_status' =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'cmeasure_justification':
                    $data = array('cmeasure_justification'=>''.$v.'',
                                  'action_status'         =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'threat_level':
                    $data = array('threat_level' =>''.$v.'',
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'threat_source':
                    $data = array('threat_source'=>''.$v.'',
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'threat_justification':
                    $data = array('threat_justification'=>''.$v.'',
                                  'action_status'       =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'sso_evaluation':
                    $data['ev_sso_evaluation'] = $v;
                    $data['ev_date_sso_evaluation'] = $now;
                    if('DENIED' == $v ){
                        $data['ev_fsa_evaluation'] = 'EXCLUDED';
                        $data['ev_ivv_evaluation'] = 'EXCLUDED';
                        $comment_data = array('id'=>$id,'user_id'=>$user_id,'comment_date'=>$now,
                                              'ev_id'=>$ev_id,
                                              'comment_topic'=>'UPDATE:','comment_body'=>$comment,
                                              'comment_log'=>'SSO_Evaluation:'.$poams[$k].'=>'.$v,
                                              'comment_type'=>'EV_SSO');
                        $result = $db->insert('POAM_COMMENTS',$comment_data);

                    }
                    $result = $db->update('POAM_EVIDENCE',$data,'id = '.$id.'');
                    break;
                case 'ev_fsa_evaluation':
                    $data['ev_fsa_evaluation'] = $v;
                    $data['ev_date_fsa_evaluation'] = $now;
                    if('DENIED' == $v ){
                        $data['ev_ivv_evaluation'] = 'EXCLUDED';
                        $comment_data = array('id'=>$id,'user_id'=>$user_id,'comment_date'=>$now,
                                              'ev_id'=>$ev_id,
                                              'comment_topic'=>'UPDATE:','comment_body'=>$comment,
                                              'comment_log'=>'FSA_Evaluation:'.$poams[$k].'=>'.$v,
                                              'comment_type'=>'EV_FSA');
                        $result = $db->insert('POAM_COMMENTS',$comment_data);

                    }
                    $result = $db->update('POAM_EVIDENCE',$data,'id = '.$id.'');
                    if('APPROVED' == $v){
                        $data = array('poam_status'=>'ES');
                        $result = $db->update('poams',$data,'id = '.$id.'');
                    }
                    break;
                case 'ev_ivv_evaluation':
                    $data = array('ev_ivv_evaluation'=>''.$v.'',
                                  'ev_date_ivv_evaluation'=>''.$now.'');
                    $result = $db->update('POAM_EVIDENCE',$data,'id = '.$id.'');
                    if('APPROVED' == $v){
                        $data = array('poam_status'=>'CLOSED',
                                      'poam_date_closed'=>''.$now.'');
                        $result = $db->update('poams',$data,'id = '.$id.'');
                        $data = array('FINDINGS.finding_status'=>'CLOSED',
                                      'FINDINGS.finding_date_closed'=>''.$now.'');
                        $result = $db->update('FINDINGS',$data,'poams.finding_id = FINDINGS.finding_id' AND
                                              'id = '.$id.'');
                    }
                    if('DENIED' == $v){
                        $data = array('poam_status'=>'EN','poam_action_date_actual'=>'NULL');
                        $result = $db->update('poams',$data,'id = '.$id.'');
                        $data = array('id'=>$id,'user_id'=>$user_id,'comment_date'=>$now,
                                      'ev_id'=>$ev_id,
                                      'comment_topic'=>'UPDATE:','comment_body'=>$comment,
                                      'comment_log'=>'IVV_Evaluation:'.$poams[$k].'=>'.$v,
                                      'comment_type'=>'EV_IVV');
                        $result = $db->insert('POAM_COMMENTS',$data);
                    }
                    break;
            }
          }
            }
            $data = array('modify_ts'=>$now,
                          'modified_by'  =>$user_id);
            $result = $db->update('poams',$data,'id = '.$id.'');
                        $now = time();
            foreach($_POST as $k=>$v){
                if(!in_array($k,array('id','comment_body','ev_id')) && !empty($v)){
                    $data = array('poam_id'=>$id,        
                                  'user_id'=>$user_id,
                                  'timestamp'   =>$now,
                                  'event'  =>'MODIFICATION',
                                  'description'=>'Original:'.$poams[$k].' New:'.$v.'');
                    $result = $db->insert('audit_logs',$data);
                }
            }
            $this->_helper->actionStack('view','Remediation',null,array('id'=>$id));
    }

    public function uploadevidenceAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $comment = $req->getParam('comment_body');
        assert($id);
        $today = date("Ymd",time());
        $user_id = $this->me->id;
        $now = date('Y-m-d,h:i:s',time());
        $db = Zend_Registry::get('db');
        if($_FILES && $id>0){
            if(!file_exists(ROOT . DS . 'public/evidence')){
                mkdir(ROOT . DS . 'public/evidence',0755);
            }
            if(!file_exists(ROOT . DS . 'public/evidence/'.$id)){
                mkdir(ROOT . DS . 'public/evidence/'.$id,0755);
            }
            $path = ROOT . DS . 'public/evidence/'.$id.'/'.date('Ymd-His-',time()).$_FILES['evidence']['name'];
            //$path_data = '../../public/evidence/'.$id.'/'.date('Ymd-His-',time()).$_FILES['evidence']['name'];
            $result_move = move_uploaded_file($_FILES['evidence']['tmp_name'],$path);
            if($result_move){
                chmod($path,0755);
            }
            else{
                die('Move upload file fail.'.$path.'');
            }
            $insert_data = array('id'          =>$id,
                                 'submission'    =>$path,
                                 'submitted_by'  =>$user_id,
                                 'date_submitted'=>$today);
            $result = $db->insert('evidences',$insert_data);
            $update_data = array('status'             => 'EP',
                                 'action_date_actual' => $now);
            $result = $db->update('poams',$update_data,'id = '.$id.'');
        }
        $this->_helper->actionStack('view','Remediation',null,array('id'=>$id));
    }
}
