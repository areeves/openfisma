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

    <td>blscr class</td>
	<td>{html_options name='blscr_class_bool' values=$bool_values  output=$bool_options selected=$selected_class_bool }</td>
	<td>{html_options name='blscr_class'      values=$unique_classs output=$unique_classs selected=$selected_class }</td>
		
  </tr>


  <tr>

    <td>blscr subclass</td>
	<td>{html_options name='blscr_subclass_bool' values=$bool_values  output=$bool_options selected=$selected_subclass_bool }</td>
	<td>{html_options name='blscr_subclass'      values=$unique_subclasss output=$unique_subclasss selected=$selected_subclass }</td>
		
  </tr>


  <tr>

    <td>blscr family</td>
	<td>{html_options name='blscr_family_bool' values=$bool_values  output=$bool_options selected=$selected_family_bool }</td>
	<td>{html_options name='blscr_family'      values=$unique_families output=$unique_families selected=$selected_family }</td>
		
  </tr>


  <tr><td colspan='3'><hr></td></tr>
  <tr>
	<td align='left' colspan='3'>
      <input type='hidden' name='form_target' value='blscr'>
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