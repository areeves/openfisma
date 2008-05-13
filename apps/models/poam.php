<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $ID$
*/

require_once 'Zend/Db/Table.php';

class poam extends Zend_Db_Table
{
    protected $_name = 'POAMS';
    protected $_primary = 'poam_id';
      
    /** 
    *  search poam records.
    *  @param $sys_ids array system id that limit the searching range
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
                $fields =  array('id'=>'p.poam_id',
                               'legacy_id'=>'p.legacy_poam_id',
                               'type'=>'p.poam_type',
                               'status'=>'p.poam_status',
                               'date_created'=>'p.poam_date_created',
                               'action_date_est'=>'p.poam_action_date_est');
                $query->from(array('p'=>'POAMS'), $fields )
                     ->where("p.poam_action_owner IN ($sid_str)")
                     ->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array('finding_data'=>'f.finding_data'))
                     ->joinLeft(array('fs'=>'FINDING_SOURCES'),'f.source_id = fs.source_id',
                                                                 array('source_nickname'=>'fs.source_nickname',
                                                                'source_name'=>'fs.source_name'))
                     ->join(array('s'=>'SYSTEMS'),'s.system_id = p.poam_action_owner',array('action_owner_nickname'=>'s.system_nickname'));
        }else{
            assert(is_array($fields));
            if( isset($fields['count']) ){
                $groupby = $fields['count'];
                $fields = 'count(*) as count';
                assert(is_array($groupby));
                $query->from(array('p'=>'POAMS'), $fields )
                     ->where("p.poam_action_owner IN ($sid_str)")
                     ->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array())
                     ->joinLeft(array('fs'=>'FINDING_SOURCES'),'f.source_id = fs.source_id', array() )
                     ->join(array('s'=>'SYSTEMS'),'s.system_id = p.poam_action_owner',array());
                foreach($groupby as $g){
                    $query->group("p.$g");
                }
            }
        }


        
        if(isset($ids) && !empty($ids)){
            $query->where("p.poam_id IN ($ids)");
        }

        if(isset($source) && $source != 'any'){
            $query->where("fs.source_id = ".$source."");
        }

        if(!empty($startdate)){
            $startdate = date("Y-m-d",strtotime($startdate));
            $query->where("p.poam_action_date_est >=?",$startdate);
        }

        if(!empty($enddate)){
            $enddate = date("Y-m-d",strtotime($enddate));
            $query->where("p.poam_action_date_est <=?",$enddate);
        }

        if(!empty($startcreatedate) ){
            $start_date_cr = date("Y-m-d",strtotime($startcreatedate));
            $query->where("p.poam_date_created >=?",$startcreatedate); 
        }

        if(!empty($end_date_cr) ){
            $end_date_cr = date("Y-m-d",strtotime($end_date_cr));
            $query->where("p.poam_date_created <=?",$end_date_cr);
        }

        if(isset($type) && $type != 'any'){
            if(is_array($type)){
                $type = implode(',',$type);
                $query->where("p.poam_type IN ($type)");
            }
            else {
                $query->where("p.poam_type = ?",$type);
            }
        }

        if(isset($status) && $status != 'any' ){
            if(is_array($status)){
                $status = implode(',',$status);
                $query->where("p.poam_status IN ($status)");
            }
            else {
                $query->where("p.poam_status = ?", $status);
            }
        }
        
        if(isset($ep)){
            $qry = $db->select()->distinct()->from(array('pe'=>'POAM_EVIDENCE'),array('poam_id'=>'pe.poam_id'))
                                            ->where("pe.ev_sso_evaluation = '".$ep['sso']."'")
                                            ->where("pe.ev_fsa_evaluation = '".$ep['fsa']."'")
                                            ->where("pe.ev_ivv_evaluation = '".$ep['ivv']."'");
            
            $ids = implode(',',$db->fetchCol($qry));
            $query->where("p.poam_id IN ($ids)");
        }

        if(isset($asset_owner) && $asset_owner != 'any'){
            $query->where("s.system_id = ".$asset_owner."");
        }
        
        if(isset($action_owner) && $action_owner != 'any'){
            $query->where("s.system_id = ".$action_owner."");
        }

        if(!empty($poam_date_modified)){
            $query->where("p.poam_date_modified < $poam_date_modified");
        }

        if( !empty( $currentPage ) && !empty( $perPage ) ){
            $query->limitPage($currentPage,$perPage);
        }
        //var_dump($db->fetchAll($query));die();
        //Should be order by ?
        //$query->order('action_owner_name ASC');
        return $db->fetchAll($query);
    }

}
?>
