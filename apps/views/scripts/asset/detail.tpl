<table border="0" width="100%" cellpadding="3" cellspacing="1">
<tr>
    <td>
    <table border="0"cellpadding="3" cellspacing="1">
    <tr>
        <td>&nbsp;</td>
        <td align="right"><b>System:</b></td>
        <td colspan="7">
<?PHP foreach($this->system as $result){
      echo $result['sname'];
}
?>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="right"><b>IP Address:</b></td>
        <td colspan="7">
<?PHP echo $this->ip;?>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="right"><b>Product:</b></td>
        <td><?php foreach($this->assets as $result){
            echo $result['pname'];
        }?></td>
        <td>&nbsp;</td>
        <td align="right"><b>Vendor:</b></td>
        <td><?php foreach($this->assets as $result){
            echo $result['pvendor'];
        }?></td>
        <td>&nbsp;</td>
        <td align="right"><b>Version:</b></td>
        <td><?php foreach($this->assets as $result){
            echo $result['pversion'];
        }?></td>
    </tr>
    </table>
    </td>
</tr>
</table>
