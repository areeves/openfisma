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

{if $filter_form_start == '1'}
<form action='{$this_page}' method='POST'>
{/if}

{****************************************************************************}
{* The filter area has its own table (whereas the pager and list share a    *}
{* table for visual purposes.                                               *}
{****************************************************************************}

<table border='0'>

    <tr><th colspan='3' align='left'>Filters</th></tr>
    <tr><td colspan='3'><hr></td></tr>

    {foreach from=$filter_list item=filter}
    <tr>

        <td>{$filter.title}</td>
        
        {if $filter.show_bool == '1'}
        <td>{html_options name=$filter.bool_name options=$bool_array     selected=$filter.bool_selected}</td>
        {/if}
        <td>{html_options name=$filter.prefix    options=$filter.options selected=$filter.selected}</td>

    </tr>    
    {/foreach}


    <tr><td colspan='3'><hr></td></tr>
    <tr>
        <td align='left' colspan='3'>
            <input type='hidden' name='form_target' value='{$this_page}'>
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

{if $filter_form_close == '1'}
</form>
{/if}

<br>