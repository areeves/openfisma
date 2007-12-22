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

    <td>function name</td>
	<td>{html_options name='function_name_bool' values=$bool_values  output=$bool_options selected=$selected_name_bool }</td>
	<td>{html_options name='function_name'      values=$unique_names output=$unique_names selected=$selected_name }</td>
		
  </tr>


  <tr>

    <td>function screen</td>
	<td>{html_options name='function_screen_bool' values=$bool_values  output=$bool_options selected=$selected_screen_bool }</td>
	<td>{html_options name='function_screen'      values=$unique_screens output=$unique_screens selected=$selected_screen }</td>
		
  </tr>


  <tr>

    <td>function action</td>
	<td>{html_options name='function_action_bool' values=$bool_values  output=$bool_options selected=$selected_action_bool }</td>
	<td>{html_options name='function_action'      values=$unique_actions output=$unique_actions selected=$selected_action }</td>
		
  </tr>


  <tr>

    <td>function open</td>
	<td>{html_options name='function_open_bool' values=$bool_values  output=$bool_options selected=$selected_open_bool }</td>
	<td>{html_options name='function_open'      values=$unique_opens output=$unique_opens selected=$selected_open }</td>
		
  </tr>


  <tr><td colspan='3'><hr></td></tr>
  <tr>
	<td align='left' colspan='3'>
      <input type='hidden' name='form_target' value='function'>
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