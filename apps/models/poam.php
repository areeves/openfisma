<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class poam extends Zend_Db_Table
{
    protected $_name = 'poams';
    protected $_primary = 'id';
    
    /** 
    *  search poam records.
    *  @param $sys_ids array system id that limit the searching agency
    *  @param $fields array information contained in the return.
    *         array('key' => 'value'). key is the alias while the value in the set of 
    *          [*, count, id, type, status, created_t, est_t, system_id, system_name, source_id, source_name]
    *  @param $criteria array 
    *  @param $limit integer results number.
    *  @param $pageno integer search start shift
    *  @return a list of record.
    */
    public function search($sys_ids, $fields = '*',$criteria=array(),$currentPage=null, $perPage=null){
        if(is_array($criteria)){
             extract($criteria);
        }

        $groupby = array();

        $db = $this->_db;
        $today = date('Ymd',time());

        $sid_str = implode(',',$sys_ids);
        $query = $db->select();
        if( $fields == '*' ) {
                $fields =  array('id'=>'p.id',
                               'legacy_id'=>'p.legacy_finding_id',
                               'type'=>'p.type',
                               'status'=>'p.status',
                               'date_created'=>'p.create_ts',
                               'action_date_est'=>'p.action_date_est');
                $query->from(array('p'=>'poams'), $fields )
                     ->where("p.system_id IN ($sid_str)")
                     //->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array('finding_data'=>'f.finding_data'))
                     //->joinLeft(array('fs'=>'FINDING_SOURCES'),'f.source_id = fs.source_id',
                     //                                            array('source_nickname'=>'fs.source_nickname',
                     //                                           'source_name'=>'fs.source_name'))
                     ->join(array('s'=>'sources'),'p.source_id = s.id',array('source_nickname'=>'s.nickname',
                                                                             'source_name'    =>'s.name'))
                     ->join(array('sys'=>'systems'),'sys.id = p.system_id',array('action_owner_nickname'=>'sys.nickname'));
        }else{
            assert(is_array($fields));
            if( isset($fields['count']) ){
                $groupby = $fields['count'];
                $fields = 'count(*) as count';
                assert(is_array($groupby));
                $query->from(array('p'=>'poams'), $fields )
                     ->where("p.system_id IN ($sid_str)")
                     //->join(array('f'=>'findings'),'p.finding_id = f.finding_id',array())
                     ->join(array('s'=>'sources'),'p.source_id = s.id', array() )
                     ->join(array('sys'=>'systems'),'sys.id = p.system_id',array());
                foreach($groupby as $g){
                    $query->group("p.$g");
                }
            }
        }


        
        if(isset($ids) && !empty($ids)){
            $query->where("p.id IN ($ids)");
        }

        if(isset($source) && $source != 'any'){
            $query->where("p.source_id = ".$source."");
        }

        if(!empty($startdate)){
            $startdate = date("Y-m-d",strtotime($startdate));
            $query->where("p.action_date_est >=?",$startdate);
        }

        if(!empty($enddate)){
            $enddate = date("Y-m-d",strtotime($enddate));
            $query->where("p.action_date_est <=?",$enddate);
        }

        if(!empty($startcreatedate) ){
            $start_date_cr = date("Y-m-d",strtotime($startcreatedate));
            $query->where("p.create_ts >=?",$startcreatedate); 
        }

        if(!empty($end_date_cr) ){
            $end_date_cr = date("Y-m-d",strtotime($end_date_cr));
            $query->where("p.create_ts <=?",$end_date_cr);
        }

        if(isset($type) && $type != 'any'){
            if(is_array($type)){
                $type = implode(',',$type);
                $query->where("p.type IN ($type)");
            }
            else {
                $query->where("p.type = ?",$type);
            }
        }

        if(isset($status) && $status != 'any' ){
            if(is_array($status)){
                $status = implode(',',$status);
                $query->where("p.status IN ($status)");
            }
            else {
                $query->where("p.status = ?", $status);
            }
        }
        
        if(isset($ep)){
            $qry = $db->select()->distinct()->from(array('e'=>'evidences'),array('id'=>'e.id'))
                                            ->where("pe.ev_sso_evaluation = '".$ep['sso']."'")
                                            ->where("pe.ev_fsa_evaluation = '".$ep['fsa']."'")
                                            ->where("pe.ev_ivv_evaluation = '".$ep['ivv']."'");
            
            $ids = implode(',',$db->fetchCol($qry));
            $query->where("p.id IN ($ids)");
        }

        if(isset($asset_owner) && $asset_owner != 'any'){
            $query->where("sys.id = ".$asset_owner."");
        }
        
        if(isset($action_owner) && $action_owner != 'any'){
            $query->where("sys.id = ".$action_owner."");
        }

        if(!empty($date_modified)){
            $query->where("p.modify_ts < $date_modified");
        }

        if( !empty( $currentPage ) && !empty( $perPage ) ){
            $query->limitPage($currentPage,$perPage);
        }
        //var_dump($db->fetchAll($query));die();
        //Should be order by ?
        //$query->order('action_owner_name ASC');
        return $db->fetchAll($query);
    }
   
    
   public function fismasearch($agency){
        $flag = substr($agency,0,1);
        $db = $this->_db;
        $fsa_sysgroup_id = Zend_Registry::get('fsa_sysgroup_id');
        $fp_system_id = Zend_Registry::get('fsa_system_id');
        $startdate = Zend_Registry::get('startdate');
        $enddate = Zend_Registry::get('enddate');
                $query = $db->select()->from(array('sgs'=>'system_group_systems'),array('system_id'=>'system_id'))
                              ->where("sgs.sysgroup_id = ".$fsa_sysgroup_id." AND sgs.system_id != ".$fp.system_id."");
        $result = $db->fetchCol($query);
        $system_ids = implode(',',$result);
        $query = $db->select()->distinct()
                              ->from(array('p'=>'poams'),array('num_poams'=>'count(p.id)'))
                              ->join(array('a'=>'assets'),'a.id = p.asset_id',array());
        switch($flag){
            case 'a':
                switch($agency){
                    case 'aaw':
                        $query->where("p.system_id = '".$fp.system_id."'");
                        break;
                    case 'as':
                        $query->where("p.system_id IN (".$system_ids.")");
                        break;
                }
                $query->where("p.create_ts < '".$startdate."'")
                      ->where("p.close_ts IS NULL OR p.close_ts >= '".$startdate."'");
                break;
            case 'b':
                switch($agency){
                    case 'baw':
                        $query->where("p.system_id = '".$fp.system_id."'");
                        break;
                    case 'bs':
                        $query->where("p.system_id IN (".$system_ids.")");
                        break;
                }
                $query->where("p.create_ts <= '".$enddate."'")
                      ->where("p.action_date_est <= '".$enddate."'")
                      ->where("p.action_date_actual >= '".$startdate."'")
                      ->where("p.action_date_actual <= '".$enddate."'");
                break;
            case 'c':
                switch($agency){
                    case 'caw':
                        $query->where("p.system_id = '".$fsa_system_id."'");
                        break;
                    case 'cs':
                        $query->where("p.system_id IN (".$system_ids.")");
                        break;
                }
                $query->where("p.create_ts <= '".$enddate."'")
                      ->where("p.action_date_est > '".$enddate."'")
                      ->where("p.action_date_actual IS NULL");
                break;
            case 'd':
                switch($agency){
                    case 'daw':
                        $query->where("p.system_id = '".$fsa_system_id."'");
                        break;
                    case 'ds':
                        $query->where("p.system_id IN (".$system_ids.")");
                        break;
                }
                $query->where("p.action_date_est <= '".$enddate."'")
                      ->where("p.action_date_actual IS NULL OR p.action_date_actual > '".$enddate."'");
                break;
            case 'e':
                switch($agency){
                    case 'eaw':
                        $query->where("p.system_id = '".$fsa_system_id."'");
                        break;
                    case 'es':
                        $query->where("p.system_id IN (".$system_ids.")");
                        break;
                }
                $query->where("p.create_ts >= '".$startdate."'")
                      ->where("p.create_ts <= '".$enddate."'");
                break;
            case 'f':
                switch($agency){
                    case 'faw':
                        $query->where("p.system_id = '".$fsa_system_id."'");
                        break;
                    case 'fs':
                        $query->where("p.system_id IN (".$system_ids.")");
                        break;
                }
                $query->where("p.create_ts <= '".$enddate."'")
                      ->where("p.close_ts IS NULL OR p.close_ts > '".$enddate."'");
                break;
        }
        $result = $db->fetchRow($query);
        return $result['num_poams'];
    }

}
?>
