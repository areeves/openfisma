<table>

	<form name='product_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='prod_id' value='{$prod_id}'>

	<tr><td>NVD Define</td>    <td> {$prod_nvd_defined}      </td></tr>
	<tr><td>Meta</td><td> {$prod_meta}     </td></tr>
	<tr><td>Vendor</td>     <td> {$prod_vendor}       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Name</td>         <td> {$prod_name}           </td></tr>
	<tr><td>Version</td>  <td> {$prod_version}    </td></tr>
	<tr><td>Desc</td>  <td> {$prod_desc}    </td></tr>
		
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='product'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>