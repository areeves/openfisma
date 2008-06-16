<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'PoamBaseController.php';
require_once MODELS . DS . 'user.php';
require_once MODELS . DS . 'evaluation.php';
require_once APPS . DS . 'Exception.php';
require_once 'Pager.php';

class RemediationController extends PoamBaseController
{

    public function indexAction(){
        $this->_helper->actionStack('searchbox','Remediation');
        $this->_helper->actionStack('summary','Remediation');
    }

    public function summaryAction(){
        require_once MODELS . DS . 'system.php';
        $req = $this->getRequest();

        $today = parent::$now->toString('Ymd');

        $summary_tmp = array('NEW'=>0,'OPEN'=>0,'EN'=>0,'EO'=>0,'EP'=>0,'EP_SNP'=>0,'EP_SSO'=>0,'ES'=>0,'CLOSED'=>0,'TOTAL'=>0);

        // mock array_fill_key in 5.2.0 
        $count = count($this->me->systems);
        $sum = array_fill(0,$count,$summary_tmp);
        $summary = array_combine($this->me->systems, $sum);

        $total = $summary_tmp;

        $ret = $this->_poam->search($this->me->systems,
                        array('count'=>array('status','system_id'), 'status','system_id'));
        $sum =array();
        foreach($ret as $s) {
            $sum[$s['system_id']][$s['status']] = $s['count'];
        }
        foreach($sum as $id=>&$s) {
            $summary[$id] = $summary_tmp;
            $summary[$id]['NEW'] = nullGet($s['NEW'],0);
            $summary[$id]['OPEN'] = nullGet($s['OPEN'],0);
            $summary[$id]['ES'] = nullGet($s['ES'],0);
            $summary[$id]['EN'] = nullGet($s['EN'],0);
            $summary[$id]['EP_SSO'] = nullGet($s['EP'],0); //temp placeholder
            $summary[$id]['CLOSED'] = nullGet($s['CLOSED'],0);
            $summary[$id]['TOTAL'] = array_sum($s);
            $total['NEW'] += $summary[$id]['NEW'];
            $total['CLOSED'] += $summary[$id]['CLOSED'];
            $total['OPEN'] += $summary[$id]['OPEN'];
            $total['ES'] += $summary[$id]['ES'];
            $total['TOTAL'] += $summary[$id]['TOTAL'];
        }

        $eo_count = $this->_poam->search($this->me->systems, 
                        array('count'=>'system_id','system_id'),
                        array(
                              'status'=>'EN',
                              'est_date_end'=> parent::$now )
                        );
        foreach($eo_count as $eo ) {
            $summary[$eo['system_id']]['EO'] = $eo['count'];
        }

        $list = $this->_poam->search($this->me->systems, 
                        array('id','system_id'), array('status'=>'EP'));
        
        foreach( $list as $r ) {
            $ep_list[$r['id']] = $r['system_id'];
        }
        $ret = $this->_poam->getEvEvaluation(array_keys($ep_list),true);
        foreach( $ret as $k=>$e ) {
            if( isset($e['decision']) && $e['decision'] == 'APPROVED' ){
                $summary[$ep_list[$e['poam_id']]]['EP_SNP']++;
                $summary[$ep_list[$e['poam_id']]]['EP_SSO']--;
            }
        }

        $this->view->assign('total',$total);
        $this->view->assign('systems',$this->_system_list);
        $this->view->assign('summary',$summary );
        $this->render('summary');
    }

