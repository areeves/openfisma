<?php 
/**
* Test function search in poam model
*/

class TestRemediationOfSearch extends UnitTestCase{
    function testSearch(){
        require_once MODELS . DS . 'system.php';
        require_once MODELS . DS . 'source.php';
        require_once MODELS . DS . 'network.php';
        require_once MODELS . DS . 'poam.php';
        //$db_initialize;
        $db = Zend_Registry::get('db');
        $this->db = $db;
        $system = new system();
        $source = new source();
        $network = new network();
        $net = $network->getList();
        $system_list = $system->getList();
        foreach($system_list as $id=>$row){
            $system_ids[] = $id;
        }
        $this->system_ids = $system_ids;
        
        $source_list = $source->getList();
        foreach($source_list as $id=>$row){
            $source_ids[] = $id;
        }

        $this->source_ids = $source_ids;
        
        /**
        * Test system nickname and count
        **/
        foreach($system_ids as $id){
            $this->__testCount($id);
        }
        
        /**
        * Test source count
        **/
        foreach($source_ids as $id){
            $this->__testCriteria($id,'source');
        }

        /**
        *Test type count
        **/
        $poam_types = array('CAP','AR','FP');
        foreach($poam_types as $type){
            $this->__testCriteria($type,'type');
        }

        /**
        * Test poam status count
        **/
        $poam_status = array('OPEN','EN','EP','ES','CLOSED');
        foreach($poam_status as $status){
            $this->__testCriteria($status,'status');
        }
        
        /**
        *Test colligate conditions search results count
        **/
        $this->__testCriteriaArray(array('asset_owner'=>29,'source'=>1,'type'=>'CAP','status'=>'OPEN',
                                         'startdate'=>'2007/01/01','enddate'=>'2008/06/01',
                                         'startcreatedate'=>'2006/01/01','end_date_cr'=>'2008/06/01'));
        
    }

    private function __testCount($id){
        $db = $this->db;
        $query = $db->select()->from(array('p'=>'POAMS'),array('num'=>'count(poam_id)'))
                              ->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array())
                              ->where("poam_action_owner = $id ");
        $result = $db->fetchRow($query);
        $count = $result['num'];

        $poam = new poam($db);
        $result = $poam->search(array($id),array('count'=>array()));
        foreach($result as $row){
            $this->assertTrue($count == $row['count']);
            if($count != $row['count']){
                echo 'faild id is'.$id.' ';
            }
        }
    }

    private function __testCriteria($value,$flag = null){
        $db = $this->db;
        $ids = implode(',',$this->system_ids);
        switch($flag){
            case 'source':
                $query = $db->select()->from(array('p'=>'POAMS'),array('num'=>'count(p.poam_id)'))
                                      ->where("f.source_id = $value ");
                break;
            case 'type':
                $query = $db->select()->from(array('p'=>'POAMS'),array('num'=>'count(p.poam_id)'))
                                      ->where("p.poam_type = '$value'");
                break;
            case 'status':
                $query = $db->select()->from(array('p'=>'POAMS'),array('num'=>'count(p.poam_id)'))
                                      ->where("p.poam_status = '$value'");
                break;
        }
        $query->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array())
              ->where("p.poam_action_owner IN ($ids)");
        $result = $db->fetchRow($query);
        $count = $result['num'];
        $poam = new poam($db);
        $result = $poam->search($this->system_ids,array('count'=>array()),array($flag=>$value));
        foreach($result as $row){
            $this->assertTrue($count == $row['count']);
            if($count != $row['count']){echo $count.' '.$row['count'].'@';
                echo 'faild id/value is '.$value;
            }
        }
    }

    private function __testCriteriaArray($criteria){
        $db = $this->db;
        $poam = new poam();
        extract($criteria);
        $startdate = date("Y-m-d",strtotime($startdate));
        $enddate   = date("Y-m-d",strtotime($enddate));
        $startcreatedate = date("Y-m-d",strtotime($startcreatedate));
        $end_date_cr = date("Y-m-d",strtotime($end_date_cr));
        $ids = implode(',',$this->system_ids);
        $query = $db->select()->from(array('p'=>'POAMS'),array('num'=>'count(p.poam_id)'))
                              ->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array())
                              ->where("p.poam_action_owner = $asset_owner")
                              ->where("f.source_id = $source")
                              ->where("p.poam_type = '$type'")
                              ->where("p.poam_status = '$status'")
                              ->where("p.poam_action_date_est >=?",$startdate)
                              ->where("p.poam_action_date_est <=?",$enddate)
                              ->where("p.poam_date_created >=?",$startcreatedate)
                              ->where("p.poam_date_created <=?",$end_date_cr);
        $result = $db->fetchRow($query);
        $count = $result['num'];
        $result = $poam->search($this->system_ids,array('count'=>array()),$criteria);
        foreach($result as $row){//echo $count.' '.$row['count'];
            $this->assertTrue($count == $row['count']);
        }
    }
}
