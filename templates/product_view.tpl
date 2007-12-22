<table>

	<tr><td>NVD Define</td>    <td> {$prod_nvd_defined}      </td></tr>
	<tr><td>Meta</td><td> {$prod_meta}     </td></tr>
	<tr><td>Vendor</td>     <td> {$prod_vendor}       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Name</td>         <td> {$prod_name}           </td></tr>
	<tr><td>Version</td>  <td> {$prod_version}    </td></tr>
	<tr><td>Desc</td>  <td> {$prod_desc}    </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='product_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='product'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='prod_id'     value='{$prod_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='product_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='product'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='prod_id'     value='{$prod_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='product_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='product'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='prod_id'     value='{$prod_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