    protected function _search($criteria){
        //refer to searchbox.tpl for a complete status list
        $internal_crit = &$criteria;
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
                    $internal_crit['est_date_begin'] = $now;
                    break;
                case 'EO':
                    $internal_crit['status'] = 'EN';                        
                    $internal_crit['est_date_end'] = $now;
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
                    $internal_crit['status'] = array('OPEN','EN','EP','ES');
                    break;
                case 'NOUP-30':
                    $internal_crit['status'] = array('OPEN','EN','EP','ES');
                    $internal_crit['modify_ts'] = $now->sub(30, Zend_Date::DAY);
                    break;
                case 'NOUP-60':
                    $internal_crit['status'] = array('OPEN','EN','EP','ES');
                    $internal_crit['modify_ts'] = $now->sub(60, Zend_Date::DAY);
                    break;
                case 'NOUP-90':
                    $internal_crit['status'] = array('OPEN','EN','EP','ES');
                    $internal_crit['modify_ts'] = $now->sub(90, Zend_Date::DAY);
                    break;
            }
        }

        $list = $this->_poam->search($this->me->systems, array('id',
                                                         'source_id',
                                                         'system_id',
                                                         'type',
                                                         'status',
                                                         'finding_data',
                                                         'action_est_date',
                                                         'count'=>'count(*)'),
                                     $internal_crit,$this->_paging['currentPage'],
                                     $this->_paging['perPage']);
        $total = array_pop($list);

        $this->_paging['totalItems'] = $total;
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $pager = &Pager::factory($this->_paging);
        $this->view->assign('list',$list);
        $this->view->assign('systems',$this->_system_list);
        $this->view->assign('sources',$this->_source_list);
        $this->view->assign('total_pages',$total);
        $this->view->assign('links',$pager->getLinks());
        $this->render('search');
    }

    public function searchboxAction()
    {
        $req = $this->getRequest();
        $this->_paging_base_path .= '/panel/remediation/sub/searchbox/s/search';
        // parse the params of search
        $criteria['system_id'] = $req->getParam('system_id');
        $criteria['source_id'] = $req->getParam('source_id');
        $criteria['type'] = $req->getParam('type');
        $criteria['status'] = $req->getParam('status');
        $criteria['ids'] = $req->getParam('ids');
        $criteria['asset_owner'] = $req->getParam('asset_owner',0);
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

        $this->view->assign('criteria',$criteria);
        $this->view->assign('systems',$this->_system_list);
        $this->view->assign('sources',$this->_source_list);
        $this->render();
        if('search' == $req->getParam('s')){
            $this->_paging_base_path = $req->getBaseUrl().'/panel/remediation/sub/searchbox/s/search';
            $this->_paging['currentPage'] = $req->getParam('p',1);
           
            foreach($criteria as $key=>$value){
                if(!empty($value) ){
                    $this->_paging_base_path .= '/'.$key.'/'.$value.'';
                }
            }    
            $this->_search($criteria);
        }
    }

    /**
        Get remediation detail info
    **/
    public function viewAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $poam_detail = $this->_poam->getDetail($id);

        if(empty($poam_detail)){
            throw new fisma_Expection("POAM($id) is not found, Make sure a valid ID is inputed");
        }

        $ev_evaluation = $this->_poam->getEvEvaluation($id);
        // currently we don't need to support the comments for est_date change
        //$act_evaluation = $this->_poam->getActEvaluation($id);
        $evs = array();
        foreach($ev_evaluation as $ev_eval){
            $evid = &$ev_eval['id'];
            if( !isset($evs[$evid]['ev']) ){
                $evs[$evid]['ev'] = array_slice($ev_eval,0,5);
            }
            $evs[$evid]['eval'][$ev_eval['level']] = array_slice($ev_eval,5);
        }


