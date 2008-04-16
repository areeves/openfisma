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

}

?>
