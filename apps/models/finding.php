<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class Finding extends Zend_Db_Table
{
    protected $_name = 'FINDINGS';
    protected $_primary = 'finding_id';
    
    /**
        count the summary of findings according to certain criteria

        @param $date_range discovery time range
        @param $systems system id those findings belongs to
        @return array of counts
    */
    public function getCount($systems, $date_range=array(), $status=null ) 
    {
        assert(!empty($systems) && is_array($systems) );
        $qry = $this->_db->select()
                ->from(array('f'=>$this->_name), 
                       array('count' => "count(*)",'status'=>'f.finding_status'))
                ->join(array('as'=>'ASSETS'),'f.asset_id=as.asset_id',array())
                ->join(array('sys'=>'SYSTEM_ASSETS'), 
                        'as.asset_id = sys.asset_id 
                         AND sys.system_id IN ('.implode(',',$systems).')',
                       array('sysid'=>'sys.system_id'))
                ->group('sysid')->group('status');
        if( isset($status) ) {
            if( is_string($status) ) {
                $status = array($status);
            }
            foreach( $status as $s ) {
                $expr[] = "f.finding_status = '$s'";
            }
            $qry->where( implode(" OR ", $expr) ); 
        }
        // range follows [from, to)
        if( !empty($date_range['from']) ){
            $qry->where("finding_date_created >= '{$date_range['from']}'");
        }
        if( !empty($date_range['to']) ){
            $qry->where("finding_date_created < '{$date_range['to']}'");
        }
        return $this->_db->fetchAll($qry);
    }

    /**
        Get the generate the summary list used on the finding page
        @param $id the user id
        @return array of summary_data
    */
    public function getSummaryList($uid) {
        $db = $this->_db;
        $data = array();
        $sql = "SELECT s.system_name AS sname, f.finding_status AS status, COUNT(f.finding_id) AS num
                FROM `FINDINGS` AS f, `SYSTEM_ASSETS` AS a, `SYSTEMS` AS s, `USER_SYSTEM_ROLES` AS u
                WHERE f.asset_id=a.asset_id
                AND s.system_id=a.system_id
                AND u.user_id=$uid
                AND u.system_id=a.system_id
                AND a.asset_id=f.asset_id
                GROUP BY s.system_id, f.finding_status
                ORDER BY s.system_name";
        $result = $db->fetchAll($sql);
        if($result) {
            foreach($result as $row) {
                $data[$row['sname']]['system'] = $row['sname'];
                if(!isset($data[$row['sname']]['total'])) $data[$row['sname']]['total']=0;

                if ('REMEDIATION'==$row['status']) {
                    $data[$row['sname']]['reme'] = $row['num'];
                    $data[$row['sname']]['total'] += $row['num'];
                }
                if ('CLOSED'==$row['status']) {
                    $data[$row['sname']]['closed'] = $row['num'];
                    $data[$row['sname']]['total'] += $row['num'];
                }
                if ('OPEN'==$row['status']) { // open count number should be split to 30,60,90 etc counts
//                  $data[$row['sname']]['open'] = $row['num'];
                    $data[$row['sname']]['total'] += $row['num'];
                }
                $data[$row['sname']]['thirty'] = '';
                $data[$row['sname']]['sixty'] = '';
                $data[$row['sname']]['ninety'] = '';
            }
        }

        $sql = "SELECT s.system_name AS sname, COUNT(f.finding_id) AS num,
                DATE_FORMAT(f.finding_date_created, '%Y%m%d') AS date_num
                FROM `FINDINGS` AS f, `SYSTEM_ASSETS` AS a, `SYSTEMS` AS s, `USER_SYSTEM_ROLES` AS u
                WHERE f.asset_id=a.asset_id
                AND s.system_id=a.system_id
                AND f.finding_status='OPEN'
                AND u.user_id=$uid
                AND u.system_id=a.system_id
                AND a.asset_id=f.asset_id
                GROUP BY s.system_id, date_num";
        $result = $db->fetchAll($sql);
        $today = date('Ymd',mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
        $day30 = date('Ymd',mktime(0, 0, 0, date("m")  , date("d")-30, date("Y")));
        $day60 = date('Ymd',mktime(0, 0, 0, date("m")  , date("d")-60, date("Y")));
        $day90 = date('Ymd',mktime(0, 0, 0, date("m")  , date("d")-90, date("Y")));
        if($result) {
            foreach($result as $row) {
                $day = $row['date_num'];
                if ($today == $day) {
                    $data[$row['sname']]['open'] = $row['num'];
                }
                elseif (($day < $today) && ($day > $day30)) {
                    $data[$row['sname']]['thirty'] += $row['num'];
                }
                elseif (($day < $day30) && ($day > $day60)) {
                    $data[$row['sname']]['sixty'] += $row['num'];
                }
                else {
                    $data[$row['sname']]['ninety'] += $row['num'];
                }
            }
        }
        return array_values($data);
    }

    /**
    Get finding detail by finding_id
    @param $fid int
    @return $finding array
    */
    public function getFindingById($fid){
           $finding_detail = array();
           $qry = $this->select()->setIntegrityCheck(false)
                                    ->from(array('f'=>'FINDINGS'), array('id' => 'finding_id',
                                                 'status'=>'finding_status',
                                                  'source_id' => 'source_id',
                                                  'asset_id' =>'asset_id',
                                                  'discovered' => 'finding_date_discovered',
                                                  'created' =>'finding_date_created',
                                                  'closed' =>'finding_date_closed',
                                                  'finding_data' =>'finding_data',
                                                  'source_name' =>'fs.source_name'));
           $qry->join(array('fs' =>'FINDING_SOURCES'),'fs.source_id = f.source_id',array());
           $qry->join(array('as' =>'ASSETS'),'as.asset_id = f.asset_id',array());
           $qry->join(array('sa'=>'SYSTEM_ASSETS'),'sa.asset_id = as.asset_id',array('system_id'=>'system_id'));
           $qry->join(array('s'=>'SYSTEMS'),'s.system_id = sa.system_id',array('system_name'=>'system_name'));
           $qry->join(array('addr'=>'ASSET_ADDRESSES'),'as.asset_id = addr.asset_id',
                         array('ip'=>'addr.address_ip','port'=>'addr.address_port'));
           $qry->join(array('n'=>'NETWORKS'),'addr.network_id = n.network_id',array('network'=>'network_name'));
           $qry->where("f.finding_id = $fid");
           $data = $this->fetchRow($qry);
           $finding_detail = $data->toArray();
           $qry->join(array('p'=>'PRODUCTS'),'p.prod_id = as.prod_id',array('prod_name'=>'p.prod_name',
                                   'prod_vendor'=>'p.prod_vendor','prod_version'=>'p.prod_version'));
           $data = $this->fetchRow($qry);
           if(!empty($data)){
               $finding_detail = $data->toArray();
           }
           $qry->reset();
           $qry->from(array('fv'=>'FINDING_VULNS'),array());
           $qry->join(array('v'=>'VULNERABILITIES'),
                         'v.vuln_seq = fv.vuln_seq and v.vuln_type = fv.vuln_type',
                             array('vuln_seq'=>'v.vuln_seq',
                                   'vuln_type'=>'v.vuln_type',
                                   'vuln_desc_primary'=>'v.vuln_desc_primary',
                                   'vuln_desc_secondary'=>'v.vuln_desc_secondary'));
           $qry->where("fv.finding_id = $fid");
           $result = $this->fetchRow($qry);
           if(!empty($result)){
               $data = $result->toArray();
               $finding_detail['vuln_seq'] = $data['vuln_seq'];
               $finding_detail['vuln_type'] = $data['vuln_type'];
               $finding_detail['vuln_desc_primary'] = $data['vuln_desc_primary'];
               $finding_detail['vuln_desc_secondary'] = $data['vuln_desc_secondary'];
               if(!empty($finding_detail['vuln_seq'])){
                   $vseq = $finding_detail['vuln_seq'];
                   $vtype = $finding_detail['vuln_type'];
                   $qry->reset();
                   $qry->from(array('v'=>'VULNERABILITIES'));
                   $qry->where("vuln_seq = $vseq","vuln_type = $vtype");
                   $data = $this->fetchAll($qry);
                   $qry->reset();
                   if(!empty($data)){
                       $vuln_arr = $data->toArray();
                       $finding_detail['vuln_arr'] = $vuln_arr;
                   }
               }
           }
           return $finding_detail;
       }
}

?>
