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
    *         array('key' => 'value'). $fields follow the sytax of Zend_Db_Select with 
              exception of 'count'. Here 'count' is an keyword. For example, if 
              $fieldsa = array( 'count'=>'status' ), it means count and groupby status. The 
              returned value contains two fields 'count','status'.
              if $fields = array( 'count'=>'count(*)' ), it return the exact value of count.
              if $fields = array( 'count'=>'count(*)' , 'key' > 'value' , ... ) the count of the              search would be array_push into the returned variable;
    *  @param $criteria array 
    *  @param $limit integer results number.
    *  @param $pageno integer search start shift
    *  @return a list of record.
    */
    public function search($sys_ids, $fields = '*',$criteria=array(),$currentPage=null, $perPage=null){
        if(is_array($criteria)){
             extract($criteria);
        }

        $ret =array();
        $count_query = null;
        $to_count = false;
        $groupby = array();

        $db = $this->_db;

        $sid_str = implode(',',$sys_ids);
        $query = $db->select();
        if( $fields == '*' ) {
            $fields =  array('id'=>'p.id',
                          'legacy_id'=>'p.legacy_finding_id',
                          'system_id'=>'p.system_id',
                          'type'=>'p.type',
                          'status'=>'p.status',
                          'created_ts'=>'p.create_ts',
                          'action_est_date'=>'p.action_est_date');
        }else if( $fields == 'count' ){
            $to_count = true;
            $fields['count']='count(*)';
        }else{
            if( isset($fields['count']) ){
                $groupby = $fields['count'];
                unset($fields['count']);
            }
        }
        assert(is_array($fields));

        $query->from(array('p'=>$this->_name), $fields )
              ->where("p.system_id IN ($sid_str)");
    
        $query->joinLeft(array('s'=>'sources'),'p.source_id = s.id',array('source_nickname'=>'s.nickname',
                                                                         'source_name'    =>'s.name'));
        if(isset($source_id) && is_int($source_id)){
            $query->where("p.source_id = ".$source."");
        }
        
        if(!empty($ids)){
            $query->where("p.id IN ($ids)");
        }

        if(!empty($est_date_begin)){
            $query->where("p.action_est_date > ?",$est_date_begin->toString('Y-m-d'));
        }

        if(!empty($est_date_end)){
            $query->where("p.action_est_date <= ?",$est_date_end->toString('Y-m-d'));
        }

        if(!empty($created_date_begin) ){
            $query->where("p.create_ts > ?",$created_date_begin->toString('Y-m-d')); 
        }

        if(!empty($created_date_end) ){
            $query->where("p.create_ts <=?",$created_date_end->toString('Y-m-d'));
        }

        if(!empty($discovered_date_begin) ){
            $query->where("p.discover_ts > ?",$discovered_date_begin->toString('Y-m-d')); 
        }

        if(!empty($discovered_date_end) ){
            $query->where("p.discover_ts <=?",$discovered_date_end->toString('Y-m-d'));
        }


        if(isset($type) && $type != 'any'){
            if(is_array($type)){
                $type = implode("','",$type);
                $query->where("p.type IN (".makeSqlInStmt($type).")");
            } else {
                $query->where("p.type = ?",$type);
            }
        }

        if( !empty($status) ){
            if(is_array($status)){
                $query->where( "p.status IN (".makeSqlInStmt($status).")" );
            } else {
                $query->where("p.status = ?", $status);
            }
        }
        /*
        if(isset($ep)){
            $qry = $db->select()->distinct()->from(array('e'=>'evidences'),array('id'=>'e.id'))
                                            ->where("pe.ev_sso_evaluation = '".$ep['sso']."'")
                                            ->where("pe.ev_fsa_evaluation = '".$ep['fsa']."'")
                                            ->where("pe.ev_ivv_evaluation = '".$ep['ivv']."'");
            
            $ids = implode(',',$db->fetchCol($qry));
            $query->where("p.id IN ($ids)");
        }
        */

        if(isset($asset_owner) && $asset_owner != 'any'){
            $query->where("sys.id = ".$asset_owner."");
        }
        
        if(!empty($date_modified)){
            assert($date_modified instanceof Zend_Date);
            $query->where("p.modify_ts < $date_modified->toString('Y-m-d')");
        }

        if( $to_count ) {
            $query->join(array('as'=>'assets'), 'as.id = p.asset_id', array());
        }else{
            $query->join(array('as'=>'assets'), 'as.id = p.asset_id', 
                array('ip'=>'as.address_ip', 'port'=>'as.address_port', 'network_id'=>'as.network_id'));
            if( $groupby == 'count(*)' ){
                $count_query = clone $query;
                $count_query->reset(Zend_Db_Select::COLUMNS);
                $count_query->reset(Zend_Db_Select::FROM);
                $count_query->from( array('p'=>$this->_name),array('count'=>$groupby) );
                $count_query->join(array('as'=>'assets'), 'as.id = p.asset_id',array());
            }else{
                $query->reset(Zend_Db_Select::COLUMNS);
                $query->reset(Zend_Db_Select::FROM);
                $query->from( array('p'=>$this->_name),array('count'=>'count(*)',$groupby) )
                      ->join(array('as'=>'assets'), 'as.id = p.asset_id',array())
                      ->group("p.$groupby");
            }
        }

        if(!empty($ip)) {
            $query->where('as.address_ip = ?', $ip);
            if( !empty($count_query) ) {
                $count_query->where('as.address_ip = ?', $ip);
            }
        }
        if(!empty($port)) {
            $query->where('as.address_port = ?', $port);
            if( !empty($count_query) ) {
                $count_query->where('as.address_port = ?', $port);
            }
        }

        if( !empty( $currentPage ) && !empty( $perPage ) ){
            $query->limitPage($currentPage,$perPage);
        }
        if($to_count) {
            $ret = $db->fetchOne($query);
        }else{
            $ret = $db->fetchAll($query);
            if( !empty($count_query) ) {
                $total = $db->fetchOne($count_query);
                array_push($ret, $total);
            }
        }
        return $ret;
    }

    /** 
        Get detail information of a remediation by Id

        @param $id int primary key of poam
    */
    public function getDetail($id)
    {
        assert($id);
        $db = $this->_db;
        $query = $this->select()->setIntegrityCheck(false)->from(array('p'=>$this->_name))
            ->where('p.id=?',$id)
            ->joinLeft(array('s'=>'sources'),'source_id = s.id',
                    array('source_nickname'=>'s.nickname', 'source_name'=>'s.name'))
            ->join(array('as'=>'assets'), 'as.id = p.asset_id', 
                array('asset_name'=>'as.name',
                      'ip'=>'as.address_ip', 
                      'port'=>'as.address_port', 
                      'network_id'=>'as.network_id'))
            ->join(array('net'=>'networks'), 'net.id = as.network_id',
                    array('network_name'=>'net.name') );
        return $db->fetchRow($query);
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
                      ->where("p.action_est_date <= '".$enddate."'")
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
                      ->where("p.action_est_date > '".$enddate."'")
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
                $query->where("p.action_est_date <= '".$enddate."'")
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
