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
    

    protected function _parseWhere($query,$where)
    {
        assert( $query instanceof Zend_Db_Select );
        if(is_array($where)){
             extract($where);
        }
        if(isset($source_id) && is_int($source_id)){
            $query->where("p.source_id = ".$source_id."");
        }
        
        if(!empty($ids)){
            $query->where("p.id IN (". makeSqlInStmt($ids) .")");
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

        if( !empty($type) ){
            if(is_array($type)){
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
        if( !empty($ip) || !empty($port) ){
            if(!empty($ip)) {
                $query->where('as.address_ip = ?', $ip);
            }
            if(!empty($port)) {
                $query->where('as.address_port = ?', $port);
            }
        }

        if( !empty($group) ){
            $query->group($group);
        }

        if(!empty($date_modified)){
            assert($date_modified instanceof Zend_Date);
            $query->where("p.modify_ts < $date_modified->toString('Y-m-d')");
        }

        return $query;
    }

    /** 
    *  search poam records.
    *  @param $sys_ids array system id that limit the searching agency
    *  @param $fields array information contained in the return.
    *         array('key' => 'value'). $fields follow the sytax of Zend_Db_Select with 
              exception of 'count'. Here 'count' is an keyword. There are 3 cases for fields: 
              1.  $fields = array( 'count'=>'status' ...)
                  It means count and groupby status. The returned value contains 'count'... and more

              2.  $fields = array( 'count'=>'count(*)' )
                  It return the exact value of count.

              3.  $fields = array( 'count'=>'count(*)' , 'key'=> 'value' , ... ) 
                  the count of the result is array_push into the returned variable;
    *  @param $criteria array 
    *  @param $limit integer results number.
    *  @param $pageno integer search start shift
    *  @return a list of record.
    */
    public function search($sys_ids, $fields = '*',$criteria=array(),$currentPage=null, $perPage=null)
    {
        static $EXTRA_FIELDS = array( 'asset'=>array('as.address_ip'=>'ip',
                                                     'as.address_port'=>'port'),
                                    'source'=>array('s.nickname'=>'source_nickname',
                                                    's.name'=>'source_name'  ));
        $ret =array();
        $count = 0;

        if( $fields == '*' ) {
            $fields = $this->_cols;
        }else if( isset($fields['count']) ) {
            $count_fields = true;
            if( $fields =='count' || $fields == array('count'=>'count(*)') ) {
                $fields = array(); //count only
            }else{
                if( $fields['count'] != 'count(*)' ) {
                    $count_fields = false;
                    $criteria['group'] = $fields['count'];
                    $fields['count'] = 'count(*)';
                }else{
                    //array_push count
                    unset($fields['count']);
                }
            }
        }
        assert(is_array($fields));
        $table_fields = array_values($fields);

        $p_fields = array_diff($fields, $EXTRA_FIELDS['asset'], $EXTRA_FIELDS['source'] );
        $as_fields = array_flip(array_intersect( $EXTRA_FIELDS['asset'],
                                                 $table_fields) );
        $src_fields = array_flip(array_intersect( $EXTRA_FIELDS['source'],
                                                 $table_fields) );

        $query = $this->_db->select()
                           ->from(array('p'=>$this->_name), $p_fields)
                           ->where("p.system_id IN (".makeSqlInStmt($sys_ids).")");
        
        if( !empty($as_fields) ) {
            $query->join( array('as'=>'assets'), 'as.id = p.asset_id',$as_fields);
        }
        if( !empty($src_fields) ) {
            $query->joinLeft( array('s'=>'sources'), 's.id = p.source_id',$src_fields);
        }
        $query = $this->_parseWhere($query, $criteria);

        if( $count_fields ) {
            $count_query = clone $query;
            $count_query->reset(Zend_Db_Select::COLUMNS);
            $count_query->reset(Zend_Db_Select::FROM);
            $count_query->reset(Zend_Db_Select::GROUP);
            $count_query->from( array('p'=>$this->_name),array('count'=>'count(*)') );
            $count = $this->_db->fetchOne($count_query);
            if( empty($p_fields) ) {
                return $count;
            }
        }

        if( !empty( $currentPage ) && !empty( $perPage ) ){
            $query->limitPage($currentPage,$perPage);
        }
        $ret = $this->_db->fetchAll($query);
        if( $count_fields && $count ) {
            array_push($ret, $count);
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
