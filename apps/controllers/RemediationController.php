<?php
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Jim Chen <xhorse@users.sourceforge.net>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 */

/**
 * The remediation controller handles CRUD for findings in remediation.
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 *
 * @todo As part of the ongoing refactoring, this class should probably be
 * merged with the FindingController.
 */
class RemediationController extends PoamBaseController
{
    //define the events of notification
    private $_notificationArray = array('action_suggested'=>Notification::UPDATE_FINDING_RECOMMENDATION,
                                       'type'=>Notification::UPDATE_COURSE_OF_ACTION,
                                       'action_planned'=>Notification::UPDATE_COURSE_OF_ACTION,
                                       'action_est_date'=>Notification::UPDATE_EST_COMPLETION_DATE,
                                       'threat_level'=>Notification::UPDATE_THREAT,
                                       'threat_source'=>Notification::UPDATE_THREAT,
                                       'threat_justification'=>Notification::UPDATE_THREAT,
                                       'cmeasure_effectiveness'=>Notification::UPDATE_COUNTERMEASURES,
                                       'cmeasure'=>Notification::UPDATE_COUNTERMEASURES,
                                       'cmeasure_justification'=>Notification::UPDATE_COUNTERMEASURES,
                                       'system_id'=>Notification::UPDATE_FINDING_ASSIGNMENT,
                                       'blscr_id'=>Notification::UPDATE_CONTROL_ASSIGNMENT,
                                       'action_status'=>Notification::MITIGATION_STRATEGY_APPROVED,
                                       'action_resources'=>Notification::UPDATE_FINDING_RESOURCES);
    /**
     *  Default action.
     *
     *  It combines the searching and summary into one page.
     */
    public function indexAction()
    {
        $this->_helper->actionStack('searchbox', 'Remediation');
        $this->_helper->actionStack('summary', 'Remediation');
    }
    /**
     *  Display the summary page of remediation, per systems.
     */
    public function summaryAction()
    {
        $criteria['system_id'] = $this->_request->getParam('system_id');
        $criteria['source_id'] = $this->_request->getParam('source_id');
        $criteria['type'] = $this->_request->getParam('type');
        $criteria['status'] = $this->_request->getParam('status');
        $criteria['ids'] = $this->_request->getParam('ids');
        $criteria['asset_owner'] = $this->_request->getParam('asset_owner', 0);

        $tmp = $this->_request->getParam('est_date_begin');
        if (!empty($tmp)) {
            $criteria['est_date_begin'] = new Zend_Date($tmp,
                Zend_Date::DATES);
        }
        $tmp = $this->_request->getParam('est_date_end');
        if (!empty($tmp)) {
            $criteria['est_date_end'] = new Zend_Date($tmp, Zend_Date::DATES);
        }
        $tmp = $this->_request->getParam('created_date_begin');
        if (!empty($tmp)) {
            $criteria['created_date_begin'] = new Zend_Date($tmp,
                Zend_Date::DATES);
        }
        $tmp = $this->_request->getParam('created_date_end');
        if (!empty($tmp)) {
            $criteria['created_date_end'] = new Zend_Date($tmp,
                Zend_Date::DATES);
        }

        $today = parent::$now->toString('Ymd');
        $summary_tmp = array(
            'NEW' => 0,
            'OPEN' => 0,
            'EN' => 0,
            'EO' => 0,
            'EP' => 0,
            'EP_SNP' => 0,
            'EP_SSO' => 0,
            'ES' => 0,
            'CLOSED' => 0,
            'TOTAL' => 0
        );

        if ( !empty($criteria['system_id']) ) {
            $sum = array('0' => $summary_tmp);
            $summary = array($criteria['system_id'] => $summary_tmp);
        } else {
            // mock array_fill_key in 5.2.0
            $count = count($this->_me->systems);
            $sum = array_fill(0, $count, $summary_tmp);
            $summary = array_combine($this->_me->systems, $sum);
        }
        $total = $summary_tmp;
        $ret = $this->_poam->search($this->_me->systems, array(
            'count' => array(
                'status',
                'system_id'
            ) ,
            'status',
            'type',
            'system_id'
        ), $criteria);
        $sum = array();
        foreach($ret as $s) {
            $sum[$s['system_id']][$s['status']] = $s['count'];
        }
        foreach($sum as $id => & $s) {
            $summary[$id] = $summary_tmp;
            $summary[$id]['NEW'] = nullGet($s['NEW'], 0);
            $summary[$id]['OPEN'] = nullGet($s['OPEN'], 0);
            $summary[$id]['ES'] = nullGet($s['ES'], 0);
            //$summary[$id]['EN'] = nullGet($s['EN'],0);
            $summary[$id]['EP'] = nullGet($s['EP'], 0); //temp placeholder
            $summary[$id]['CLOSED'] = nullGet($s['CLOSED'], 0);
            $summary[$id]['TOTAL'] = array_sum($s);
            $total['NEW']+= $summary[$id]['NEW'];
            //$total['EN'] += $summary[$id]['EN'];
            $total['CLOSED']+= $summary[$id]['CLOSED'];
            $total['OPEN']+= $summary[$id]['OPEN'];
            $total['ES']+= $summary[$id]['ES'];
            $total['TOTAL']+= $summary[$id]['TOTAL'];
        }
        $eo_count = $this->_poam->search($this->_me->systems, array(
            'count' => 'system_id',
            'system_id'
        ) , array(
            'status' => 'EN',
            'est_date_end' => parent::$now
        ));
        foreach($eo_count as $eo) {
            $summary[$eo['system_id']]['EO'] = $eo['count'];
            $total['EO']+= $summary[$eo['system_id']]['EO'];
        }
        $en_count = $this->_poam->search($this->_me->systems, array(
            'count' => 'system_id',
            'system_id'
        ) , array(
            'status' => 'EN',
            'est_date_begin' => parent::$now
        ));
        foreach($en_count as $en) {
            $summary[$en['system_id']]['EN'] = $en['count'];
            $total['EN']+= $summary[$en['system_id']]['EN'];
        }
        $spsso = $this->_poam->search($this->_me->systems, array(
            'count' => 'system_id',
            'system_id'
        ) , array_merge(array(
            'ep' => 0
        ), $criteria));
        foreach($spsso as $sp) {
            $summary[$sp['system_id']]['EP_SSO'] = $sp['count'];
            $total['EP_SSO']+= $sp['count'];
        }
        $spsnp = $this->_poam->search($this->_me->systems, array(
            'count' => 'system_id',
            'system_id'
        ) , array_merge(array(
            'ep' => 1
        ), $criteria));
        foreach($spsnp as $sp) {
            $summary[$sp['system_id']]['EP_SNP'] = $sp['count'];
            $total['EP_SNP']+= $sp['count'];
        }
        $this->view->assign('total', $total);
        $this->view->assign('systems', $this->_system_list);
        $this->view->assign('summary', $summary);
        $this->render('summary');
        $this->_helper->actionStack('searchbox','Remediation', null, array('action'=>'summary'));
    }
    /**
     *  Do the real searching work. It's a thin wrapper of poam model's search method.
     */
    protected function _search($criteria)
    {
        //refer to searchbox.tpl for a complete status list
        $internal_crit = & $criteria;
        if (!empty($criteria['status'])) {
            $now = clone parent::$now;
            switch ($criteria['status']) {
            case 'NEW':
                $internal_crit['status'] = 'NEW';
                break;

            case 'OPEN':
                $internal_crit['status'] = 'OPEN';
                $internal_crit['type'] = array(
                    'CAP',
                    'FP',
                    'AR'
                );
                break;

            case 'EN':
                $internal_crit['status'] = 'EN';
                //Should we include EO status in?
                $internal_crit['est_date_begin'] = $now;
                break;

            case 'EO':
                $internal_crit['status'] = 'EN';
                $internal_crit['est_date_end'] = $now;
                break;

            case 'EP-SSO':
                ///@todo EP searching needed
                $internal_crit['status'] = 'EP';
                $internal_crit['ep'] = 0; //level
                break;

            case 'EP-SNP':
                $internal_crit['status'] = 'EP';
                $internal_crit['ep'] = 1; //level
                break;

            case 'ES':
                $internal_crit['status'] = 'ES';
                break;

            case 'CLOSED':
                $internal_crit['status'] = 'CLOSED';
                break;

            case 'NOT-CLOSED':
                $internal_crit['status'] = array(
                    'OPEN',
                    'EN',
                    'EP',
                    'ES'
                );
                break;

            case 'NOUP-30':
                $internal_crit['status'] = array(
                    'OPEN',
                    'EN',
                    'EP',
                    'ES'
                );
                $internal_crit['modify_ts'] = $now->sub(30, Zend_Date::DAY);
                break;

            case 'NOUP-60':
                $internal_crit['status'] = array(
                    'OPEN',
                    'EN',
                    'EP',
                    'ES'
                );
                $internal_crit['modify_ts'] = $now->sub(60, Zend_Date::DAY);
                break;

            case 'NOUP-90':
                $internal_crit['status'] = array(
                    'OPEN',
                    'EN',
                    'EP',
                    'ES'
                );
                $internal_crit['modify_ts'] = $now->sub(90, Zend_Date::DAY);
                break;
            }
        }
        $list = $this->_poam->search($this->_me->systems, array(
            'id',
            'source_id',
            'system_id',
            'type',
            'status',
            'finding_data',
            'action_est_date',
            'count' => 'count(*)'
        ) , $internal_crit, $this->_paging['currentPage'], $this->_paging['perPage']);
        $total = array_pop($list);
        $this->_paging['totalItems'] = $total;
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $lastSearch_url = str_replace('%d', $this->_paging['currentPage'], $this->_paging['fileName']);
        $urlNamespace = new Zend_Session_Namespace('urlNamespace');
        $urlNamespace->lastSearch = $lastSearch_url;
        $pager = & Pager::factory($this->_paging);
        $this->view->assign('list', $list);
        $this->view->assign('systems', $this->_system_list);
        $this->view->assign('sources', $this->_source_list);
        $this->view->assign('total_pages', $total);
        $this->view->assign('links', $pager->getLinks());
        $this->render('search');
    }
    public function searchboxAction()
    {
        $req = $this->getRequest();
        $this->_paging_base_path.= '/panel/remediation/sub/searchbox/s/search';
        // parse the params of search
        $criteria['system_id'] = $req->getParam('system_id');
        $criteria['source_id'] = $req->getParam('source_id');
        $criteria['type'] = $req->getParam('type');
        $criteria['status'] = $req->getParam('status');
        $criteria['ids'] = $req->getParam('ids');
        $criteria['asset_owner'] = $req->getParam('asset_owner', 0);
        $criteria['order'] = array();
        if ($req->getParam('sortby') != null && $req->getParam('order') != null) {
            array_push($criteria['order'], $req->getParam('sortby'));
            array_push($criteria['order'], $req->getParam('order'));
        }
        $tmp = $req->getParam('est_date_begin');
        if (!empty($tmp)) {
            $criteria['est_date_begin'] = new Zend_Date($tmp, Zend_Date::DATES);
        }
        $tmp = $req->getParam('est_date_end');
        if (!empty($tmp)) {
            $criteria['est_date_end'] = new Zend_Date($tmp, Zend_Date::DATES);
        }
        $tmp = $req->getParam('created_date_begin');
        if (!empty($tmp)) {
            $criteria['created_date_begin'] = new Zend_Date($tmp, Zend_Date::DATES);
        }
        $tmp = $req->getParam('created_date_end');
        if (!empty($tmp)) {
          $de =  $criteria['created_date_end'] = new Zend_Date($tmp);
        }

        if ('summary' == $this->_request->getParam('action')) {
            $postAction = "/panel/remediation/sub/summary";
        } else {
            $postAction = "/panel/remediation/sub/searchbox/s/search";
        }

        $this->makeUrl($criteria);
        $this->view->assign('url', $this->_paging_base_path);
        $this->view->assign('criteria', $criteria);
        $this->view->assign('systems', $this->_system_list);
        $this->view->assign('sources', $this->_source_list);
        $this->view->assign('postAction', $postAction);
        $this->render();
        if ('search' == $req->getParam('s')) {
            $this->_paging_base_path = $req->getBaseUrl() . '/panel/remediation/sub/searchbox/s/search';
            $this->_paging['currentPage'] = $req->getParam('p', 1);
            foreach($criteria as $key => $value) {
                if (!empty($value)) {
                    if ($value instanceof Zend_Date) {
                        $this->_paging_base_path.= '/' . $key . '/' . $value->toString('Ymd') . '';
                    } else {
                        $this->_paging_base_path.= '/' . $key . '/' . $value . '';
                    }
                }
            }
            $this->_search($criteria);
        }
    }
    /**
     Get remediation detail info
     *
     */
    public function viewAction()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $poam_detail = $this->_poam->getDetail($id);
        if (empty($poam_detail)) {
            throw new FismaException("POAM($id) is not found, Make sure a valid ID is inputed");
        }
        $ev_evaluation = $this->_poam->getEvEvaluation($id);
        // currently we don't need to support the comments for est_date change
        //$act_evaluation = $this->_poam->getActEvaluation($id);
        $evs = array();
        foreach($ev_evaluation as $ev_eval) {
            $evid = & $ev_eval['id'];
            if (!isset($evs[$evid]['ev'])) {
                $evs[$evid]['ev'] = array_slice($ev_eval, 0, 5);
            }
            $evs[$evid]['eval'][$ev_eval['eval_name']] = array_slice($ev_eval, 5);
        }
        $this->view->assign('poam', $poam_detail);
        $this->view->assign('logs', $this->_poam->getLogs($id));
        $this->view->assign('ev_evals', $evs);
        $this->view->assign('system_list', $this->_system_list);
        $this->view->assign('network_list',$this->_network_list);
        $this->render();
    }
    public function modifyAction()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $poam = $req->getPost('poam');
        if (!empty($poam)) {
            $oldpoam = $this->_poam->find($id)->toArray();
            if (empty($oldpoam)) {
                throw new FismaException('incorrect ID specified for poam');
            } else {
                $oldpoam = $oldpoam[0];
            }
            $where = $this->_poam->getAdapter()->quoteInto('id = ?', $id);
            $log_content = "Changed:";
            //@todo sanity check
            //@todo this should be encapsulated in a single transaction
            foreach($poam as $k => $v) {
                if ($k == 'type' && $oldpoam['status'] == 'NEW') {
                    assert(empty($poam['status']));
                    $poam['status'] = 'OPEN';
                    $poam['modify_ts'] = self::$now->toString('Y-m-d H:i:s');
                }
                if ($k == 'action_status' && $v == 'APPROVED') {
                    $poam['status'] = 'EN';
                } elseif ($k == 'action_status' && $v == 'DENIED') {
                    // If the SSO denies, then put back into OPEN status to make the POAM
                    // editable again.
                    $poam['status'] = 'OPEN';
                }
                ///@todo SSO can only approve the action after all the required info provided
            }
            $result = $this->_poam->update($poam, $where);
                        
            // Generate notifications and audit records if the update is
            // successful
            $notificationsSent = array();
            if( $result > 0 ) {
                foreach($poam as $k => $v) {
                    // We shouldn't send the same type of notification twice
                    // in one update. $notificationsSent is a set which
                    // tracks which notifications we have already created.
                    if (array_key_exists($k, $this->_notificationArray)
                        && !array_key_exists($this->_notificationArray[$k],
                                             $notificationsSent)) {
                        $this->_notification->add(
                            $this->_notificationArray[$k],
                            $this->_me->account,
                            "PoamID: $id",
                            nullGet($poam['system_id'], $oldpoam['system_id'])
                        );
                        $notificationsSent[$this->_notificationArray[$k]] = 1;
                    }

                    $log_content = "Update: $k\nOriginal: \"{$oldpoam[$k]}\" New: \"$v\"";
            	    $this->_poam->writeLogs($id, $this->_me->id, self::$now->toString('Y-m-d H:i:s'), 'MODIFICATION', $log_content);
                }
            }
        }
        //throw new Fisma_Excpection('POAM not updated for some reason');
        $this->_redirect('/panel/remediation/sub/view/id/' . $id);
    }
    public function uploadevidenceAction()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id');
        define('EVIDENCE_PATH', WEB_ROOT . DS . 'evidence');
        if ($_FILES && $id > 0) {
            $poam = $this->_poam->find($id)->toArray();
            if (empty($poam)) {
                throw new FismaException('incorrect ID specified for poam');
            } else {
                $poam = $poam[0];
            }
            
            $user_id = $this->_me->id;
            $now_str = self::$now->toString('Y-m-d-his');
            if (!file_exists(EVIDENCE_PATH)) {
                mkdir(EVIDENCE_PATH, 0755);
            }
            if (!file_exists(EVIDENCE_PATH . DS . $id)) {
                mkdir(EVIDENCE_PATH . DS . $id, 0755);
            }
            $count = 0;
            $filename = preg_replace('/^([^.]*)(\.[^.]*)?\.([^.]*)$/', '$1$2-' . $now_str . '.$3', $_FILES['evidence']['name'], 2, $count);
            $abs_file = EVIDENCE_PATH . DS . $id . DS . $filename;
            if ($count > 0) {
                $result_move = move_uploaded_file($_FILES['evidence']['tmp_name'], $abs_file);
                if ($result_move) {
                    chmod($abs_file, 0755);
                } else {
                    throw new FismaException('Failed in move_uploaded_file(). ' . $abs_file . $_FILES['evidence']['error']);
                }
            } else {
                throw new FismaException('The filename is not valid');
            }
            $today = substr($now_str, 0, 10);
            $data = array(
                'poam_id' => $id,
                'submission' => $filename,
                'submitted_by' => $user_id,
                'submit_ts' => $today
            );
            $db = Zend_Registry::get('db');
            $result = $db->insert('evidences', $data);
            $evidenceId = $db->LastInsertId();
            $this->_notification->add(
                Notification::EVIDENCE_APPROVAL_1ST,
                $this->_me->account,
                "PoamId: $id",
                $poam['system_id']
            );

            $update_data = array(
                'status' => 'EP',
                'action_actual_date' => $today
            );
            $result = $this->_poam->update($update_data, "id = $id");
            if ($result > 0) {
                $log_content = "Changed: status: EP . Upload evidence: $filename OK";
                $this->_poam->writeLogs($id, $user_id, self::$now->toString('Y-m-d H:i:s') , 'UPLOAD EVIDENCE', $log_content);
            }
        }
        $this->_redirect('/panel/remediation/sub/view/id/' . $id);
    }
    /**
     *  Handle the evidence evaluations
     */
    public function evidenceAction()
    {
        $req = $this->getRequest();
        $eval_id = $req->getParam('evaluation');
        $decision = $req->getParam('decision');
        $eid = $req->getParam('id');
        $ev = new Evidence();
        $ev_detail = $ev->find($eid);

        // Get the poam data because we need system_id to generate the
        // notification
        $poam = $this->_poam->find($ev_detail->current()->poam_id)->toArray();
        if (empty($poam)) {
            throw new FismaException('incorrect ID specified for poam');
        } else {
            $poam = $poam[0];
        }
        
        if (empty($ev_detail)) {
            throw new FismaException('Wrong evidence id:' . $eid);
        }
        if ($decision == 'APPROVE') {
            $decision = 'APPROVED';
        } else if ($decision == 'DENY') {
            $decision = 'DENIED';
        } else {
            throw new FismaException('Wrong decision:' . $decision);
        }
        $poam_id = $ev_detail->current()->poam_id;
        $log_content = "";
        if (in_array($decision, array(
            'APPROVED',
            'DENIED'
        ))) {
            $log_content = "";
            $evv_id = $this->_poam->reviewEv($eid, array(
                'decision' => $decision,
                'eval_id' => $eval_id,
                'user_id' => $this->_me->id,
                'date' => self::$now->toString('Y-m-d')
            ));
            if ( $eval_id == 1 ) {
                $this->_notification
                     ->add(Notification::EVIDENCE_APPROVAL_2ND,
                        $this->_me->account,
                        "PoamId: $poam_id",
                        $poam['system_id']);
            }

            $log_content.= " Decision: $decision.";
            if ($decision == 'DENIED') {
                $this->_poam->update(array(
                    'status' => 'EN'
                ) , 'id=' . $poam_id);
                $topic = $req->getParam('topic');
                $body = $req->getParam('reject');
                $comm = new Comments();
                $comm->insert(array(
                    'poam_evaluation_id' => $evv_id,
                    'user_id' => $this->_me->id,
                    'date' => 'CURDATE()',
                    'topic' => $topic,
                    'content' => $body
                ));
                $log_content.= " Status: EN. Topic: $topic. Content: $body.";
                $this->_notification
                     ->add(Notification::EVIDENCE_DENIED,
                        $this->_me->account,
                        "PoamId: $poam_id",
                        $poam['system_id']);
            }
            if ($decision == 'APPROVED' && $eval_id == 2) {
                $log_content.= " Status: ES";
                $this->_poam->update(array(
                    'status' => 'ES'
                ) , 'id=' . $poam_id);

                $this->_notification
                     ->add(Notification::EVIDENCE_APPROVAL_3RD,
                        $this->_me->account,
                        "PoamId: $poam_id",
                        $poam['system_id']);
            }
            if ($decision == 'APPROVED' && $eval_id == 3) {
                $log_content.= " Status: CLOSED";
                $this->_poam->update(array(
                    'status' => 'CLOSED'
                ) , 'id=' . $poam_id);
            
                $this->_notification
                     ->add(Notification::POAM_CLOSED,
                        $this->_me->account,
                        "PoamId: $poam_id",
                        $poam['system_id']);
            }
            if (!empty($log_content)) {
                $log_content = "Changed: $log_content";
                $this->_poam->writeLogs($poam_id, $this->_me->id, self::$now->toString('Y-m-d H:i:s') , 'EVIDENCE EVALUATION', $log_content);
            }
        }
        $this->_redirect('/panel/remediation/sub/view/id/' . $poam_id, array(
            'exit'
        ));
    }
    /**
     *  Generate RAF report
     *
     *  It can handle different format of RAF report.
     */
    public function rafAction()
    {
        $id = $this->_req->getParam('id');
        $this->_helper->layout->disableLayout(true);
        $this->_helper->contextSwitch()->addContext('pdf', array(
            'suffix' => 'pdf',
            'headers' => array(
                'Content-Disposition' => "attachement;filename=\"{$id}_raf.pdf\"",
                'Content-Type' => 'application/pdf'
            )
        ))->addActionContext('raf', array(
            'pdf'
        ))->initContext();
        $poam_detail = $this->_poam->getDetail($id);
        if (empty($poam_detail)) {
            throw new FismaException("Not able to get details for this POAM ID ($id)");
        }
        $this->view->assign('poam', $poam_detail);
        $this->view->assign('system_list', $this->_system_list);
        $this->view->assign('source_list', $this->_source_list);
        $this->render();
    }
}
