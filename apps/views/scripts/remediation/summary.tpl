<div class="barleft">
<div class="barright">
<p><b>Remediation Summary</b><span><?php echo date('Y-M-D h:i:s:A');?></span></p>
</div>
</div>
<table align="center" cellpadding="5" class="tbframe">
    <tr align="center">
        <th>Action Owner</th>
        <th>New</th>
        <th>Open</th>
        <th>EN</th>
        <th>EO</th>
        <!--th>EP</th-->
        <th>EP (SSO)</th>
        <th>EP (S&P)</th>
        <th>ES</th>
        <th>CLOSED</th>
        <th>Total</th>
    </tr>

    <!-- SUMMARY LOOP -->
    <?php foreach($this->summary as $system){ ?>
    <tr>
        <td width='45%' align='left'   class='tdc'>(<?php echo $system['action_owner_nickname'];?>)<?php echo $system['action_owner_name'];?></td>
        <td align='center' class='tdc'><?php echo $system['NEW'] == ''?'-':$system['NEW'];?></td>
        <td align='center' class='tdc'><?php echo $system['OPEN']== ''?'-':$system['OPEN'];?></td>
        <td align='center' class='tdc'><?php echo $system['EN']== ''?'-':$system['EN'];?></td>
        <td align='center' class='tdc'><?php echo $system['EO']== ''?'-':$system['EO'];?></td>
        <td align='center' class='tdc'><?php echo $system['EP_SSO']==''?'-':$system['EP_SSO'];?></td>
        <td align='center' class='tdc'><?php echo $system['EP_SNP']==''?'-':$system['EP_SNP'];?></td>
        <td align='center' class='tdc'><?php echo $system['ES']==''?'-':$system['ES'];?></td>
        <td align='center' class='tdc'><?php echo $system['CLOSED']==''?'-':$system['CLOSED'];?></td>       
        <td align='center' class='tdc'><b><?php echo $system['TOTAL']==''?'0':$system['TOTAL'];?></b></td>
    </tr>
    <?php }?>
    <tr>
        <td width='45%' align='center' class='tdc'><b>TOTALS</b></td>
        <td class='tdc'><b><?php echo $this->totals['NEW']==''?'0':$this->totals['NEW'];?></b></td>
        <td class='tdc'><b><?php echo $this->totals['OPEN']==''?'0':$this->totals['OPEN'];?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EN']==''?'0':$this->totals['EN'];?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EO']==''?'0':$this->totals['EO'];?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EP_SSO']==''?'0':$this->totals['EP_SSO'];?></b></td>
        <td class='tdc'><b><?php echo $this->totals['EP_SNP']==''?'0':$this->totals['EP_SNP'];?></b></td>
        <td class='tdc'><b><?php echo $this->totals['ES']==''?'0':$this->totals['ES'];?></b></td>
        <td class='tdc'><b><?php echo $this->totals['CLOSED']==''?'0':$this->totals['CLOSED'];?></b></td>     
        <td class='tdc'><b><?php echo $this->totals['TOTAL']==''?'0':$this->totals['TOTAL'];?></b></td>
    </tr>
</table>
<br>
