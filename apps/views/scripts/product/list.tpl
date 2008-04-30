<select name="prod_list" size="8" style="width: 400px;">
<?php  
foreach( $this->prod_list as $key ){
    echo'<option value='.$key['prod_id'].'>'.$key['prod_id'].' | '.$key['prod_name'].' | '.$key['prod_vendor'].' | '.$key['prod_version'].'</option>';
}
?>
