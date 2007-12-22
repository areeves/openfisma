<form action='{$this_page}' method='POST'>
<input type='hidden' name='id'       value='{$id}'>
<input type='hidden' name='referrer' value='{$this_page}'>

<table>

  <tr>

    <td><input type='submit' name='form_target' value='list'></td>
    <td>|</td>
    <td><input type='submit' name='form_target' value='finding'></td>
    <td><input type='submit' name='form_target' value='remediation'></td>
    <td><input type='submit' name='form_target' value='evidence'></td>
    <td><input type='submit' name='form_target' value='comments'></td>
    <td>|</td>
	<td><input type='submit' name='form_target' value='raf'></td>
    <td>|</td>
    <td><input type='submit' name='form_target' value='system'></td>
    <td><input type='submit' name='form_target' value='asset'></td>
    <td><input type='submit' name='form_target' value='blscr'></td>

  </tr>

  <tr><td colspan='20'><hr></td></tr>

</table>

</form>