/*
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
                $r['action_planned'], $r['action_resources'], $r['blscr_id']);
                $r_fields_zero = array($r['action_est_date']);
                $r_fields_none = array($r['cmeasure_effectiveness'], $r['threat_level']);
                $is_completed = (in_array(null, $r_fields_null) || in_array('NONE', $r_fields_none) || in_array('0000-00-00', $r_fields_zero))?'no':'yes';
                $this->view->assign('is_completed', $is_completed);
            }
        
        
            $this->view->assign('remediation_id',$id);
        }*/
        $this->view->assign('poam',$poam_detail);
        $this->view->assign('logs',$this->_poam->getLogs($id));
        $this->view->assign('ev_evals',$evs);
        $this->view->assign('system_list',$this->_system_list);
        $this->render();
    }

    public function modifyAction(){
        $req = $this->getRequest();
        $post = $req->getPost();
        $id = $req->getParam('id');
        $comment = $req->getParam('comment_body');
        $ev_id   = $req->getParam('ev_id');
        $eval_id = $req->getParam('eval_id');
        assert($id);
        $today = date("Ymd",time());
        $user_id = $this->me->id;
        $now = date('Y-m-d,h:i:s',time());
        $poam = new poam();
        $db = $poam->getAdapter();
        $query = $db->select()->from(array('p'=>'poams'),'*');
        if(!empty($eval_id)){
            $query->joinLeft(array('ev'=>'evidences'),'ev.poam_id = p.id',array())
                  ->joinLeft(array('eval'=>'ev_evaluations'),'eval.ev_id = ev.id and 
                                   eval.id = '.$eval_id,
                                   array('decision'=>'decision'));
        }
        $query->where("p.id = $id");
        $poams = $db->fetchRow($query);
        foreach($post as $k=>$v){
          if(!empty($v)){
            switch($k){
                case 'blscr':
                    $data = array('blscr_id'=>$v);
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'type':
                    $data = array('type'=>$v,
                                  'status'=>'OPEN',
                                  'modify_ts'=>''.$now.'',
                                  'action_planned'=>'null',
                                  'action_est_date'=>'null',
                                  'action_actual_date'=>'null',
                                  'action_resources'=>'null',
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'action_planned':
                    $data = array('action_planned'=>$v,
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'action_est_date':
                    $data = array('action_est_date'=>$v,
                                  'action_status'  =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'action_status':
                    $data = array('action_status' =>$v);
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    if('APPROVED' == $v){
                        $db->update('poams',array('status'=>'EN'),'id = '.$id.'');
                    } else {
                        $db->update('poams',array('status'=>'OPEN'),'id = '.$id.'');
                    }
                    break;
                case 'action_suggested':
                    $data = array('action_suggested'=>$v,
                                  'action_status'   =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'action_owner':
                    $data = array('system_id'=>$v);
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;                                                               
                case 'action_resources':
                    $data = array('action_resources'=>$v);
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'cmeasure_effectiveness':
                    $data = array('cmeasure_effectiveness'=>$v,
                                  'action_status'         =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'cmeasure':
                    $data = array('cmeasure'      =>$v,
                                  'action_status' =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'cmeasure_justification':
                    $data = array('cmeasure_justification'=>$v,
                                  'action_status'         =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'threat_level':
                    $data = array('threat_level' =>$v,
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'threat_source':
                    $data = array('threat_source'=>$v,
                                  'action_status'=>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'threat_justification':
                    $data = array('threat_justification'=>$v,
                                  'action_status'       =>'NONE');
                    $result = $db->update('poams',$data,'id = '.$id.'');
                    break;
                case 'EV_SSO':
                    $poams['EV_SSO'] = $poams['decision'];
                    $data = array('ev_id'=>$ev_id,'eval_id'=>$eval_id,
                                  'user_id'=>$user_id,'decision'=>$v);
                    $result = $db->insert('ev_evaluations',$data);
                    $ev_evaluation_id = $db->lastInsertId();
                    if('DENIED' == $v ){
                        $data = array('status'=>'EN','action_actual_date'=>'NULL');
                        $result = $db->update('poams',$data,'id = '.$id);
                        $data = array('EV_FSA'=>'EXCLUDED','EV_IVV'=>'EXCLUDED');
                        $comment_data = array('user_id'=>$user_id,
                                              'date'=>$now,
                                              'ev_evaluation_id'=>$ev_evaluation_id,
                                              'content'=>$comment,
                                              'topic'=>'SSO_Evaluation:'.$poams[$k].'=>'.$v);
                        $result = $db->insert('comments',$comment_data);
                        
                    }
                    break;
                case 'EV_FSA':
                    $poams['EV_FSA'] = $poams['decision'];
                    $data = array('ev_id'=>$ev_id,
                                  'eval_id'=>$eval_id,
                                  'user_id'=>$user_id,
                                  'decision'=>$v);
                    $result = $db->insert('ev_evaluations',$data);
                    $ev_evaluation_id = $db->lastInsertId();
                    if('DENIED' == $v ){
                        $data = array('status'=>'EN','action_actual_date'=>'NULL');
                        $result = $db->update('poams',$data,'id = '.$id);
                        $comment_data = array('user_id'=>$user_id,
                                              'date'=>$now,
                                              'ev_evaluation_id'=>$ev_evaluation_id,
                                              'content'=>$comment,
                                              'topic'=>'FSA_Evaluation:'.$poams[$k].'=>'.$v);
                        $result = $db->insert('comments',$comment_data);

                    }
                    if('APPROVED' == $v){
                        $data = array('status'=>'ES');
                        $result = $db->update('poams',$data,'id = '.$id);
                    }
                    break;
                case 'EV_IVV':
                    $poams['EV_IVV'] = $poams['decision'];
                    $data = array('ev_id'=>$ev_id,'eval_id'=>$eval_id,
                                  'user_id'=>$user_id,'decision'=>$v);
                    $result = $db->insert('ev_evaluations',$data);
                    $ev_evaluation_id = $db->lastInsertId();
                    if('APPROVED' == $v){
                        $data = array('status'=>'CLOSED',
                                      'close_ts'=>$now);
                        $result = $db->update('poams',$data,'id = '.$id.'');
                    }
                    if('DENIED' == $v){
                        $data = array('status'=>'EN','action_actual_date'=>'NULL');
                        $result = $db->update('poams',$data,'id = '.$id.'');
                        $data = array('user_id'=>$user_id,
                                      'date'=>$now,
                                      'ev_evaluation_id'=>$ev_evaluation_id,
                                      'content'=>$comment,
                                      'topic'=>'IVV_Evaluation:'.$poams[$k].'=>'.$v);
                        $result = $db->insert('comments',$data);
                    }
                    break;
            }
          }
            }
            $data = array('modify_ts'=>$now,
                          'modified_by'  =>$user_id);
            $result = $db->update('poams',$data,'id = '.$id);
            $now = time();
            foreach($_POST as $k=>$v){
                if(!in_array($k,array('id','comment_body','ev_id','eval_id')) && !empty($v)){
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
            $insert_data = array('poam_id'          =>$id,
                                 'submission'    =>$path,
                                 'submitted_by'  =>$user_id,
                                 'submit_ts'=>$today);
            $result = $db->insert('evidences',$insert_data);
            $update_data = array('status'             => 'EP',
                                 'action_actual_date' => $now);
            $result = $db->update('poams',$update_data,'id = '.$id.'');
        }
        $this->_helper->actionStack('view','Remediation',null,array('id'=>$id));
    }
}
