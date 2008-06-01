<?php
    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }

    if (!defined('URL_BASE') ){
        define('URL_BASE', 'http://192.168.0.115/of/zfentry.php/');
    }

    if (!defined('ROOT')) {
        define('ROOT', dirname(dirname(__FILE__)));
    }
    
    require_once( ROOT . DS . 'paths.php');
    require_once( APPS . DS . 'basic.php');
    include_once( CONFIGS . DS . 'debug.php');
    import(LIBS, VENDORS, VENDORS.DS.'Pear');

    require_once 'Zend/Controller/Front.php';
    require_once 'Zend/Layout.php';
    require_once 'Zend/Registry.php';
    require_once 'Zend/Config.php';
    require_once 'Zend/Db.php';
    require_once 'Zend/Db/Table.php';
    require_once MODELS . DS . 'Abstract.php';
    require_once 'Zend/Controller/Plugin/ErrorHandler.php';
    require_once ( CONFIGS . DS . 'database.php');


    $table_name=  array( 'BLSCR', 'NETWORKS', 
              'PRODUCTS','FINDING_SOURCES' , 
              'SYSTEM_GROUP_SYSTEMS','SYSTEMS',
              'SYSTEM_GROUPS','FUNCTIONS','ROLES','ASSETS',
              'USER_ROLES','USERS','USER_SYSTEM_ROLES','FINDINGS','POAMS');

    $db_target = Zend_DB::factory(Zend_Registry::get('datasource')->default);
    $db_src    = Zend_DB::factory(Zend_Registry::get('legacy_datasource')->default);
    
    $delta = 100;
    echo "start to migrate \n";
    foreach( $table_name as $table ) 
    {
        echo "$table ";

        try{

        if($table == 'POAMS'){
            poam_conv($db_src, $db_target);
            continue;
        }
        $qry = $db_src->select()->from($table,'count(*)');
        //Get count
        $count = $db_src->fetchRow($qry);
        $count=$count['count(*)'] ;
        $qry = $db_src->select()->from($table)->limit(0,$delta);
        $rc = 0;
        for($i=0;$i<$count+$delta ; $i+=$delta )
        {
            $qry->reset(Zend_Db_Select::LIMIT_COUNT)
               ->reset(Zend_Db_Select::LIMIT_OFFSET);
            $qry->limit($delta,$i);
            $rows = $db_src->fetchAll($qry);
            $rc += count($rows);
            foreach($rows as &$data) {
                convert($db_src, $db_target, $table,$data);
            }
        }
        echo " ( $rc ) successfully\n";
        }catch(Zend_Exception $e){
            echo "skip \n\t", $e->getMessage() . "\n";
            continue;
        }
    }





function convert($db_src, $db_target, $table,&$data)
{
    switch($table)
    {
    case 'BLSCR':
         blscr_conv($db_src, $db_target, $data);
         break;

    case 'NETWORKS':
         networks_conv($db_src, $db_target, $data);
         break;

    case 'PRODUCTS':
         products_conv($db_src, $db_target, $data);
         break;

    case 'FINDING_SOURCES':
         sources_conv($db_src, $db_target, $data);
         break;

    case 'SYSTEM_GROUP_SYSTEMS':
         systemgroup_systems_conv($db_src, $db_target, $data);
         break;

    case 'SYSTEMS':
         system_conv($db_src, $db_target, $data);
         break;
   
    case 'SYSTEM_GROUPS':
         system_groups_conv($db_src, $db_target, $data);
         break;

    case 'FUNCTIONS':
         functions_conv($db_src, $db_target, $data);
         break;

    case 'ROLES':
         roles_conv($db_src, $db_target, $data);
         break;

    case 'ROLE_FUNCTIONS':
         role_functions_conv($db_src, $db_target, $data);
         break;

    case 'USER_ROLES':
         user_roles_conv($db_src, $db_target, $data);
         break;

    case 'USERS':
         users_conv($db_src, $db_target, $data);
         break;

    case 'USER_SYSTEM_ROLES':
         user_system_conv($db_src, $db_target, $data);
         break;
/////////////////////////////////////////////////////  
    case 'ASSETS':
         assets_conv($db_src, $db_target, $data);
         break;

    case 'FINDINGS':
         finding_conv($db_src, $db_target, $data);
         break;
    default:
            assert(false);
    }
}

