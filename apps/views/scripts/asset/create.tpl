<script LANGUAGE="JavaScript" type="test/javascript" src="/javascripts/ajax.js"></script>
<div class="barleft">
<div class="barright">
<p><b>Asset Creation</b><span><?PHP echo $this->escape($this->Current_time);?></span></p>
</div>
</div>
<form name="assetcreate" method="post" action="">
<table width="810" border="0" align="center">
<tr>
    <td><input type="hidden" name="prod_id" />
	    <input name="input" type="submit" value="Create Asset"/>
        <input type="reset" name="button" id="button" value="Reset" />
        <?php echo $this->result;?></td>
</tr>
	<tr>
    	<td>
            <table border="0" width="800" cellpadding="5" class="tipframe">
			    <tr><th colspan="2" align="left"> General Information</th>
                <tr>
					<td valign="center" ><b>Asset Name </b></td>
					<td valign="center" >
                        <input name="assetname" type="text" id="assetname" value="<?php echo $this->param['assetname'] ?>" size="23" maxlength="23">                    </td>
				</tr>
				<tr>
					<td valign="center" ><b>System:</b></td>
					<td valign="center" >
                        <?php echo $this->formSelect('system_list', 'select', null, $this->system_list); ?></td>
				</tr>
				<tr>
					<td valign="center" ><b>Network:</b></td>
					<td valign="center" ><?php echo $this->formSelect('network_list', 'select', null, $this->network_list); ?></td>
				</tr>
				<tr>
					<td valign="center" ><b>IP Address:</b></td>
					<td valign="center" >
                        <input type="text" name="ip" value="<?php echo $this->param['ip'] ?>" maxlength="23" size="23">
						<input type="radio" name="addrtype" value="1" onClick="javascript:changeAddrType(this);" {$chked1}> IPV4
						<input type="radio" name="addrtype" value="2" onClick="javascript:changeAddrType(this);" {$chked2}> IPV6 </td>
				</tr>
				<tr>
					<td valign="center" ><b>Port:</b></td>
					<td valign="center" ><input type="text" name="port" value="<?php echo $this->param['port'] ?>" maxlength="5" size="5"></td>
				</tr>
			</table>
        </td>
	</tr>
</table>
</form>
