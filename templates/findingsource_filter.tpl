{****************************************************************************}
{* We need to open up the form tag for in one of two instances:             *}
{*                                                                          *}
{* 1) if the filter is running as standalone (no pager)                     *}
{* 2) if the filter and pager are not running standalone                    *}
{*                                                                          *}
{* In the event of #2, it is up to the filter table to open up the form tag *}
{* that encompasses the filter, the pager, and the column headers that have *}
{* the sorting information                                                  *}
{****************************************************************************}

{if $filter_standalone == '1' || ($filter_standalone == '0' && $pager_standalone == '0')}
<form action='{$this_page}' method='POST'>
{/if}


{****************************************************************************}
{* The filter area has its own table (whereas the pager and list share a    *}
{* table for visual purposes.                                               *}
{****************************************************************************}

<table border='0'>

  <tr><th colspan='3' align='left'>Filters</th></tr>
  <tr><td colspan='3'><hr></td></tr>

  
  <tr>

    <td>source name</td>
	<td>{html_options name='source_name_bool' values=$bool_values  output=$bool_options selected=$source_name_bool }</td>
	<td>{html_options name='source_name'      values=$source_names output=$source_names selected=$source_name }</td>
		
  </tr>


  <tr>

    <td>source nickname</td>
	<td>{html_options name='source_nickname_bool' values=$bool_values  output=$bool_options selected=$source_nickname_bool }</td>
	<td>{html_options name='source_nickname'      values=$source_nicknames output=$source_nicknames selected=$source_nickname }</td>
		
  </tr>


  <tr>

    <td>source desc</td>
	<td>{html_options name='source_desc_bool' values=$bool_values  output=$bool_options selected=$source_desc_bool }</td>
	<td>{html_options name='source_desc'      values=$source_descs output=$source_descs selected=$source_desc }</td>
		
  </tr>


  <tr><td colspan='3'><hr></td></tr>
  <tr>
	<td align='left' colspan='3'>
      <input type='hidden' name='form_target' value='findingsource'>
	  <input type='image' name='form_action' value='filter' src='images/button_filter.png'>
      <input type='image' name='form_action' value='reset'  src='images/button_reset.png'>
	</td>
  </tr>  

</table>


{****************************************************************************}
{* We need to close the form tag if the filter is standalone - this means   *}
{* that there is no filter, nor are there sort options on the column header *}
{* in the list.                                                             *}
{****************************************************************************}

{if $filter_standalone == '1'}
</form>
{/if}

<br>