function blscr_conv($db_src, $db_target, $data)
{
    if($data['blscr_low']==1&&$data['blscr_moderate']==1&&$data['blscr_high']==1 ){
        $level= 'low';
    } else if($data['blscr_low']==0&&$data['blscr_moderate']==1&&$data['blscr_high']==1 ) {
        $level='moderate';    
    }else if($data['blscr_low']==0&&$data['blscr_moderate']==0&&$data['blscr_high']==1 ) {
        $level='high';
    }else if($data['blscr_low']==0&&$data['blscr_moderate']==0&&$data['blscr_high']==0 ) {
        $level='none';
    }else {
        echo "{$data['blscr_id']} level error";
    }
         
    if( empty($data['blscr_enhancements']) || 
        $data['blscr_enhancements'] == '.' ) {
        $data['blscr_enhancements'] = 'N/A';
    }
    if( $data['blscr_supplement'] == '.' 
        || empty($data['blscr_supplement']) ) {
        $data['blscr_supplement'] = 'N/A';
    }
    $tmparray=array('code'=>$data['blscr_number'] ,
                    'class'=>$data['blscr_class']  ,
                    'subclass'=>$data['blscr_subclass']  ,
                    'family'=>$data['blscr_family']  ,
                    'control'=>$data['blscr_control']  , 
                    'guidance'=> $data['blscr_guidance'] , 
                    'control_level'=>$level,
                    'enhancements'=>$data['blscr_enhancements']  ,
                    'supplement'=> $data['blscr_supplement']);
    $db_target->insert('blscrs',$tmparray);
}


function networks_conv($db_src, $db_target, $data)
{
    $tmparray=array('id'=>$data['network_id'] ,
                  'name'=>$data['network_name']  ,
              'nickname'=>$data['network_nickname']  ,
                  'desc'=>$data['network_desc'] );
    $db_target->insert('networks',$tmparray);
}

function products_conv($db_src, $db_target, &$data)
{
    $tmparray=array('id'=>$data['prod_id'] ,
           'nvd_defined'=>$data['prod_nvd_defined']  ,
                  'meta'=>$data['prod_meta']  ,
                'vendor'=>$data['prod_vendor'],
                  'name'=>$data['prod_name'] ,
               'version'=>$data['prod_version'],
                  'desc'=>$data['prod_desc'] );
    $db_target->insert('products',$tmparray);
    unset($tmparray);
}

function sources_conv($db_src, $db_target, &$data)
{
    $tmparray=array('id'=>$data['source_id'] ,
                  'name'=>$data['source_name'],
              'nickname'=>$data['source_name'] ,
                  'desc'=>$data['source_desc'] );
    $db_target->insert('sources',$tmparray);
    unset($tmparray);
}

function systemgroup_systems_conv($db_src, $db_target, &$data)
{
    $tmparray=array('sysgroup_id'=>$data['sysgroup_id'] ,
                      'system_id'=>$data['system_id']);
    $db_target->insert('systemgroup_systems',$tmparray);
    unset($tmparray);
}

function system_conv($db_src, $db_target, $data)
{
    $tmparray=array('id'=>$data['system_id'] ,
                  'name'=>$data['system_name'],
              'nickname'=>$data['system_nickname'],
                  'desc'=>$data['system_desc'],
                  'type'=>$data['system_type'],
        'primary_office'=>$data['system_primary_office'],
       'confidentiality'=>$data['system_confidentiality'],
             'integrity'=>$data['system_integrity'],
          'availability'=>$data['system_availability'],
                  'tier'=>$data['system_tier'],
    'criticality_justification'=>$data['system_criticality_justification'],
    'sensitivity_justification'=>$data['system_sensitivity_justification'],
           'criticality'=>$data['system_criticality']);
     $db_target->insert('systems',$tmparray);
     unset($tmparray);
}

function system_groups_conv($db_src, $db_target, $data)
{
    $tmparray=array('id'=>$data['sysgroup_id'] ,
                  'name'=>$data['sysgroup_name'],
              'nickname'=>$data['sysgroup_nickname'],
           'is_identity'=>$data['sysgroup_is_identity']);
    $db_target->insert('system_groups',$tmparray);
    unset($tmparray);
}

function functions_conv($db_src, $db_target, $data)
{
    $tmparray=array('id'=>$data['function_id'] ,
                  'name'=>$data['function_name'],
                'screen'=>$data['function_screen'],
                'action'=>$data['function_action'],
                  'desc'=>$data['function_desc'],
                  'open'=>$data['function_open']);
    $db_target->insert('functions',$tmparray);
    unset($tmparray);
}

function roles_conv($db_src, $db_target, $data)
{
    $tmparray=array('id'=>$data['role_id'] ,
                  'name'=>$data['role_name'],
              'nickname'=>$data['role_nickname'],
                  'desc'=>$data['role_desc']);
    $db_target->insert('roles',$tmparray);
    unset($tmparray);
}

function role_functions_conv($db_src, $db_target, $data)
{
    $tmparray=array('role_id'=>$data['role_id'] ,
               'function_id'=>$data['function_id']);
    $db_target->insert('role_functions',$tmparray);
    unset($tmparray);
}


