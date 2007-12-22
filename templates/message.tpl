<table border='1'>


    <tr><th>{$message_title}</th></tr>

    <tr>
        <td>
        
            <ul> {foreach from=$messages item=message} <li>{$message}</li> {/foreach} </ul>
            
        </td>
    </tr>
        
</table>
