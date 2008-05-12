<?php
/**
 * OpenFISMA
 *
 * MIT LICENSE
 *
 * @version $Id$
 */

define('SCHEMA_DATABASE','schema.sql');

class TestController extends Zend_Controller_Action
{
    public function sqlAction(){

        $db = Zend_Registry::get('db');
        $user_name=$this->_getParam('user','root');
        $this->view->user_name=$user_name;

        $qry="select user_id,user_name from USERS where user_name = '$user_name'";
        $this->view->user_name_qry=$qry;
        $user=$db->query($qry)->fetch();
        $user_id=$user['user_id'];
        $this->view->user_id=$user_id;
        $qry="select system_id from USER_SYSTEM_ROLES where user_id = '$user_id'";

        if($user_id=='17'){
        $qry="select distinct system_id from USER_SYSTEM_ROLES";
        }
        $this->view->system_id_qry=$qry;

        $sys=$db->query($qry)->fetchAll();
        foreach ($sys as $res){
           $sysids[]=$res['system_id'];
        }
        $system_id_list=implode(',',$sysids);
        $this->view->system_id_list=$system_id_list;

        $qry="select count(*) from POAMS where poam_status ='OPEN' and poam_action_owner IN ($system_id_list)";
        $this->view->count_open_qry=$qry;
        $poam_open=$db->query($qry)->fetch();
        $this->view->count_open=$poam_open['count(*)'];

        $qry="select count(*) from POAMS where poam_status ='EN' and poam_action_date_est > NOW() and poam_action_owner IN ($system_id_list)";
        $this->view->count_en_qry=$qry;
        $poam_en=$db->query($qry)->fetch();
        $this->view->count_en=$poam_en['count(*)'];

        $qry="select count(*) from POAMS where poam_status ='EN' and poam_action_date_est <= NOW() and poam_action_owner IN ($system_id_list)";
        $this->view->count_eo_qry=$qry;
        $poam_eo=$db->query($qry)->fetch();
        $this->view->count_eo=$poam_eo['count(*)'];

        $this->render();
    }

    public function dbrecoverAction(){
        $ds=Zend_Registry::get('datasource');
        $user=$ds->default->params->username;
        $passwd=$ds->default->params->password;
        $host=$ds->default->params->host;
        $database=$ds->default->params->dbname;
        $sqlpath=dirname(dirname(dirname(__FILE__))).'/test/20080407_r210';
        if(!is_dir($sqlpath)) die("'$sqlpath' is not exist.");
        if(!mysql_connect($host,$user,$passwd)) die("Could   not   connect   to   mysql");
        $dbconn = mysql_select_db($database);
        if(!$dbconn) die("database connect error!");
        $files=scandir($sqlpath);
        if(!$files) die("files error!");
        $sql_file = preg_grep("/[a-zA-Z0-9]*\.sql$/i",$files);
        echo "<b>Now start importing <<< </b><br/>";
        foreach($sql_file as $elem){
        if($elem==SCHEMA_DATABASE) continue;
        $exec="mysql -h $host -u $user -p$passwd $database < $sqlpath/$elem";
        echo $exec ;
        passthru($exec, $ret);
        if($ret != 0) {
            echo " : Failed\n<br/>";
        }else{
            echo " : OK \n<br/>";
        }
        }
        echo "<b>Test database loading finished.</b>";
    }
}
?>