function user_roles_conv($db_src, $db_target, $data)
{
    $tmparray=array('user_id'=>$data['user_id'] ,
                    'role_id'=>$data['role_id']);
    $db_target->insert('user_roles',$tmparray);
    unset($tmparray);
}

function user_system_conv($db_src, $db_target, $data)
{
    $tmparray=array('user_id'=>$data['user_id'] ,
                    'system_id'=>$data['system_id']);
    $db_target->insert('user_systems',$tmparray);
    unset($tmparray);
}

function users_conv($db_src, $db_target, $data)
{
    $tmparray=array('id'=>$data['user_id'] ,
               'account'=>$data['user_name'],
              'password'=>$data['user_password'],
                 'title'=>$data['user_title'],
             'name_last'=>$data['user_name_last'],
           'name_middle'=>$data['user_name_middle'],
            'name_first'=>$data['user_name_first'],
            'created_ts'=>$data['user_date_created'],
           'password_ts'=>$data['user_date_password'],
      'history_password'=>$data['user_history_password'],
         'last_login_ts'=>$data['user_date_last_login'],
        'termination_ts'=>$data['user_date_deleted'],
             'is_active'=>$data['user_is_active'],
         'failure_count'=>$data['failure_count'],
          'phone_office'=>$data['user_phone_office'],
          'phone_mobile'=>$data['user_phone_mobile'],
                 'email'=>$data['user_email'],
             'auto_role'=>$data['extra_role']);
            $db_target->insert('users',$tmparray);
                unset($tmparray);
}

/////////////////////////////////////
function assets_conv($db_src, $db_target,$data)
{    
    $qry=$db_src->select()->from('SYSTEM_ASSETS' ,array('system_id'=>'system_id'))->where('asset_id=?',$data['asset_id']);
    $system_id=$db_src->fetchRow($qry);
    if(empty($system_id)) 
        $system_id=0;
    else
        $system_id=$system_id['system_id'];
    
    $qry=null;
    $qry=$db_src->select()->from('FINDINGS' ,array('finding_id'=>'finding_id'))->where('asset_id=?',$data['asset_id']);
    $finding_id=$db_src->fetchRow($qry);
    $finding_id=$finding_id['finding_id'];
    if(empty($finding_id))
        $is_virgin=1;
    else
        $is_virgin=0;        
     
    $qry=null;
    $qry=$db_src->select()->from('ASSET_ADDRESSES' ,
                        array('network_id'=>'network_id',
                              'address_ip'=>'address_ip',
                            'address_port'=>'address_port'))
                          ->where('asset_id=?',$data['asset_id']);
    $network=$db_src->fetchRow($qry);
    if(empty($network))
    {
        $network_id=0;
        $address_ip=0;
        $address_port=0;
    }else
    {
        $network_id=$network['network_id'];
        $address_ip=$network['address_ip'];
        $address_port=$network['address_port'];
    }
  //  echo $network['address_port'];

    $tmparray=array('id'=>$data['asset_id'] ,
               'prod_id'=>$data['prod_id'],
                  'name'=>$data['asset_name'],
             'create_ts'=>$data['asset_date_created'],
                'source'=>$data['asset_source'],
             'system_id'=>$system_id,
             'is_virgin'=>$is_virgin,
            'network_id'=>$network_id,
            'address_ip'=>$address_ip,
          'address_port'=>$address_port);
// var_dump($tmparray);
    $db_target->insert('assets',$tmparray);
    unset($tmparray);
}

