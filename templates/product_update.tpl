<table>

	<form name='product_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='prod_id' value='{$prod_id}'>
	<tr><td>NVD Define</td> <td>  <input type='text' name='prod_nvd_defined' value='{$prod_nvd_defined}'>      </td></tr>
	<tr><td>Meta</td><td> <textarea name='prod_meta' rows="5" cols="40">{$prod_meta}</textarea>     </td></tr>
	<tr><td>Vendor</td> <td> <input type='text' name='prod_vendor' value='{$prod_vendor}'>       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Name</td> <td> <input type='text' name='prod_name' value='{$prod_name}'> </td></tr>
	<tr><td>Version</td>  <td> <input type='text' name='prod_version' value='{$prod_version}'> </td></tr>
	<tr><td>Desc</td>  <td> <textarea name='prod_desc' rows="5" cols="40">{$prod_desc}</textarea>    </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='product'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>