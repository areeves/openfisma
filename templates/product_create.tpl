<table>

	<form name='product_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>NVD Define</td> <td>  <input type='text' name='prod_nvd_defined' value='0'>      </td></tr>
	<tr><td>Meta</td><td> <textarea name='prod_meta' rows="5" cols="40"></textarea>     </td></tr>
	<tr><td>Vendor</td> <td> <input type='text' name='prod_vendor' value=''>       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Name</td> <td> <input type='text' name='prod_name' value=''> </td></tr>
	<tr><td>Version</td>  <td> <input type='text' name='prod_version' value=''> </td></tr>
	<tr><td>Desc</td>  <td> <textarea name='prod_desc' rows="5" cols="40"></textarea>    </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='product'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>