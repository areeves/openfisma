<div class="barleft">
<div class="barright">
<p><b>Report: Generate RAFs per system</b>
</div>
</div>
<form action="/zfentry.php/report/rafs" target="_rafs" method="post">

<table border="0" cellpadding="5" cellspacing="0" class="tipframe">
<tr>
    <td><b>System:</b></td>
    <td>
    <?php echo $this->formSelect('system_id',
                                 0,
                                 null,
                                 $this->system_list);?>

    <input type="submit">
    </td>
</tr>
<tr>
    <td colspan="2"> Please select a system to generate risk analysis forms.  </td>
</tr>
</table>
</form>
