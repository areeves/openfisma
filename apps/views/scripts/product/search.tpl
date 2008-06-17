<div><form id="form1" name="form1" method="post" action="" >
<table width="810" border="0" align="center" cellpadding="5" class="tipframe">
    <tr>
        <th colspan="2" align="left"> Product Search</th>
        <th align="left">Product Select</th>
    	<th align="left"><a href="#">Create Product</a></th>
    </tr>
    <tr>
        <td><b> Product:</b></td>
        <td><input type="text" class="product" name="prod_name" value="<?php echo $this->prod_name?>" size="20" /></td>
        <td colspan="2" rowspan="4">
            <div><select name="prod_list" size="8" style="width: 400px;">
                 <?php  foreach( $this->prod_list as $key ) {
                  echo'<option value='.$key['id'].'>'.$key['id'].' | '.$key['name'].' | '.$key['vendor'].' | '.$key['version'].'</option>';
                 } ?>
                 </select>
            </div>        </td>
    </tr>
    <tr>
        <td><b>Vendor:</b></td>
        <td><input type="text" class="product" name="prod_vendor" value="<?php echo $this->prod_vendor?>" size="20" /></td>
    </tr>
    <tr>
        <td><b>Version:</b></td>
        <td><input type="text" class="product" name="prod_version" value="<?php echo $this->prod_version?>" size="20" /></td>
    </tr>
    <tr>
        <td><input id="search_product" type="button" value="Search Product" url='/zfentry.php/product/search' /></td>
        <td><input type="reset" name="button2" id="button" value="Reset" /></td>
    </tr>
</table>
</form></div>