function finding_conv($db_src, $db_target, $data)
{
    $qry = $db_src->select();
    $poam_data = $db_src->fetchAll($qry->from('POAMS')->where('finding_id=?',$data['finding_id']));
    $qry->reset();
    $asset_data = $db_src->fetchAll(
                  $qry->from(array('as'=>'ASSETS'))->where('as.asset_id=?',$data['asset_id'])
                      ->join(array('sys'=>'SYSTEM_ASSETS'),'sys.asset_id = as.asset_id') );
    if(empty($asset_data)) {
        echo "asset {$data['asset_id']} missing for finding[{$data['finding_id']}] \n";
        return;
    }else{
        if(empty($poam_data)){
            $tmp = array(
                         'legacy_finding_id'=> $data['finding_id'],
                         'asset_id'=>$data['asset_id'],
                         'source_id'=>$data['source_id'],
                         'system_id'=>$asset_data[0]['system_id'],
                         'create_ts'=>$data['finding_date_created'],
                         'discover_ts'=>$data['finding_date_discovered'],
                         'status'=>'NEW'
                         );
            echo "INSERT INTO poams (" . implode(',',array_keys($tmp)).") VALUES ('" .
                 implode("','",$tmp) . "');\n";
            //$db_target->insert('poams',$tmp);
            return;
        }else{
            $poam_data = $poam_data[0];
        }
        if($poam_data['poam_action_owner'] != $asset_data[0]['system_id']){
            echo "finding_id[{$data['finding_id']}] system_id {$asset_data[0]['system_id']} is inconsistent with action owner {$poam_data['poam_action_owner']} \n";
        }
    }

    if($data['finding_id'] != $poam_data['finding_id']){
        echo "{$data['finding_id']} is inconsistent between finding_id and poam.finding_id \n";
    }
    $tmp = array('id'=> $poam_data['poam_id'],
                 'legacy_finding_id'=> $data['finding_id'],
                 'asset_id'=>$data['asset_id'],
                 'source_id'=>$data['source_id'],
                 'system_id'=>$poam_data['poam_action_owner'],
                 'create_ts'=>$data['finding_date_created'],
                 'discover_ts'=>$data['finding_date_discovered'],
                 'modify_ts'=>$poam_data['poam_date_modified'],
                 'close_ts'=>$poam_data['poam_date_closed'],
                 'type'=>$poam_data['poam_type'],
                 'status'=>$poam_data['poam_status'],
                 'is_repeat'=>$poam_data['poam_is_repeat'],
                 'previous_audits'=>$poam_data['poam_previous_audits'],
                 'created_by'=>$poam_data['poam_created_by'],
                 'modified_by'=>$poam_data['poam_modified_by'],
                 'closed_by'=>$poam_data['poam_closed_by'],
                 'action_suggested'=>$poam_data['poam_action_suggested'],
                 'action_planned'=>$poam_data['poam_action_planned'],
                 'action_status'=>$poam_data['poam_action_status'],
                 'action_approved_by'=>$poam_data['poam_action_approved_by'],
                 'action_resources'=>$poam_data['poam_action_resources'],
                 'action_est_date'=>$poam_data['poam_action_date_est'],
                 'action_actual_date'=>$poam_data['poam_action_date_actual'],
                 'cmeasure'=>$poam_data['poam_cmeasure'],
                 'cmeasure_effectiveness'=>$poam_data['poam_cmeasure_effectiveness'],
                 'cmeasure_justification'=>$poam_data['poam_cmeasure_justification'],
                 'threat_source'=>$poam_data['poam_threat_source'],
                 'threat_level'=>$poam_data['poam_threat_level'],
                 'threat_justification'=>$poam_data['poam_threat_justification']);
     $db_target->insert('poams',$tmp);
}


function poam_conv( $db_src, $db_target)
{
    $qry = $db_src->select();
    $data = $db_src->fetchAll(
            "SELECT *
            FROM POAMS p
            WHERE NOT
            EXISTS (

            SELECT finding_id
            FROM FINDINGS f
            WHERE f.finding_id = p.finding_id
            )");
    foreach($data as $poam_data){

    $tmp = array('id'=> $poam_data['poam_id'],
                 'legacy_finding_id'=> 0,
                 'asset_id'=>0,
                 'source_id'=>0,
                 'system_id'=>$poam_data['poam_action_owner'],
                 'create_ts'=>$poam_data['poam_date_created'],
                 'modify_ts'=>$poam_data['poam_date_modified'],
                 'close_ts'=>$poam_data['poam_date_closed'],
                 'type'=>$poam_data['poam_type'],
                 'status'=>$poam_data['poam_status'],
                 'is_repeat'=>$poam_data['poam_is_repeat'],
                 'previous_audits'=>$poam_data['poam_previous_audits'],
                 'created_by'=>$poam_data['poam_created_by'],
                 'modified_by'=>$poam_data['poam_modified_by'],
                 'closed_by'=>$poam_data['poam_closed_by'],
                 'action_suggested'=>$poam_data['poam_action_suggested'],
                 'action_planned'=>$poam_data['poam_action_planned'],
                 'action_status'=>$poam_data['poam_action_status'],
                 'action_approved_by'=>$poam_data['poam_action_approved_by'],
                 'action_resources'=>$poam_data['poam_action_resources'],
                 'action_est_date'=>$poam_data['poam_action_date_est'],
                 'action_actual_date'=>$poam_data['poam_action_date_actual'],
                 'cmeasure'=>$poam_data['poam_cmeasure'],
                 'cmeasure_effectiveness'=>$poam_data['poam_cmeasure_effectiveness'],
                 'cmeasure_justification'=>$poam_data['poam_cmeasure_justification'],
                 'threat_source'=>$poam_data['poam_threat_source'],
                 'threat_level'=>$poam_data['poam_threat_level'],
                 'threat_justification'=>$poam_data['poam_threat_justification']);
     $db_target->insert('poams',$tmp);
    }
}


?>
